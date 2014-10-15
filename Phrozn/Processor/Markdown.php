<?php
/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *          http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Processor;
use Michelf\MarkdownExtra as MarkdownParser;
use Phrozn\Autoloader as Loader;

/**
 * Markdown markup processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class Markdown
    extends Base
    implements \Phrozn\Processor
{
    /**
     * List of permitted Markdown configuration variables
     *
     * Taken from https://michelf.ca/projects/php-markdown/configuration/
     * @var array
     */
    private static $markdownConfigVars=array(
        'empty_element_suffix',
        'tab_width',
        'no_markup',
        'no_entities',
        'predef_urls',
        'predef_titles',
        'fn_id_prefix',
        'fn_link_title',
        'fn_backlink_title',
        'fn_link_class',
        'fn_backlink_class',
        'code_class_prefix',
        'code_attr_on_pre',
        'predef_abbr',
        'code_class_prefix',
        'code_attr_on_pre'
    );

    /**
     * Reference to procesor class
     * @var MarkdownParser
     */
    protected $markdown;

    /**
     * Processor can be setup at initialization time
     *
     * @param array $options Processor options
     *
     * @return void
     */
    public function __construct($options = array())
    {
        $this->markdown = new MarkdownParser;
    }


    /**
    * Helper for setting markdown configuration values
    *
    * @param string $name name of config variable
    * @param string $value value of config variable
    * @return void
    */
    protected function setMarkdownConfig($name, $value)
    {
        $this->markdown->$name = $value;
    }

    /**
    * Configure Markdown with options from the page and config.yml
    *
    * You can add markdown: entry to your config.yml or page front
    * matter which contains values for any of the Markdown Extra
    * configuration options.
    *
    * See https://michelf.ca/projects/php-markdown/configuration/ for
    * a complete list of options. An example to support marking up
    * code blocks for the Google Prettify script would be this:
    *
    * <code>
    * markdown:
    *     code_class_prefix: "prettyprint "
    *     code_attr_on_pre: true
    * </code>
    *
    * The space in the prefix is important in the case of prettyprint,
    * as you can specify extra styles on markdown fenced code blocks,
    * allowing you to specify the language, for example:
    *
    * <code>
    * ~~~~~~~~~ .lang-js
    * {"foo":"bar"}
    * ~~~~~~~~~
    * </code>
    *
    * @param array $vars List of variables passed to template engine
    */
    protected function configureMarkdown($vars)
    {

        foreach (self::$markdownConfigVars as $name) {
            //if variable set in front matter, use that, but if not present
            //use site-wide configuration from config.yml
            if (isset($vars['page']['markdown'][$name])) {
                $this->setMarkdownConfig($name, $vars['page']['markdown'][$name]);
            } elseif (isset($vars['site']['markdown'][$name])) {
                $this->setMarkdownConfig($name, $vars['site']['markdown'][$name]);
            }
        }
    }
    /**
     * Parse the incoming template
     *
     * @param string $tpl Source template content
     * @param array $vars List of variables passed to template engine
     *
     * @return string Processed template
     */
    public function render($tpl, $vars = array())
    {
        $this->configureMarkdown($vars);
        return $this->markdown->transform($tpl);
    }
}

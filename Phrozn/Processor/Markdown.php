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
     * Parse the incoming template
     *
     * @param string $tpl Source template content
     * @param array $vars List of variables passed to template engine
     *
     * @return string Processed template
     */
    public function render($tpl, $vars = array())
    {
        return $this->markdown->transform($tpl);
    }
}

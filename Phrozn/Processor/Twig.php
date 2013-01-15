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
use Phrozn\Autoloader as Loader,
    Phrozn\Path\Project as ProjectPath;

/**
 * Twig templates processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class Twig
    extends Base
    implements \Phrozn\Processor
{
    /**
     * Reference to twig engine environment object
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Reference to twig current template loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     * If configuration options are passed then twig environment
     * is initialized right away
     *
     * @param array $options Processor options
     *
     * @return \Phrozn\Processor\Twig
     */
    public function __construct($options = array())
    {
        $path = Loader::getInstance()->getPath('library');

        if (count($options)) {
            $this->setConfig($options)
                 ->getEnvironment();
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
        return $this->getEnvironment()
                    ->loadTemplate($tpl)
                    ->render($vars);
    }

    /**
     * Get (init if necessary) twig environment
     *
     * @param boolean $reset Force re-initialization (helpful for UTs)
     *
     * @return \Twig_Environment
     */
    protected function getEnvironment($reset = false)
    {
        if ($reset === true || null === $this->twig) {
            $this->twig = new \Twig_Environment(
                $this->getLoader(), $this->getConfig());
            $this->twig->removeExtension('escaper');
        }

        return $this->twig;
    }

    /**
     * Get template loader chain
     *
     * @return \Twig_LoaderInterface
     */
    protected function getLoader()
    {
        $config = $this->getConfig();
        $chain = new \Twig_Loader_Chain();

        // use template's own directory to search for templates
        $paths = array($config['phr_template_dir']);

        // inject common paths
        $projectPath = new ProjectPath($config['phr_template_dir']);
        if ($projectPath = $projectPath->get()) {
            $paths[] = $projectPath . DIRECTORY_SEPARATOR . 'layouts';
            $paths[] = $projectPath;
        }
        $chain->addLoader(new \Twig_Loader_Filesystem($paths));

        // add string template loader, which is responsible for loading templates
        // and removing front-matter
        $chain->addLoader(new \Phrozn\Twig\Loader\String);

        return $chain;
    }

}

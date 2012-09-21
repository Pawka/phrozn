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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\View;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Path\Project as ProjectPath,
    Phrozn\Site\View\Factory as ViewFactory,
    Phrozn\Provider\Factory as ProviderFactory,
    Phrozn\Site\Layout\DefaultLayout as Layout,
    Phrozn\Site\View\OutputPath\Entry as OutputFile,
    Phrozn\Autoloader as Loader;

/**
 * Base implementation of Phrozn view
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
abstract class Base
    implements \Phrozn\Site\View
{
    /**
     * Input file path
     * @var string
     */
    private $inputFile;

    /**
     * Output directory path
     * @var string
     */
    private $outputDir;

    /**
     * Output file path
     * @var string
     */
    private $outputFile;

    /**
     * Registered text processors
     * @var array of \Phrozn\Processor
     */
    private $processors;

    /**
     * Template source text
     * @var string
     */
    private $source;

    /**
     * Whether given concrete view must be wrapped into layout or not
     */
    private $hasLayout = true;

    /**
     * Cache object for extracted template
     * @var string
     */
    private $template;

    /**
     * Cache object for extracted FM
     * @var array
     */
    private $frontMatter;

    /**
     * Loaded content of site/config.yml
     * @var array
     */
    private $siteConfig;

    /**
     * Loaded content of configs/phrozn.yml
     * @var array
     */
    private $appConfig;

    /**
     * Initialize page
     *
     * @param string $inputFile Path to page source file
     * @param string $outputDir File destination path
     *
     * @return \Phrozn\Site\View
     */
    public function __construct($inputFile = null, $outputDir = null)
    {
        $this
            ->setInputFile($inputFile)
            ->setOutputDir($outputDir);
    }

    /**
     * Create static version of a concrete page
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return \Phrozn\Site\View
     */
    public function compile($vars = array())
    {
        $out = $this->render($vars);

        $destinationDir = dirname($this->getOutputFile());
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $outputFile = $this->getOutputFile();
        if (!is_dir($outputFile)) {
            file_put_contents($outputFile, $out);
        } else {
            throw new \Exception(sprintf(
                'Output path "%s" is directory.', $outputFile));
        }

        return $out;
    }

    /**
     * Render view
     *
     * @param array $vars List of variables passed to text processors
     *
     * @return string
     */
    public function render($vars = array())
    {
        // inject front matter options into template
        $vars = array_merge($vars, $this->getParams());

        // inject providers content
        if ($providers = $this->getParam('page.providers', false)) {
            $factory = new ProviderFactory();
            $projectPath = new ProjectPath($this->getOutputDir());
            foreach ($providers as $varname => $data) {
                if (!isset($data['provider'])) {
                    continue;
                }
                $provider = $factory->create($data['provider'], $data);
                $provider->setProjectPath($projectPath->get());
                $providedContent = $provider->get();
                $vars['page']['providers'][$varname] = $providedContent;
                $vars['this']['providers'][$varname] = $providedContent;
            }

        }

        // convert view into static representation
        $view = $this->getTemplate();
        foreach ($this->getProcessors() as $processor) {
            $view = $processor->render($view, $vars);
        }

        return $view;
    }

    /**
     * Set input file path
     *
     * @param string $file Path to file
     *
     * @return \Phrozn\Site\View
     */
    public function setInputFile($path)
    {
        $this->inputFile = $path;
        return $this;
    }

    /**
     * Get input file path
     *
     * @return string
     */
    public function getInputFile()
    {
        return $this->inputFile;
    }

    /**
     * Set output file path
     *
     * @param string $path File path
     *
     * @return \Phrozn\Site\View
     */
    public function setOutputFile($path)
    {
        $this->outputFile = $path;
        return $this;
    }

    /**
     * Get output file path
     *
     * @return string
     */
    public function getOutputFile()
    {
        $path = new OutputFile($this);
        return $path->get();
    }

    /**
     * Set output directory path
     *
     * @param string $path Directory path
     *
     * @return \Phrozn\Site\View
     */
    public function setOutputDir($path)
    {
        $this->outputDir = $path;
        return $this;
    }

    /**
     * Get output directory path
     *
     * @return string
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * Add text processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Site\View
     */
    public function addProcessor($processor)
    {
        $this->processors[get_class($processor)] = $processor;
        return $this;
    }

    /**
     * Remove text processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Site\View
     */
    public function removeProcessor($processor)
    {
        unset($this->processors[get_class($processor)]);
        return $this;
    }

    /**
     * Get list of registered processors
     *
     * @return array of \Phrozn\Processor items
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Set param
     *
     * @param string $param Name of the parameter to set
     * @param mixed $value Value of the parameter
     *
     * @return \Phrozn\Has\Param
     */
    public function setParam($param, $value)
    {
        $this->parse();
        $this->frontMatter[$param] = $value;
        return $this;
    }

    /**
     * Get param value
     *
     * @param string $param Parameter name to obtain value for
     * @param mixed $default Default parameter value, if non found in FM
     *
     * @return mixed
     */
    public function getParam($param, $default = null)
    {
        try {
            $this->parse();
            $value = $this->getParams($param, null);
        } catch (\Exception $e) {
            // skip error on file read problems, just return default value
        }
        if (isset($value)) {
            return $value;
        } else {
            return $default;
        }
    }

    /**
     * Get view parameters from both front matter and general site options
     *
     * @param string $param Parameter to get value for. Levels are separated with dots
     * @param string $default Default value to fetch if param is not found
     *
     * @return array
     */
    public function getParams($param = null, $default = array())
    {
        $params['page'] = $this->getFrontMatter();
        $params['site'] = $this->getSiteConfig();
        $params['phr'] = $this->getAppConfig();
        // also create merged configuration
        if (isset($params['page'], $params['site'])) {
            $params['this'] = array_merge($params['page'], $params['site']);
        } else {
            $params['this'] = array();
        }

        if (null !== $param) {
            $params = $this->locateParam($params, $param);
        }

        return isset($params) ? $params : $default;
    }

    /**
     * Locate nested param (levels separated with dot) in params array
     *
     * @return mixed
     */
    private function locateParam($params, $param)
    {
        $value = null;
        $keys = explode('.', $param);
        for ($i = 0, $mx = count($keys); $i < $mx; $i++) {
            $key = $keys[$i];
            if (isset($params[$key])) {
                $value = $params[$key];
                if ((($i + 1) < $mx) && is_array($value)) {
                    return $this->locateParam($value, implode('.', array_slice($keys, $i)));
                }
            }
        }
        return $value;
    }

    /**
     * Set site configuration
     *
     * @param array $config Array of options
     *
     * @return \Phrozn\Has\SiteConfig
     */
    public function setSiteConfig($config)
    {
        $this->siteConfig = $config;
        return $this;
    }

    /**
     * Get site configuration
     *
     * @return array
     */
    public function getSiteConfig()
    {
        if (null === $this->siteConfig) {
            $this->siteConfig = array();
        }
        return $this->siteConfig;
    }

    /**
     * Set front matter
     *
     * @param array $frontMatter Array of options
     *
     * @return \Phrozn\Has\FrontMatter
     */
    public function setFrontMatter($frontMatter)
    {
        $this->frontMatter = $frontMatter;
        return $this;
    }

    /**
     * Get YAML front matter from input view
     *
     * @return array
     */
    public function getFrontMatter()
    {
        if (null === $this->frontMatter) {
            $this->parse();
        }
        if (null === $this->frontMatter) {
            $this->frontMatter = array();
        }
        return $this->frontMatter;
    }

    /**
     * Set template
     *
     * @param string $template Source template
     *
     * @return \Phrozn\Has\Template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get template content from input view
     *
     * @return string
     */
    public function getTemplate()
    {
        if (null === $this->template) {
            $this->parse();
        }

        return $this->template;
    }

    /**
     * Two step view is used. View to wrap is provided with content variable.
     *
     * @param string $content View text to wrap into layout
     * @param array $vars List of variables passed to processors
     *
     * @return string
     */
    protected function applyLayout($content, $vars)
    {
        $layoutName = $this->getParam('page.layout', ViewFactory::DEFAULT_LAYOUT_SCRIPT);

        $inputFile = $this->getInputFile();
        $ds  = DIRECTORY_SEPARATOR;
        $pos = strpos($inputFile, $ds . 'entries');
        // make sure that input path is normalized to root entries directory
        if (false !== $pos) {
            $inputFile = substr($inputFile, 0, $pos + 8) . $ds . 'entry';
        }
        $layoutPath = realpath(dirname($inputFile) . '/../layouts/' . $layoutName);

        $factory = new ViewFactory($layoutPath);
        $layout = $factory->create(); // essentially layout is Site\View as well
        $layout->hasLayout(false); // no nested layouts

        return $layout->render(array('content' => $content, 'entry' => $vars['page']));
    }

    /**
     * Get/set hasLayout setting. Allows to enable/disable layout for a given view
     *
     * @param boolean $value Value to set to hasLayout option
     *
     * @return boolean
     */
    protected function hasLayout($value = null)
    {
        if (null !== $value) {
            $this->hasLayout = $value;
        }
        return $this->hasLayout;
    }


    /**
     * Parses input file into front matter and actual template content
     *
     * @return \Phrozn\Site\View
     */
    private function parse()
    {
        if (isset($this->template, $this->frontMatter)) {
            return $this;
        }

        $source = $this->readSourceFile();

        $parts = preg_split('/[\n]*[-]{3}[\n]/', $source, 2);
        if (count($parts) === 2) {
            $this->frontMatter = Yaml::load($parts[0]);
            $this->template = trim($parts[1]);
        } else {
            $this->frontMatter = array();
            $this->template = trim($source);
        }

        return $this;
    }

    /**
     * Read input file
     *
     * @return string
     */
    private function readSourceFile()
    {
        if (null == $this->source) {
            $path = $this->getInputFile();
            if (null === $path) {
                throw new \Exception("View input file not specified.");
            }
            try {
                $this->source = \file_get_contents($path);
            } catch (\Exception $e) {
                throw new \Exception(sprintf('View "%s" file can not be read', $path));
            }
        }
        return $this->source;
    }

    private function getAppConfig()
    {
        if (null === $this->appConfig) {
            $path = Loader::getInstance()->getPath('configs'). '/phrozn.yml';
            $this->appConfig = Yaml::load(file_get_contents($path));
        }

        return $this->appConfig;
    }
}

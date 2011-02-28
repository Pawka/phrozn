<?php
/**
 * Copyright 2011 Victor Farazdagi
 *
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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site;
use Phrozn\Site\View,
    Phrozn\Outputter\DefaultOutputter as Outputter,
    Symfony\Component\Yaml\Yaml;

/**
 * Base implementation of Phrozn Site 
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
abstract class Base
    implements \Phrozn\Site
{
    /**
     * List of views to process
     * @var array
     */
    private $views = array();

    /**
     * Input directory path. 
     * Generally is a path with Phrozn project or Phrozn project directory itself.
     * @var string
     */
    private $inputDir;

    /**
     * Site output directory path
     * @var string
     */
    private $outputDir;

    /**
     * Phrozn outputter
     * @var \Phrozn\Outputter
     */
    private $outputter;

    /**
     * Loaded content of site/config.yml
     * @var array
     */
    private $siteConfig;

    /**
     * Initialize site object
     *
     * @param string $inputDir Input directory path
     * @param string $outputDir Output directory path
     *
     * @return void
     */
    public function __construct($inputDir = null, $outputDir = null)
    {
        $this
            ->setInputDir($inputDir)
            ->setOutputDir($outputDir);
    }

    /**
     * Set input directory path
     *
     * @param string $path Directory path
     *
     * @return \Phrozn\Site
     */
    public function setInputDir($path)
    {
        $this->inputDir = $path;
        return $this;
    }

    /**
     * Get input directory path
     *
     * @return string
     */
    public function getInputDir()
    {
        return $this->inputDir;
    }

    /**
     * Set output directory path
     *
     * @param string $path Directory path
     *
     * @return \Phrozn\Site
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
     * Create list of views to be created
     *
     * @return \Phrozn\Site
     */
    protected function buildQueue()
    {
        $basePath = rtrim($this->getInputDir(), '/');
        if (is_dir($basePath . '/_phrozn')) {
            $basePath .= '/_phrozn/';
        } 

        if (!is_dir($basePath . '/entries')) {
            throw new \Exception('Entries folder not found');
        }

        $config = $this->parseConfig();

        if (isset($config['site']['output'])) {
            $outputDir = $config['site']['output'];
        } else {
            $outputDir = $this->getOutputDir();
        }

        $folders = array(
            'entries', 'styles', 'scripts'
        );
        foreach ($folders as $folder) {
            $dir = new \RecursiveDirectoryIterator($basePath . '/' . $folder);
            $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($it as $item) {
                $baseName = $item->getBaseName();
                if ($item->isFile()) {
                    try {
                        $factory = new View\Factory($item->getRealPath());
                        $view = $factory->create();
                        $view->setOutputDir($outputDir);
                        $this->views[] = $view;
                    } catch (\Exception $e) {
                        $this->getOutputter()
                                ->stderr(str_replace($basePath, '', $item->getRealPath()) . ': ' . $e->getMessage());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Return list of queued views ready to be processed
     *
     * @return array 
     */
    public function getQueue()
    {
        return $this->views;
    }

    /**
     * Set outputter
     *
     * @param \Phrozn\Outputter $outputter Outputter instance
     *
     * @return \Phrozn\Site
     */
    public function setOutputter($outputter)
    {
        $this->outputter = $outputter;
        return $this;
    }

    /**
     * Get outputter instance
     *
     * @return \Phrozn\Outputter
     */
    public function getOutputter()
    {
        if (null === $this->outputter) {
            $this->outputter = new Outputter;
        }
        return $this->outputter;
    }

    /**
     * Load site config
     *
     * @return array
     */
    protected function parseConfig()
    {
        if (null === $this->siteConfig) {
            $configFile = realpath($this->getInputDir() . '/config.yml');
            $this->siteConfig = Yaml::load($configFile);
        }
        return $this->siteConfig;
    }
}

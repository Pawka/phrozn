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
use Phrozn\Site\Page,
    Phrozn\Outputter\DefaultOutputter as Outputter;

/**
 * Base implementation of Phrozn Site 
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
abstract class BaseSite 
    implements \Phrozn\Site
{
    /**
     * List of pages to process
     * @var array
     */
    private $pages = array();

    /**
     * Path with Phrozn project
     * @var string
     */
    private $sourcePath;

    /**
     * Site output path
     * @var string
     */
    private $destinationPath;

    /**
     * Phrozn outputter
     * @var \Phrozn\Outputter
     */
    private $outputter;

    /**
     * Initialize site object
     *
     * @param string $sourcePath Path to source file
     * @param string $destinationPath Path to destination file
     *
     * @return void
     */
    public function __construct($sourcePath = null, $destinationPath = null)
    {
        $this
            ->setSourcePath($sourcePath)
            ->setDestinationPath($destinationPath);
    }

    /**
     * Set path of source Phrozn project to compile
     *
     * @param string $path Phrozn source path
     *
     * @return \Phrozn\Site
     */
    public function setSourcePath($path)
    {
        $this->sourcePath = $path;
        return $this;
    }

    /**
     * Get path of source Phrozn project
     *
     * @return string
     */
    public function getSourcePath() 
    {
        return $this->sourcePath;
    }

    /**
     * Set where to compile site into
     *
     * @param string $path Destination/output path
     *
     * @return \Phrozn\Site
     */
    public function setDestinationPath($path)
    {
        $this->destinationPath = $path;
        return $this;
    }

    /**
     * Get site output path
     *
     * @return string
     */
    public function getDestinationPath()
    {
        return $this->destinationPath;
    }

    /**
     * Create list of pages to be created
     *
     * @return \Phrozn\Site
     */
    protected function buildQueue()
    {
        $basePath = rtrim($this->getSourcePath(), '/');
        if (is_dir($basePath . '/_phrozn')) {
            $basePath .= '/_phrozn/';
        } 

        if (!is_dir($basePath . '/entries')) {
            throw new \Exception('Entries folder not found');
        }

        $dir = new \RecursiveDirectoryIterator($basePath . '/entries');
        $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $dirname = '';
        foreach ($it as $item) {
            $baseName = $item->getBaseName();
            if ($baseName != '.' && $baseName != '..') {
                if ($item->isFile()) {
                    try {
                        $factory = new PageFactory($item->getRealPath());
                        $page = $factory->createPage();
                        $page->setDestinationPath($this->getDestinationPath());
                        $this->pages[] = $page;
                    } catch (\Exception $e) {
                        $this->getOutputter()
                             ->stderr($page->getName() . ': ' . $e->getMessage());
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Return list of queued pages
     *
     * @return array 
     */
    public function getQueue()
    {
        return $this->pages;
    }

    /**
     * Set outputter
     *
     * @param \Phrozn\Outputter $outputter Outputter instance
     *
     * @return \Phrozn\Has\Outputter
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
            $this->outputter = new Outputter();
        }
        return $this->outputter;
    }
}

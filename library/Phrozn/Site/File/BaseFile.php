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
 * @package     Phrozn\Site\File
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\File;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Site\FileFactory,
    Phrozn\Site\Layout\DefaultLayout as Layout;

/**
 * Abstract base implementation of Phrozn  File
 *
 * @category    Phrozn
 * @package     Phrozn\Site\File
 * @author      Victor Farazdagi
 */
abstract class BaseFile 
    implements \Phrozn\Site\File
{
    /**
     * Input file
     * @var string
     */
    private $sourcePath;

    /**
     * Output file
     * @var string
     */
    private $destinationPath;

    /**
     * Markup processor
     * @var \Phrozn\Processor
     */
    private $processor;

    /**
     * Template source text
     * @var string
     */
    private $source;

    /**
     * Initialize page
     *
     * @param string $sourcePath Path to page source file
     * @param string $destinationPath File destination path
     * @param \Phrozn\Process $processor Phrozn markup processor
     *
     * @return \Phrozn\Site\File
     */
    public function __construct($sourcePath = null, $destinationPath = null, $processor = null)
    {
        $this
            ->setSourcePath($sourcePath)
            ->setDestinationPath($destinationPath)
            ->setProcessor($processor);
    }

    /**
     * Create static version of a concrete page
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return \Phrozn\Site\File
     */
    public function compile($vars = array())
    {
        $out = $this->render($vars);

        // getDestinationPath() is template method, 
        // overriden by concrete class
        file_put_contents($this->getDestinationPath(), $out);

        return $out;
    }

    /**
     * Render input template
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return string
     */
    public function render($vars = array())
    {
        // inject front matter options into template
        $vars = array_merge($vars, array('this' => $this->extractFrontMatter()));

        // parse page
        $page = $this->getProcessor()
                     ->render($this->extractTemplate(), $vars);

        return $this->applyLayout($page, $vars);
    }

    /**
     * Set page input file path
     *
     * @param string $file Path to source file
     *
     * @return \Phrozn\Site\File
     */
    public function setSourcePath($path)
    {
        $this->sourcePath = $path;
        return $this;
    }

    /**
     * Get page input file path
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
     * Set markup processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Site\File
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }

    /**
     * Get markup processor
     *
     * @return \Phrozn\Processor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Get page name
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->getSourcePath());
    }

    /**
     * Two step view is used. Layout is provided with content variable.
     *
     * @param string $content File content to inject into layout
     * @param array $vars List of variables passed to template engine
     *
     * @return string
     */
    protected function applyLayout($content, $vars)
    {
        $layoutName = isset($vars['this']['layout']) 
                    ? $vars['this']['layout'] : Layout::DEFAULT_LAYOUT_SCRIPT;
        $layoutPath = realpath(dirname($this->getSourcePath()) . '/../layouts/' . $layoutName);

        $layout = new Layout($layoutPath, $this->getProcessor());

        return $layout->render(array('content' => $content));
    }

    /**
     * Extract page template, from source file
     *
     * @return string
     */
    private function extractTemplate()
    {
        $source = $this->readSourceFile();

        $pos = strpos($source, '---');
        if ($pos === false) {
            return $source;
        }

        return substr($source, $pos + 3);
    }

    /**
     * Extract YAML front matter from page's source file
     *
     * @return array
     */
    private function extractFrontMatter()
    {
        $source = $this->readSourceFile();

        $pos = strpos($source, '---');
        if ($pos === false) {
            return null;
        }

        $frontMatter = substr($source, 0, $pos);
        $parsed = Yaml::load($frontMatter);

        return $parsed;
    }

    /**
     * Read input file
     *
     * @return string
     */
    private function readSourceFile()
    {
        if (null == $this->source) {
            $path = $this->getSourcePath();
            if (null === $path) {
                throw new \Exception("Source file not specified.");
            }

            $this->source = \file_get_contents($path);
        }
        return $this->source;
    }
}

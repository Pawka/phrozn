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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\View;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Site\View\Factory,
    Phrozn\Site\Layout\DefaultLayout as Layout;

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

        file_put_contents($this->getOutputFile(), $out);

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
        $vars = array_merge($vars, array('this' => $this->extractFrontMatter()));

        // convert view into static representation
        $view = $this->extractTemplate();
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
        return $this->outputFile;
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
     * Two step view is used. View to wrap is provided with content variable.
     *
     * @param string $content View text to wrap into layout
     * @param array $vars List of variables passed to processors
     *
     * @return string
     */
    protected function applyLayout($content, $vars)
    {
        $layoutName = isset($vars['this']['layout']) 
                    ? $vars['this']['layout'] : Factory::DEFAULT_LAYOUT_SCRIPT;
        $inputFile = $this->getInputFile();
        $pos = strpos($inputFile, '/entries');
        // make sure that input path is normalized to root entries directory 
        if (false !== $pos) {
            $inputFile = substr($inputFile, 0, $pos + 8) . '/entry';
        }
        $layoutPath = realpath(dirname($inputFile) . '/../layouts/' . $layoutName);

        $factory = new Factory($layoutPath);
        $layout = $factory->create(); // essentially layout is Site\View as well
        $layout->hasLayout(false); // no nested layouts

        return $layout->render(array('content' => $content));
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
     * Extract template part, from view input file
     *
     * @return string
     */
    protected function extractTemplate()
    {
        $source = $this->readSourceFile();

        $pos = strpos($source, '---');
        if ($pos === false) {
            return $source;
        }

        return substr($source, $pos + 3);
    }

    /**
     * Extract YAML front matter from view input file
     *
     * @return array
     */
    protected function extractFrontMatter()
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
            $path = $this->getInputFile();
            if (null === $path) {
                throw new \Exception("View input file not specified.");
            }
            $this->source = \file_get_contents($path);
        }
        return $this->source;
    }
}

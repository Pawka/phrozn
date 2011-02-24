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
 * @package     Phrozn\Site\Page
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\Page;
use Symfony\Component\Yaml\Yaml;

/**
 * Abstract baase implementation of Phrozn  Page
 *
 * @category    Phrozn
 * @package     Phrozn\Site\Page
 * @author      Victor Farazdagi
 */
abstract class BasePage 
    implements \Phrozn\Site\Page
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
     * @param string $source Page source file
     * @param string $destination Page destination path
     * @param \Phrozn\Process $processor Phrozn markup processor
     *
     * @return \Phrozn\Site\Page
     */
    public function __construct($source = null, $destination = null, $processor = null)
    {
        $this
            ->setSourcePath($source)
            ->setDestinationPath($destination)
            ->setProcessor($processor);
    }

    /**
     * Create static version of a concrete page
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return \Phrozn\Site\Page
     */
    public function compile($vars)
    {
        $out = $this->render($vars);

        $path = $this->getDestinationPath() . '/'
              . basename($this->getSourcePath(), '.twig') . '.html';
        file_put_contents($path, $out);

        return $out;
    }

    /**
     * Render input template
     *
     * @param array $vars List of variables passed to template engine
     *
     * @return string
     */
    public function render($vars)
    {
        $vars = array_merge($vars, array('this' => $this->extractFrontMatter()));
        return $this->getProcessor()
                    ->render($this->extractTemplate(), $vars);
    }

    /**
     * Set page input file path
     *
     * @param string $file Path to source file
     *
     * @return \Phrozn\Site\Page
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
     * @return \Phrozn\Site\Page
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
                throw new \Exception("Page's source file not specified.");
            }

            $this->source = \file_get_contents($path);
        }
        return $this->source;
    }
}

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
    Symfony\Component\Yaml\Yaml,
    Phrozn\Has;

/**
 * Page producing factory
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class PageFactory 
    implements Has\Source
{
    /**
     * Page\Html page is initialized by default
     */
    const DEFAULT_PAGE_TYPE = 'twig';

    /**
     * Path to input file
     * @var string
     */
    private $sourcePath;

    /**
     * Template source text
     * @var string
     */
    private $source;

    /**
     * Initialize factory by providing input file path
     *
     * @param string $path Path to page source file
     *
     * @return void
     */
    public function __construct($path = null)
    {
        $this->setSourcePath($path);
    }

    /**
     * Depending on internal configuration and concrete type, create page
     *
     * return \Phrozn\Site\Page
     */
    public function createPage()
    {
        $fm = $this->getFrontMatter();
        if ($fm === null) {
            throw new \Exception('Page front matter not found');
        }

        $type = isset($fm['type']) ? $fm['type'] : self::DEFAULT_PAGE_TYPE;

        return $this->constructPage($type);
    }

    /**
     * Set page input file path
     *
     * @param string $path Path to source file
     *
     * @return \Phrozn\Site\Page
     */
    public function setSourcePath($path)
    {
        if (null !== $path) {
            if (!is_readable($path)) {
                throw new \Exception("Page source file cannot be read: {$path}");
            }
            $this->sourcePath = $path;
        }

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
     * Extract page template content
     *
     * @return string
     */
    private function getFrontMatter()
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

    /**
     * Create and return page of a given type
     *
     * @param string $type Page type to load
     *
     * @return \Phrozn\Site\Page
     */
    private function constructPage($type)
    {
        $class = 'Phrozn\\Site\\Page\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new \Exception("Page of type '{$type}' not found..");
        }
        $object = new $class;
        $object->setSourcePath($this->getSourcePath());
        return $object;
    }
}

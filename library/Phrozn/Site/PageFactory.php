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
     * Page\Twig is default page type
     */
    const DEFAULT_PAGE_TYPE = 'twig';

    /**
     * Path to input file
     * @var string
     */
    private $sourcePath;

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
    public function create()
    {
        $ext = pathinfo($this->getSourcePath(), PATHINFO_EXTENSION);

        $type = $ext ? : self::DEFAULT_PAGE_TYPE;

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

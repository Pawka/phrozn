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
use Phrozn\Site\File,
    Symfony\Component\Yaml\Yaml,
    Phrozn\Has;

/**
 * File producing factory
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class FileFactory 
    implements Has\Source
{
    /**
     * File\Twig is default page type
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
     * return \Phrozn\Site\File
     */
    public function create()
    {
        $ext = pathinfo($this->getSourcePath(), PATHINFO_EXTENSION);

        $type = $ext ? : self::DEFAULT_PAGE_TYPE;

        return $this->constructFile($type);
    }

    /**
     * Set page input file path
     *
     * @param string $path Path to source file
     *
     * @return \Phrozn\Site\File
     */
    public function setSourcePath($path)
    {
        if (null !== $path) {
            if (!is_readable($path)) {
                throw new \Exception("File source file cannot be read: {$path}");
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
     * @param string $type File type to load
     *
     * @return \Phrozn\Site\File
     */
    private function constructFile($type)
    {
        $class = 'Phrozn\\Site\\File\\' . ucfirst($type);
        if (!class_exists($class)) {
            throw new \Exception("File of type '{$type}' not found..");
        }
        $object = new $class;
        $object->setSourcePath($this->getSourcePath());
        return $object;
    }
}

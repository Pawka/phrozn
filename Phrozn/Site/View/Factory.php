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
    Phrozn\Has;

/**
 * View producing factory
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class Factory
    implements Has\InputFile
{
    /**
     * Site\View\Html is default page type
     */
    const DEFAULT_VIEW_TYPE = 'html';

    /**
     * By default assume default.twig as layout input file
     */
    const DEFAULT_LAYOUT_SCRIPT = 'default.twig';

    /**
     * Path to input root dir
     *
     * @var string
     */
    private $inputRootDir;

    /**
     * Path to input file
     * @var string
     */
    private $inputFile;

    /**
     * Initialize factory by providing input file path
     *
     * @param string $path Path to page source file
     *
     * @return void
     */
    public function __construct($path = null)
    {
        $this->setInputFile($path);
    }

    /**
     * Depending on internal configuration and concrete type, create view
     *
     * return \Phrozn\Site\View\Factory
     */
    public function create()
    {
        $ext = pathinfo($this->getInputFile(), PATHINFO_EXTENSION);

        $type = $ext ? : self::DEFAULT_VIEW_TYPE;

        return $this->constructFile($type);
    }

    /**
     * Set input root dir
     *
     * @param string $path Input root directory
     *
     * @return \Phrozn\Site\View\Factory
     */
    public function setInputRootDir($path)
    {
        $this->inputRootDir = $path;
        return $this;
    }

    /**
     * Get input root directory
     *
     * @return string
     */
    public function getInputRootDir()
    {
        return $this->inputRootDir;
    }

    /**
     * Set input file path
     *
     * @param string $path Path to file
     *
     * @return \Phrozn\Site\View\Factory
     */
    public function setInputFile($path)
    {
        if (null !== $path) {
            if (!is_readable($path)) {
                throw new \RuntimeException("View source file cannot be read: {$path}");
            }
            $this->inputFile = $path;
        }

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
     * Create and return view of a given type
     *
     * @param string $type File type to load
     *
     * @return \Phrozn\Site\View
     */
    private function constructFile($type)
    {
        // try to see if we have user defined plugin
        $class = 'PhroznPlugin\\Site\\View\\' . ucfirst($type);
        if (!class_exists($class)) {
            $class = 'Phrozn\\Site\\View\\' . ucfirst($type);
            if (!class_exists($class)) {
                //throw new \RuntimeException("View of type '{$type}' not found..");
                $class = 'Phrozn\\Site\\View\\Plain';
            }
        }
        $object = new $class;
        $object->setInputRootDir($this->getInputRootDir());
        $object->setInputFile($this->getInputFile());
        return $object;
    }
}

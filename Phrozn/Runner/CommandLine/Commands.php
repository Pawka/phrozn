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
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner\CommandLine;
use Symfony\Component\Yaml\Yaml;

/**
 * Collection of phr commands
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class Commands
    implements \Iterator
{
    /**
     * Loaded commands data
     */
    private $commands = array();

    /**
     * Directory iterator to commands config directory
     * @var \DirectoryIterator
     */
    private $it;

    /**
     * Path where commands configs are stored
     */
    private $path;

    /**
     * @var \Phrozn\Runner\CommandLine\Commands
     */
    private static $instance;

    private function __construct()
    {}

    private function __clone()
    {}

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function setPath($path)
    {
        $this->path = $path;
        self::$instance->rewind(); // make sure we reset instance
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function current()
    {
        return $this->load($this->getIterator()->getPathname());
    }

    public function key()
    {
        $data = $this->current();
        return $data['command']['name'];
    }

    public function next()
    {
        $this->getIterator()->next();
    }

    public function rewind()
    {
        $this->getIterator()->rewind();
    }

    public function valid()
    {
        $it = $this->getIterator();

        // skip directories and file !*.yml
        while ($it->valid()
            && (
                $it->isDot()
                || $it->isLink()
                || $it->isFile() === false
                || $it->getBasename('.yml') === $it->getFilename() // only *.yml files
            )
        ) {
            $this->getIterator()->next();
        }
        return $this->getIterator()->valid();
    }

    /**
     * Load configuration for a given command.
     * Loading happens only once, after that cached info is returned
     *
     * @param string $file  Config file name
     *
     * @return array Array of loaded config data
     */
    private function load($file)
    {
        if (!isset($this->commands[$file])) {
            $this->commands[$file] = Yaml::parse($file);
        }
        return $this->commands[$file];
    }

    /**
     * Get DirecotryIterator to commands config path
     *
     * @return \DirectoryIterator
     */
    private function getIterator()
    {
        if (null === $this->getPath()) {
            throw new \RuntimeException('Commands config path not set');
        }
        if (null === $this->it) {
            $this->it =  new \DirectoryIterator($this->getPath());
        }
        return $this->it;
    }
}

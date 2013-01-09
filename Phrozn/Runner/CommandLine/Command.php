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
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner\CommandLine;
use Symfony\Component\Yaml\Yaml;

/**
 * Single phrozn sub-command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class Command
    implements \ArrayAccess
{
    /**
     * Loaded command's data
     */
    private $command;

    public function __construct($file)
    {
        $this->load($file);
    }
    public function offsetExists($offset)
    {
        return isset($this->command[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->command[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->command[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->command[$offset]);
    }

    /**
     * Load configuration for a given command.
     * Loading happens only once, after that cached info is returned
     *
     * @param string $file  Config file path
     *
     * @return array Array of loaded config data
     */
    private function load($file)
    {
        if (null === $this->command) {
            $this->command = Yaml::parse($file);
        }
        return $this->command;
    }
}

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
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Has;

/**
 * Phozn configuration reader and aggregator
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class Config
    implements \ArrayAccess
{
    /**
     * Loaded YAML config files
     * @var array
     */
    private $configs;

    /**
     * Setup config aggregator
     *
     * @param string $path Path to config folder
     *
     * @return
     */
    public function __construct($path)
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $item) {
            if ($item->isFile()) {
                if (substr($item->getBasename(), -3) === 'yml') {
                    $this->configs[$item->getBasename('.yml')] = Yaml::load($item->getRealPath());
                }
            }
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->configs[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->configs[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->configs[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->configs[$offset]);
    }
}

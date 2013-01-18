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
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Has,
    Phrozn\Autoloader as Loader;

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
        if (is_file($path)) {
            $this->configs[basename($path, '.yml')] = Yaml::parse($path);
        } else {
            $dir = new \DirectoryIterator($path);
            foreach ($dir as $item) {
                if ($item->isFile()) {
                    if (substr($item->getBasename(), -3) === 'yml') {
                        $this->configs[$item->getBasename('.yml')] = Yaml::parse($item->getRealPath());
                    }
                }
            }
            $this->updatePaths(); // make sure that config.yml paths are absolute
        }
    }

    /**
     * Check whether specified config section exists
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->configs[$offset]);
    }

    /**
     * Get loaded configuration identified by $offset
     *
     * @param string $offset Configuration file basename (w/o extension)
     *
     * @return array
     */
    public function offsetGet($offset)
    {
        return $this->configs[$offset];
    }

    /**
     * Set config file identified by $offset
     *
     * @param string $offset Configuration file basename (w/o extension)
     * @param array $value
     *
     * @return \Phrozn\Config
     */
    public function offsetSet($offset, $value)
    {
        $this->configs[$offset] = $value;
        return $this;
    }

    /**
     * Unset config file specified by $offset
     *
     * @param string $offset Configuration item to unset
     *
     * @return \Phrozn\Config
     */
    public function offsetUnset($offset)
    {
        unset($this->configs[$offset]);
        return $this;
    }

    /**
     * Make sure that absolute application path is prepended to config paths
     *
     * @return \Phrozn\Config
     */
    public function updatePaths()
    {
        if (isset($this->configs['paths'])) {
            $paths = Loader::getInstance()->getPaths();
            foreach ($this->configs['paths'] as $key => $file) {
                $file = str_replace('@PEAR-DIR@', $paths['php_dir'], $file);
                $file = str_replace('@DATA-DIR@', $paths['data_dir'], $file);
                $this->configs['paths'][$key] = $file;
            }
        }
        return $this;
    }
}

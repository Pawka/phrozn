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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Processor;

/**
 * Base implementation of text processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
abstract class Base
    implements \Phrozn\Processor
{
    /**
     * Engine environment configuration
     */
    private $config = array();

    /**
     * Gateway to pass concrete Processor some configuration options
     *
     * @param array $options Options to pass to engine
     *
     * @return \Phrozn\Processor
     */
    public function setConfig($options)
    {
        $this->config = $options;
        return $this;
    }

    /**
     * Get processor configuration, as array
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get a specific processor configuration, or null if unset
     * Alternative API : getConfigValue ?
     *
     * @param  string $key
     * @return mixed|null
     */
    public function getConfigFor($key)
    {
        $config = null;
        if (isset($this->config[$key])) {
            $config = $this->config[$key];
        }

        return $config;
    }
}

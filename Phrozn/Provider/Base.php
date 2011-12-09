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
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Provider;
use Phrozn\Provider;

/**
 * Implementation of common methods of content providers
 *
 * @category    Phrozn
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 */
abstract class Base
    implements Provider
{
    /**
     * Engine environment configuration
     */
    private $config = array();

    /**
     * Project path
     * @var string
     */
    private $projectPath;

    /**
     * Init provider
     *
     * @param array $options Config options
     *
     * @return void
     */
    public function __construct($options = null)
    {
        $this->setConfig($options);
    }

    /**
     * Setup provider
     *
     * @param array $options Options to pass to provider
     *
     * @return \Phrozn\Provider
     */
    public function setConfig($options)
    {
        $this->config = $options;
        return $this;
    }

    /**
     * Get provider configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set project path.
     *
     * @param string $path Project path.
     *
     * @return \Phrozn\Has\ProjectPath
     */
    public function setProjectPath($path)
    {
        $this->projectPath = $path;
        return $this;
    }

    /**
     * Get project path.
     *
     * @return string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

}

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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Processor;

/**
 * Base implementation of templates processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
abstract class BaseProcessor
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
     * Get processor configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}

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
 * @category    PhroznPlugin
 * @package     PhroznPlugin\Provider
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznPlugin\Provider;
use Phrozn\Provider;

/**
 * Load contents of the file
 *
 * @category    Phrozn
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 */
class PluginProvider
    extends Provider\Base
{
    /**
     * Get generated content
     *
     * @return mixed
     */
    public function get()
    {
        $config = $this->getConfig();
        if (!isset($config['input'])) {
            throw new \Exception('No input file provided.');
        }
        $path = $this->getProjectPath() . '/' . $config['input'];
        if (!is_readable($path)) {
            throw new \Exception(sprintf('Input file "%s" not found.', $path));
        }
        return file_get_contents($path);
    }

}

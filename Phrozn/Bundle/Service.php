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
 * @package     Phrozn\Bundle\Service
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Bundle;
use Phrozn\Has,
    Phrozn\Bundle,
    Phrozn\Path\Project as ProjectPath;

/**
 * Bundle service exposing bundle managing functionality
 *
 * @category    Phrozn
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 */
class Service
    implements Has\Config
{
    /**
     * Repository with bundles
     * @var string
     */
    private $repo = 'https://github.com/farazdagi/phrozn-bundles/'; 

    /**
     * Configuration object
     * @var \Phrozn\Config
     */
    private $config;

    /**
     * Get list of bundles by type
     *
     * @param string $type Bundles type. 
     * @param string $filter Bundle search term. When listing you can filter by bundle.
     *
     * @see Bundle::TYPE_ALL
     * @see Bundle::TYPE_AVAILABLE
     * @see Bundle::TYPE_INSTALLED
     *
     * @return array of \Phrozn\Bundle objects
     */
    public function getBundles($type = Bundle::TYPE_ALL, $filter = null)
    {
        $types =  array(
            Bundle::TYPE_ALL,
            Bundle::TYPE_AVAILABLE,
            Bundle::TYPE_INSTALLED
        );
        if (!in_array($type, $types)) {
            throw new \Exception(sprintf('Invalid bundle type "%s".', $type));
        }
        $config = $this->getConfig();
        $bundles = array();
        foreach ($config['bundles'] as $bundle) {
            if ($filter) {
                if (
                    (false === stripos($bundle['name'], $filter)) 
                 && (false === stripos($bundle['id'], $filter)) 
                ) { // ID 
                    continue;
                } 
            } 
            $bundles[$bundle['id']] = $bundle;
        }
        return $bundles;
   }

    /**
     * Apply given bundle
     *
     * @param string $path Path where bundle is to be applied
     * @param string $bundle Bundle name, URI or filename
     *
     * @return \Phrozn\Bundle
     */
    public function applyBundle($path, $bundle)
    {
        $path = new ProjectPath($path);
        var_dump($path->get());
    }

    /**
     * Get bundle info
     *
     * @param string $bundle Bundle name, URI or filename
     *
     * @throws \Exception
     * @return array
     */
    public function getBundleInfo($bundle)
    {
        $bundle = strtolower($bundle);
        $config = $this->getConfig();
        $bundles = $config['bundles'];

        if (isset($bundles[$bundle])) {
            return $bundles[$bundle];
        } else {
            foreach ($bundles as $data) {
                if (strtolower($data['name']) == $bundle) {
                    return $data;
                }
            }
        }
        throw new \Exception(sprintf('Bundle "%s" not found..', $bundle));
    }

    /**
     * Set configuration
     *
     * @param array $config Array of options
     *
     * @return \Phrozn\Has\Config
     */
    public function setConfig($config)
    {
        if (!($config instanceof \Phrozn\Config)) {
            throw new \Exception('Configuration object must be an instance of Phrozn\Config');
        }
        $this->config = $config;
        return $this;
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}

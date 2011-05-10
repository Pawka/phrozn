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
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;
use Archive_Tar as BundleArchive;

/**
 * Phozn bundle
 *
 * @category    Phrozn
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 */
class Bundle
    implements Has\InputFile,
               Has\Config
{
    /**
     * Official repository 
     * @var string
     */
    const REPO = 'https://github.com/farazdagi/phrozn-bundles/raw/master/'; 

    const TYPE_ALL = 'all';
    const TYPE_AVAILABLE = 'available';
    const TYPE_INSTALLED = 'installed';

    /**
     * Configuration object
     * @var \Phrozn\Config
     */
    private $config;

    /**
     * Bundle source file (archive having bundle contents)
     * @var string
     */
    private $bundleFile;

    /**
     * Array of bundle information
     * @var array
     */
    private $bundleData;

    /**
     * Bundle initialization key: either path, URL or name
     * @var string
     */
    private $bundleKey;

    /**
     * Initialize bundle
     *
     * @param string $bundle Bundle name, path or URL
     * @param \Phrozn\Config $config Bundles configuration
     *
     * @return void
     */
    public function __construct($bundle, $config = null)
    {
        $this->setConfig($config)
             ->setKey($bundle)
             ->discover(); // discover input file
    }

    /**
     * Get list of files in the bundle
     *
     * @return array
     */
    public function getFiles()
    {
        $tar = new BundleArchive($this->getInputFile());
        return $tar->listContent();
    }

    /**
     * Extract bundle content into given path
     *
     * @param string $path Destination path
     *
     * @return \Phrozn\Bundle
     */
    public function extractTo($path, $dryrun)
    {
        $path = (string)$path; // if \Phrozn\Path passed convert to string
        var_dump($list);

        return $this;
    }

    /**
     * Ger array of bundle options
     *
     * @param string $option Key whose value to fetch
     *
     * @return array
     */
    public function getInfo($option = null)
    {
        if (null !== $this->bundleData) {
            return $option 
                ? $this->bundleData[$option] 
                : $this->bundleData;
        }

        $key = strtolower($this->getKey());
        $config = $this->getConfig();
        $bundles = $config['bundles'];

        if (isset($bundles[$key])) {
            $this->bundleData = $bundles[$key];
        } else {
            foreach ($bundles as $bundle) {
                if (strtolower($bundle['name']) == $key) {
                    $this->bundleData = $bundle;
                }
            }
        }
        if (null === $this->bundleData) {
            throw new \Exception(sprintf('Bundle "%s" not found..', $key));
        }

        return $option 
            ? $this->bundleData[$option] 
            : $this->bundleData;
    }

    /**
     * Set input file path
     *
     * @param string $file Path to file
     *
     * @return \Phrozn\Has\InputFile
     */
    public function setInputFile($path)
    {
        $this->bundleFile = $path;
        return $this;
    }

    /**
     * Get input file path
     *
     * @return string
     */
    public function getInputFile()
    {
        return $this->bundleFile;
    }

    /**
     * Set bundle key
     *
     * @param string $key Bundle key
     *
     * @return \Phrozn\Bundle
     */
    private function setKey($key)
    {
        $this->bundleKey = $key;
        return $this;
    }

    /**
     * Set bundle key
     *
     * @return string
     */
    private function getKey()
    {
        return $this->bundleKey;
    }

    /**
     * Discover bundle source.
     * Bundles sources include filesystem path, URL or bundle name.
     *
     * @return string
     */
    private function discover()
    {
        if (null === $this->getInputFile()) {
            $bundle = $this->getKey();
            $bundleId = $this->getInfo('id');
            if (substr($bundle, 0, 4) === 'http') {
                $this->setInputFile($bundle);
            } else if (substr($bundle, -4) === '.tgz') {
                $this->setInputFile($bundle);
            } 
            $this->setInputFile(self::REPO . $bundleId . '.tgz');
        }

        return $this;
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
        if (null !== $config && !($config instanceof \Phrozn\Config)) {
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

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
 * @package     Phrozn\Bundle\Service
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Bundle;
use Phrozn\Has,
    Phrozn\Bundle,
    Phrozn\Registry\Container\Bundles as RegistryContainer,
    Phrozn\Registry\Dao\Serialized as RegistryDao,
    Phrozn\Path\Project as ProjectPath;

/**
 * Bundle service exposing bundle managing functionality
 *
 * @category    Phrozn
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 */
class Service
    implements
            Has\Config,
            Has\RegistryContainer,
            Has\ProjectPath
{
    /**
     * Configuration object
     * @var \Phrozn\Config
     */
    private $config;

    /**
     * Bundle registry
     * @var \Phrozn\Registry\Container
     */
    private $registryContainer;

    /**
     * Service operates on a project located at given path.
     * @var \Phrozn\Path\Project
     */
    private $projectPath;

    /**
     * Initializat the service
     *
     * @param \Phrozn\Config $config Service configuration options
     * @param \Phrozn\Path|string $path Project path (location of .phrozn directory)
     */
    public function __construct($config = null, $path = null)
    {
        $this
            ->setConfig($config)
            ->setProjectPath($path);
    }

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
            throw new \RuntimeException(sprintf('Invalid bundle type "%s".', $type));
        }
        $config = $this->getConfig();
        $registry = $this->getRegistryContainer();

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
            switch ($type) {
                case Bundle::TYPE_INSTALLED:
                    if (false === $registry->isInstalled($bundle['id'])) {
                        continue 2;
                    }
                    break;
                case Bundle::TYPE_AVAILABLE:
                    if (true === $registry->isInstalled($bundle['id'])) {
                        continue 2;
                    }
                    break;
                default:
                    // do nothing
                    break;
            }
            $bundles[$bundle['id']] = $bundle;
        }
        return $bundles;
    }

    /**
     * List all files present in bundle
     *
     * @param string $bundle Bundle name, URI or filename
     *
     * @return array
     */
    public function getBundleFiles($bundle)
    {
        $bundle = new Bundle($bundle, $this->getConfig());
        return $bundle->getFiles();
    }

    /**
     * Apply given bundle
     *
     * @param string $bundle Bundle name, URI or filename
     *
     * @return \Phrozn\Bundle
     */
    public function applyBundle($bundle)
    {
        $bundle = new Bundle($bundle, $this->getConfig());
        $bundleId = $bundle->getInfo('id');

        $registry = $this->getRegistryContainer();

        if ($registry->isInstalled($bundleId)) {
            throw new \RuntimeException(
                sprintf('Bundle "%s" is already installed.', $bundleId));
        }

        // install
        $bundle->extractTo($this->getProjectPath());

        // persist list of installed bundles
        $registry->markAsInstalled($bundleId, $bundle->getFiles());
    }

    /**
     * Remove given bundle from project directory
     *
     * @param string $bundle Bundle name, URI or filename
     *
     * @return \Phrozn\Bundle
     */
    public function clobberBundle($bundle)
    {
        $bundle = new Bundle($bundle, $this->getConfig());
        $bundleId = $bundle->getInfo('id');

        $registry = $this->getRegistryContainer();

        if (false === $registry->isInstalled($bundleId)) {
            throw new \RuntimeException(
                sprintf('Bundle "%s" is NOT installed.', $bundleId));
        }

        // uninstall
        $bundle->removeFrom($this->getProjectPath());

        // persist list of installed bundles
        $registry->markAsUninstalled($bundleId);
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
        $bundle = new Bundle($bundle, $this->getConfig());
        return $bundle->getInfo();
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
            throw new \RuntimeException('Configuration object must be an instance of Phrozn\Config');
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

    /**
     * Set container
     *
     * @param \Phrozn\Registry\Container $container Registry container
     *
     * @return \Phrozn\Has\RegistryContainer
     */
    public function setRegistryContainer($container)
    {
        $this->registryContainer = $container;
        return $this;
    }

    /**
     * Get registry container
     *
     * @return \Phrozn\Registry\Container
     */
    public function getRegistryContainer()
    {
        if (null === $this->registryContainer) {
            $dao = new RegistryDao();
            $dao
                ->setProjectPath($this->getProjectPath())
                ->setOutputFile('.bundles');
            $this->registryContainer = new RegistryContainer($dao);
            $this->registryContainer->read(); //(re)read container values
        } else { // update project path
            $this
                ->registryContainer
                ->getDao()
                ->setProjectPath($this->getProjectPath());
        }
        return $this->registryContainer;
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
        $path = (string)$path;
        $this->projectPath = new ProjectPath($path);
        return $this;
    }

    /**
     * Get project path.
     *
     * @return string
     */
    public function getProjectPath()
    {
        return (string)$this->projectPath;
    }

}

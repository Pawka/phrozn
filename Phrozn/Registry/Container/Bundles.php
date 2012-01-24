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
 * @package     Phrozn\Registry
 * @subpackage  Container
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry\Container;
use Phrozn\Registry\Container;

/**
 * Phrozn registry container for managing bundles.
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @subpackage  Container
 * @author      Victor Farazdagi
 */
class Bundles
    extends Container
{
    /**
     * Initialize container
     *
     * @return void
     */
    public function init()
    {
        if (null === $this->get('installed')) {
            $this->set('installed', array());
        }
        if (null === $this->get('files')) {
            $this->set('files', array());
        }
    }

    /**
     * Mark bundle as installed
     *
     * @param string $bundle Bundle identifier.
     *
     * @return \Phrozn\Registry\Container
     */
    public function markAsInstalled($bundle, $files)
    {
        $installed = $this->get('installed');
        $installed[] = $bundle;

        $bundleFiles = $this->get('files');
        $bundleFiles[$bundle] = $files;
        $this
            ->set('installed', $installed)
            ->set('files', $bundleFiles)
            ->save();
        return $this;
    }

    /**
     * Mark bundle as uninstalled (clean from registry)
     *
     * @param string $bundle Bundle identifier.
     *
     * @return \Phrozn\Registry\Container
     */
    public function markAsUninstalled($bundle)
    {
        $installed = $this->get('installed');
        $bundleFiles = $this->get('files');

        if (false !== ($k = array_search($bundle, $installed))) {
            unset($installed[$k]);
        }
        if (isset($bundleFiles[$bundle])) {
            unset($bundleFiles[$bundle]);
        }
        $this
            ->set('installed', $installed)
            ->set('files', $bundleFiles)
            ->save();
        return $this;
    }

    /**
     * Check whether bundle is installed
     *
     * @param string $bundle Bundle identifier.
     *
     * @return boolean
     */
    public function isInstalled($bundle)
    {
        $installed = $this->get('installed');
        return in_array($bundle, $installed);
    }

    public function getFiles($bundle)
    {
        $files = $this->get('files');
        return isset($files[$bundle]) ? $files[$bundle] : array();
    }

    /**
     * (Re)read current container from DAO
     *
     * @return \Phrozn\Registry\Container
     */
    public function read()
    {
        parent::read();
        $this->init(); // re-initialize container
        return $this;
    }
}

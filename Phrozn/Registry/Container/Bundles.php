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
 * @package     Phrozn\Registry
 * @subpackage  Container
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
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
    }

    /**
     * Mark bundle as installed
     *
     * @param string $bundle Bundle identifier.
     *
     * @return \Phrozn\Registry\Container
     */
    public function markAsInstalled($bundle)
    {
        $installed = $this->get('installed');
        $installed[] = $bundle;
        $this
            ->set('installed', $installed)
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
}

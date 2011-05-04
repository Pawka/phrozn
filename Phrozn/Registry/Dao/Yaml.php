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
 * @subpackage  Dao
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry\Dao;
use Phrozn\Registry\Dao;

/**
 * YAML data store.
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
class Yaml
    extends Base
    implements Dao
{
    /**
     * Save current registry container
     *
     * @return \Phorzn\Registry\Dao
     */
    public function save()
    {
        if ($path = $this->getProjectPath()) {
            file_put_contents($path . '/.registry', serialize($this->getContainer()));
        }
        return $this;
    }

    /**
     * Read registry data into container.
     *
     * @return \Phrozn\Registry\Container
     */
    public function read()
    {
        $path = $this->getProjectPath() . '/.registry';
        if (!is_readable($path)) {
            $this->getContainer()->set(null, array());
        }

        $this->getContainer()
             ->set(null, unserialize(file_get_contents($path)));
        return $this;
    }
}

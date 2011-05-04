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
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Registry;
use Phrozn\Registry\Has,
    Phrozn\Registry\Dao\Yaml as DefaultDao;

/**
 * Phrozn registry container
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 */
class Container
    implements Has\Dao
{
    /**
     * @var \Phrozn\Registry\Dao
     */
    private $dao;

    /**
     * Initialize container
     *
     * @param \Phrozn\Registry\Dao $dao Data access object
     *
     * @return void
     */
    public function __construct($dao = null)
    {
        if (null === $dao) {
            $this->setDao(new DefaultDao());
        }
    }

    /**
     * Persist current instance
     *
     * @return \Phrozn\Registry\Container
     */
    public function save()
    {
        $this->getDao()->save();
        return $this;
    }

    /**
     * (Re)read current container from DAO
     *
     * @return \Phrozn\Registry\Container
     */
    public function read()
    {
        $this->getDao()->read();
        return $this;
    }

    /**
     * Set DAO.
     *
     * @param \Phrozn\Registry\Dao $dao Data access object
     *
     * @return \Phrozn\Has\Dao
     */
    public function setDao(\Phrozn\Registry\Dao $dao)
    {
        $this->dao = $dao;
        return $this;
    }

    /**
     * Get DAO.
     *
     * @return \Phrozn\Has\Dao
     */
    public function getDao()
    {
        return $this->dao;
    }

}

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
    Phrozn\Registry\Dao\Serialized as DefaultDao;

/**
 * Phrozn registry container
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 */
class Container
    implements \Serializable,
               \ArrayAccess,
               Has\Dao,
               Has\Values
{
    /**
     * @var \Phrozn\Registry\Dao
     */
    private $dao;

    /**
     * Registry values
     * @var array
     */
    private $values;

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
            $dao = new DefaultDao();
        }
        $this->setDao($dao);
    }

    /**
     * Magic set method
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->values[$name] = new Item($name, $value);
    }

    /**
     * Magic get method
     *
     * @param string $name Property name
     *
     * @return void
     */
    public function __get($name)
    {
        if (!isset($this->values[$name])) {
            $this->values[$name] = new Item($name);
        }
        return $this->values[$name];
    }

    /**
     * Magic unset method
     *
     * @param string $name Member name
     *
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
        }
    }

    /**
     * ArrayAccess method - check whether offset exists
     *
     * @param mixed $offset Offset to check
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * ArrayAccess method - get offset value
     *
     * @param mixed $offset Offset to check
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset)->getValue();
    }

    /**
     * ArrayAccess method - set the offset value
     *
     * @param mixed $offset Offset to check
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess method - reset value at offset
     *
     * @param mixed $offset Offset to check
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->values[$offset]);
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
        $container = $this->getDao()->read();
        $this->setValues($container->getValues());
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
        if ($dao instanceof Dao) {
            $dao->setContainer($this);
        }
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

    /**
     * Serialize container
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->getValues());
    }

    /**
     * Unserialize container
     *
     * @param string $serialized Serialized data
     *
     * @return array
     */
    public function unserialize($serialized)
    {
        $this->setValues(unserialize($serialized));
        return $this->getValues();
    }

    /**
     * Set values attribute.
     *
     * @param array $values Values attribute
     *
     * @return \Phrozn\Has\Values
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Get values attribute.
     *
     * @return \Phrozn\Has\Values
     */
    public function getValues()
    {
        return $this->values;
    }

}

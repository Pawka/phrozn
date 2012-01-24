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
 * @author      Victor Farazdagi
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
    protected $values;

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

        $this->init(); // allow sub-classes to initialize
    }

    /**
     * Initialize container
     *
     * @return void
     */
    public function init()
    {}

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

    /**
     * Set property value
     *
     * @param string $name Property name
     * @param mixed $value Property value
     *
     * @return \Phrozn\Registry\Container
     */
    public function set($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * Get property value
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($this->values[$name])) {
            return null;
        }
        return $this->values[$name];
    }

    /**
     * Unset property
     *
     * @param string $name Member name
     *
     * @return void
     */
    public function remove($name)
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
        return $this->get($offset);
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
        $this->set($offset, $value);
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
        $this->remove($offset);
    }
}

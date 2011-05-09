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

/**
 * Phrozn registry item
 *
 * @category    Phrozn
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 */
class Item
{
    /**
     * Item name
     */
    private $name;

    /**
     * Children object
     * @var array
     */
    private $children;

    /**
     * Current object's value
     * @var mixed
     */
    public $value;

    /**
     * Init registry item
     *
     * @return void
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
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
        $this->expand($name, $value);
    }

    /**
     * Magic get method
     *
     * @param string $name Member name
     *
     * @return \Phrozn\Registry\Item
     */
    public function __get($name)
    {
        $this->expand($name);
        return $this->children[$name];
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
        if (isset($this->children[$name])) {
            unset($this->children[$name]);
        }
    }

    /**
     * Convert object into string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Expand given object with named item
     *
     * @param string $name Member name
     * @return void
     */
    private function expand($name, $value = null)
    {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Item($name, $value);
        } 
        
        if (null !== $value) { // we are resetting value
            if ($this->children[$name] instanceof Item){
                $this->children[$name]->value = $value;
            }
        }
    }

}

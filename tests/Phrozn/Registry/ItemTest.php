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
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;
use Phrozn\Registry\Item,
    Phrozn\Registry\Container;

/**
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 */
class ItemTest 
    extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $item = new Item('bundle');

        // auto-initialization
        $this->assertInstanceOf('Phrozn\Registry\Item', $item->foo->bar);
        $this->assertNull($item->foo->bar->value);
        $this->assertNull($item->foo->bar->getValue());
        $item->foo->bar = 42;
        $this->assertSame('42', (string)$item->foo->bar);
        $this->assertSame(42, $item->foo->bar->value);
        $this->assertSame(42, $item->foo->bar->getValue());

        // unset
        unset($item->foo); // reset the parent path
        $this->assertNull($item->foo->bar->value);
        $this->assertNull($item->foo->bar->getValue());
    }

    public function testArrayAccess()
    {
        $item = new Item('installed');
        $val = array(
            'id' => 'some.bundle.id'
        );
        $this->assertFalse(isset($item['some.bundle.name']));
        $item['some.bundle.name'] = $val;
        $this->assertTrue(isset($item['some.bundle.name']));
        $this->assertSame($val, $item['some.bundle.name']);
        $this->assertSame('some.bundle.id', $item['some.bundle.name']['id']);
        unset($item['some.bundle.name']);
        $this->assertFalse(isset($item['some.bundle.name']));
    }

}

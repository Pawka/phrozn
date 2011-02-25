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
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;
use Phrozn\Config;

/**
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 */
class ConfigTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testInitialization()
    {
        $config = new Config(dirname(__FILE__) . '/../../configs/');
        $this->assertTrue(isset($config['phrozn']));
        $this->assertTrue(isset($config['phrozn']['author']));
        $this->assertSame('Victor Farazdagi', $config['phrozn']['author']);
        $this->assertTrue(isset($config['processors']['twig']));

        $config['phrozn'] = 'updated';
        $this->assertSame('updated', $config['phrozn']);
        unset($config['phrozn']);
        $this->assertFalse(isset($config['phrozn']));


        $this->assertTrue(isset($config['paths']));
        $this->assertTrue(isset($config['paths']['bin']));
        $this->assertTrue(isset($config['paths']['configs']));
        $this->assertTrue(isset($config['paths']['app']));
        $this->assertTrue(isset($config['paths']['lib']));


    }


}

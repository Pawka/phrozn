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
 * @package     Phrozn
 * @author      Victor Farazdagi
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

        $config['phrozn'] = 'updated';
        $this->assertSame('updated', $config['phrozn']);
        unset($config['phrozn']);
        $this->assertFalse(isset($config['phrozn']));


        $this->assertTrue(isset($config['paths']));
        $this->assertTrue(isset($config['paths']['skeleton']));
        $this->assertTrue(isset($config['paths']['library']));
        $this->assertTrue(isset($config['paths']['configs']));
    }

    public function testLoadFile()
    {
        $config = new Config(dirname(__FILE__) . '/../../configs/phrozn.yml');
        $this->assertInstanceOf('\Phrozn\Config', $config);
        $this->assertTrue(isset($config['phrozn']['command']['name']));
    }


}

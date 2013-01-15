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
 * @category    PhroznTest
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Provider;
use \PHPUnit_Framework_TestCase as TestCase,
    Phrozn\Provider\Factory;


/**
 * @category    PhroznTest
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 */
class ProviderTest
    extends TestCase
{
    public function testProviderCreation()
    {
        $input = dirname(__FILE__) . '/data/LoadFromFile.txt';
        $options = array(
            'input' => basename($input),
        );
        $factory = new Factory();
        $provider = $factory->create('LoadFromFile', $options);
        $provider->setProjectPath(dirname($input));
        $this->assertSame(file_get_contents($input), $provider->get());
    }

    public function testPluginProviderCreation()
    {
        require_once dirname(__FILE__) . '/data/PluginProvider.php';

        $input = dirname(__FILE__) . '/data/LoadFromFile.txt';
        $options = array(
            'input' => basename($input),
        );
        $factory = new Factory();
        $provider = $factory->create('PluginProvider', $options);
        $provider->setProjectPath(dirname($input));
        $this->assertSame(file_get_contents($input), $provider->get());
    }

    public function testProviderNotFoundException()
    {
        $this->setExpectedException('RuntimeException', "Provider of type 'SpeedyJoe' not found..");
        $input = dirname(__FILE__) . '/data/LoadFromFile.txt';
        $options = array();

        $factory = new Factory();
        $provider = $factory->create('SpeedyJoe', $options);
        $provider->setProjectPath(dirname($input));
    }
}


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
    Phrozn\Provider\LoadFromFile as Provider;

/**
 * @category    PhroznTest
 * @package     Phrozn\Provider
 * @author      Victor Farazdagi
 */
class LoadFromFileProviderTest
    extends TestCase
{
    public function testProvider()
    {
        $input = dirname(__FILE__) . '/data/LoadFromFile.txt';
        $options = array(
            'input' => basename($input),
        );
        $provider = new Provider($options);
        $provider->setProjectPath(dirname($input));
        $this->assertSame(file_get_contents($input), $provider->get());
    }

    public function testProviderNoInputSet()
    {
        $this->setExpectedException('RuntimeException', 'No input file provided');
        $provider = new Provider();
        $provider->get();
    }

    public function testProviderInvalidInputFile()
    {
        $this->setExpectedException('RuntimeException', 'Input file "/some-unreadable-file" not found');
        $provider = new Provider();
        $provider->setConfig(array(
            'input' => 'some-unreadable-file'
        ))->get();
    }
}

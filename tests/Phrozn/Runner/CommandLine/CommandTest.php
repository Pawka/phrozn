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
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Runner\CommandLine;
use Phrozn\Runner\CommandLine\Command;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class CommandTest
    extends \PHPUnit_Framework_TestCase
{
    private $path;

    public function setUp()
    {
        // paths
        $base = realpath(dirname(__FILE__) . '/../../../../') . '/';
        $this->path = $base . 'configs/';
    }

    public function testInitialize()
    {
        $cmd = new Command($this->path . 'commands/init.yml');
        $config = $this->getConfigData('init');

        $this->assertInstanceOf('\Phrozn\Runner\CommandLine\Command', $cmd);
        $this->assertTrue(isset($cmd['docs']));

        $this->assertSame($config['name'], $cmd['docs']['name']);
        $this->assertSame($config['summary'], $cmd['docs']['summary']);

        $this->assertFalse(isset($cmd['ping']));
        $cmd['ping'] = 'pong';
        $this->assertTrue(isset($cmd['ping']));
        $this->assertSame('pong', $cmd['ping']);
        unset($cmd['ping']);
        $this->assertFalse(isset($cmd['ping']));
    }

    private function getConfigData($commandName)
    {
        $configFile = $this->path . 'commands/' . $commandName . '.yml';
        $cmdConfig = new Command($configFile);
        return $cmdConfig['docs'];
    }

    private function getParseResult($cmd)
    {
        $args = explode(' ', $cmd);
        return $this->parser->parse(count($args), $args);
    }

}

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
use Phrozn\Runner\CommandLine\Parser,
    Phrozn\Runner\CommandLine\Command;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class ParserTest
    extends \PHPUnit_Framework_TestCase
{
    private $paths = array();

    public function setUp()
    {
        // paths
        $base = realpath(dirname(__FILE__) . '/../../../../') . '/';
        $this->paths = array(
            'app'       => $base,
            'bin'       => $base . 'bin/',
            'lib'       => $base . 'library/',
            'configs'   => $base . 'configs/',
            'skeleton'  => $base . 'skeleton/',
        );
    }
    public function testParser()
    {
        $parser = new Parser($this->paths);
        $this->assertTrue(isset($parser->name));
        $this->assertTrue(isset($parser->description));
        $this->assertTrue(isset($parser->options));
        $this->assertTrue(isset($parser->version));
        $this->assertTrue(isset($parser->commands));
    }


    public function testSubcommands()
    {
        $this->subcommand('init');
        $this->subcommand('clobber');
        $this->subcommand('up');
        $this->subcommand('help');
    }

    private function subcommand($commandName)
    {
        $parser = new Parser($this->paths);
        $this->assertTrue(isset($parser->commands));
        $this->assertTrue(isset($parser->commands[$commandName]));
        $cmdParsed = $parser->commands[$commandName];

        // phr init
        $this->assertSame($commandName, $cmdParsed->name);
        $configFile = $this->paths['configs'] . 'commands/' . $commandName . '.yml';
        $cmdConfig = new Command($configFile);
        $cmdConfig = $cmdConfig['command'];

        // unset incompatible properties
        $excludedOptions = array('doc_name');

        if (isset($cmdConfig['options'])) {
            foreach ($cmdConfig['options'] as $argName => $argVals) {
                $this->assertTrue(isset($cmdParsed->options[$argName]));
                foreach ($argVals as $name => $val) {
                    if (!in_array($name, $excludedOptions)) {
                        $this->assertSame($val, $cmdParsed->options[$argName]->{$name});
                    }
                }
            }
        }
        if (isset($cmdConfig['arguments'])) {
            foreach ($cmdConfig['arguments'] as $argName => $argVals) {
                $this->assertTrue(isset($cmdParsed->args[$argName]));
                foreach ($argVals as $name => $val) {
                    $this->assertSame($val, $cmdParsed->args[$argName]->{$name});
                }
            }
        }
    }

    public function testToString()
    {
        ob_start();
        $parser = new Parser($this->paths);
        echo $parser;
        $out = ob_get_clean();
        $this->assertTrue(strpos($out, 'OPTIONS:') !== false);
        $this->assertTrue(strpos($out, 'ARGUMENTS:') !== false);
        $this->assertTrue(strpos($out, 'COMMANDS:') !== false);
    }
}

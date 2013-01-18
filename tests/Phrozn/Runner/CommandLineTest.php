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

namespace PhroznTest\Runner;
use Phrozn\Runner\CommandLine as Runner,
    Phrozn\Autoloader as Loader,
    Phrozn\Outputter\TestOutputter as Outputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class CommandLineTest
    extends \PHPUnit_Framework_TestCase
{
    const STDOUT = '/tmp/CommandLineTestOut';

    private $phr;
    private $outputter;
    private $runner;

    public function setUp()
    {
        $this->phr = realpath(__DIR__ . '/../../../bin/phrozn.php');
        $this->outputter = new Outputter($this);
        $this->fout = fopen(self::STDOUT, 'w+');
        define('STDOUT', $this->fout);

        require_once 'Phrozn/Autoloader.php';
        $loader = Loader::getInstance();
        $this->runner = new Runner($loader);
    }

    public function tearDown()
    {
        fclose($this->fout);
    }

    public function testRunHelpUpdate()
    {
        $this->runner->run(array(
            $this->phr,
            'help',
            'update',
        ));
        $path = dirname(__FILE__) . '/output/phr-help-update.out';
        $original = file_get_contents($path);
        $rendered = implode("", array_slice(file(self::STDOUT), 1));
        $this->assertSame($original, $rendered);
    }

    public function testRunHUpdate()
    {
        $this->runner->run(array(
            $this->phr,
            '-h',
        ));
        $path = dirname(__FILE__) . '/output/phr-help.out';
        $original = file_get_contents($path);
        $rendered = implode("", array_slice(file(self::STDOUT), 1));
        $this->assertSame($original, $rendered);
    }

    /**
     * @group cur
     */
    public function testRunWithNoArgs()
    {
        $this->runner->run(array(
            $this->phr,
        ));
        $path = dirname(__FILE__) . '/output/phr-no-params.out';
        $this->assertSame(
            file_get_contents($path), file_get_contents(self::STDOUT));
    }

    private function getParseResult($cmd)
    {
        $args = explode(' ', $cmd);
        return $this->parser->parse(count($args), $args);
    }


}

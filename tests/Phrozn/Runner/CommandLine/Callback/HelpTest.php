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
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Runner\CommandLine\Callback;
use Phrozn\Runner\CommandLine\Callback\Help as Callback,
    Phrozn\Runner\CommandLine as Runner,
    Phrozn\Runner\CommandLine\Parser,
    Phrozn\Outputter\TestOutputter as Outputter,
    Phrozn\Runner\CommandLine\Command;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class HelpTest
    extends \PHPUnit_Framework_TestCase
{
    private $runner;
    private $outputter;
    private $parser;
    private $paths;

    public function setUp()
    {
        // paths
        $base = realpath(dirname(__FILE__) . '/../../../../../') . '/';
        $this->paths = $paths = array(
            'app'       => $base,
            'bin'       => $base . 'bin/',
            'lib'       => $base . 'library/',
            'configs'   => $base . 'configs/',
            'skeleton'  => $base . 'skeleton/',
        );

        $this->outputter = new Outputter($this);
        $runner = new Callback();
        $data['paths'] = $paths; // inject paths
        $runner
            ->setOutputter($this->outputter)
            ->setConfig($data);
        $this->runner = $runner;

        // setup parser
        $this->parser = new Parser($paths);
    }

    public function testHelpInitialize()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr help initialize");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testHelpInitializeVerbose()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr help initialize --verbose");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
        $out->assertInLogs('Examples:');
    }

    public function testHelpInitializeV()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr help initialize -v");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
        $out->assertInLogs('Examples:');
    }

    public function testHelpTopicNotFound()
    {
        $out = $this->outputter;

        $topic = 'superduper';
        $result = $this->getParseResult("phr help {$topic}");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs("Help topic '$topic' not found");
    }

    public function testHelpInit()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr help init");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testHelpClobber()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('clobber');

        $result = $this->getParseResult("phr help clobber");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testHelpUpdate()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('up');

        $result = $this->getParseResult("phr help update");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testHelpUp()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('up');

        $result = $this->getParseResult("phr help up");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testQuestionInit()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr ? init");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testQuestionInitialize()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('init');

        $result = $this->getParseResult("phr ? initialize");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testQuestionClobber()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('clobber');

        $result = $this->getParseResult("phr ? clobber");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testQuestionUp()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('up');

        $result = $this->getParseResult("phr ? up");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testQuestionUpdate()
    {
        $out = $this->outputter;

        $config = $this->getConfigData('up');

        $result = $this->getParseResult("phr ? update");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs($config['name']);
        $out->assertInLogs($config['summary']);
    }

    public function testMainQuestion()
    {
        $out = $this->outputter;

        $result = $this->getParseResult("phr ?");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs("Type 'phrozn help <command>' for help on a specific command.");
        $out->assertInLogs("Type 'phrozn ? help' for help on using help.");
    }

    public function testMainHelpOption()
    {
        $out = $this->outputter;

        $result = $this->getParseResult("phr --help");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs("Type 'phrozn help <command>' for help on a specific command.");
        $out->assertInLogs("Type 'phrozn ? help' for help on using help.");
    }

    public function testMainHOption()
    {
        $out = $this->outputter;

        $result = $this->getParseResult("phr -h");

        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs("Type 'phrozn help <command>' for help on a specific command.");
        $out->assertInLogs("Type 'phrozn ? help' for help on using help.");
    }

    private function getConfigData($commandName)
    {
        $configFile = $this->paths['configs'] . 'commands/' . $commandName . '.yml';
        $cmdConfig = new Command($configFile);
        return $cmdConfig['docs'];
    }

    private function getParseResult($cmd)
    {
        $args = explode(' ', $cmd);
        return $this->parser->parse(count($args), $args);
    }

}

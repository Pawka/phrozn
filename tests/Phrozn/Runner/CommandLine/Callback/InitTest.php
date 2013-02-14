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
use Phrozn\Runner\CommandLine\Callback\Init as Callback,
    Phrozn\Runner\CommandLine as Runner,
    Phrozn\Runner\CommandLine\Parser,
    Phrozn\Outputter\TestOutputter as Outputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class InitTest
    extends \PHPUnit_Framework_TestCase
{
    private $runner;
    private $outputter;
    private $parser;

    public function setUp()
    {
        // paths
        $base = realpath(dirname(__FILE__) . '/../../../../../') . '/';
        $paths = array(
            'app'       => $base,
            'bin'       => $base . 'bin/',
            'lib'       => $base . 'library/',
            'configs'   => $base . 'configs/',
            'skeleton'  => $base . 'skeleton/',
        );

        // purge project directory
        $this->removeProjectDirectory();

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

    public function tearDown()
    {
        // purge project directory
        $this->removeProjectDirectory();
    }

    public function testInitWithExplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';

        $result = $this->getParseResult("phr init {$path}/.phrozn");

        $this->assertFalse(is_dir($path . '/.phrozn'));
        $this->assertFalse(is_dir($path . '/.phrozn/entries'));
        $this->assertFalse(is_dir($path . '/.phrozn/layouts'));
        $this->assertFalse(is_dir($path . '/.phrozn/media'));
        $this->assertFalse(is_dir($path . '/.phrozn/scripts'));
        $this->assertFalse(is_dir($path . '/.phrozn/styles'));
        $this->assertFalse(is_readable($path . '/.phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs('Initializing new project');
        $out->assertInLogs('[ADDED]   /config.yml');
        $out->assertInLogs('[ADDED]   /entries/index.twig');
        $out->assertInLogs('[ADDED]   /layouts/default.twig');
        $out->assertInLogs("Project path: {$path}");

        $this->assertTrue(is_dir($path . '/.phrozn'));
        $this->assertTrue(is_dir($path . '/.phrozn/entries'));
        $this->assertTrue(is_dir($path . '/.phrozn/layouts'));
        $this->assertTrue(is_dir($path . '/.phrozn/media'));
        $this->assertTrue(is_dir($path . '/.phrozn/scripts'));
        $this->assertTrue(is_dir($path . '/.phrozn/styles'));
        $this->assertTrue(is_readable($path . '/.phrozn/config.yml'));
    }

    public function testInitWithImplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chdir($path));

        $result = $this->getParseResult("phr init");

        $this->assertFalse(is_dir($path . '/.phrozn'));
        $this->assertFalse(is_dir($path . '/.phrozn/entries'));
        $this->assertFalse(is_dir($path . '/.phrozn/layouts'));
        $this->assertFalse(is_dir($path . '/.phrozn/media'));
        $this->assertFalse(is_dir($path . '/.phrozn/scripts'));
        $this->assertFalse(is_dir($path . '/.phrozn/styles'));
        $this->assertFalse(is_readable($path . '/.phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs('Initializing new project');
        $out->assertInLogs('[ADDED]   /config.yml');
        $out->assertInLogs('[ADDED]   /entries/index.twig');
        $out->assertInLogs('[ADDED]   /layouts/default.twig');
        $out->assertInLogs("Project path: {$path}");

        $this->assertTrue(is_dir($path . '/.phrozn'));
        $this->assertTrue(is_dir($path . '/.phrozn/entries'));
        $this->assertTrue(is_dir($path . '/.phrozn/layouts'));
        $this->assertTrue(is_dir($path . '/.phrozn/media'));
        $this->assertTrue(is_dir($path . '/.phrozn/scripts'));
        $this->assertTrue(is_dir($path . '/.phrozn/styles'));
        $this->assertTrue(is_readable($path . '/.phrozn/config.yml'));
    }

    public function testInitWithExplicitNonAbsolutePath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/';
        $this->assertTrue(chdir($path));

        $result = $this->getParseResult("phr init project/.phrozn");

        $this->assertFalse(is_dir($path . '/project/.phrozn'));
        $this->assertFalse(is_dir($path . '/project/.phrozn/entries'));
        $this->assertFalse(is_dir($path . '/project/.phrozn/layouts'));
        $this->assertFalse(is_dir($path . '/project/.phrozn/media'));
        $this->assertFalse(is_dir($path . '/project/.phrozn/scripts'));
        $this->assertFalse(is_dir($path . '/project/.phrozn/styles'));
        $this->assertFalse(is_readable($path . '/project/.phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();


        $out->assertInLogs('Initializing new project');
        $out->assertInLogs('[ADDED]   /config.yml');
        $out->assertInLogs('[ADDED]   /entries/index.twig');
        $out->assertInLogs('[ADDED]   /layouts/default.twig');
        $out->assertInLogs("Project path: {$path}");

        $this->assertTrue(is_dir($path . '/project/.phrozn'));
        $this->assertTrue(is_dir($path . '/project/.phrozn/entries'));
        $this->assertTrue(is_dir($path . '/project/.phrozn/layouts'));
        $this->assertTrue(is_dir($path . '/project/.phrozn/media'));
        $this->assertTrue(is_dir($path . '/project/.phrozn/scripts'));
        $this->assertTrue(is_dir($path . '/project/.phrozn/styles'));
        $this->assertTrue(is_readable($path . '/project/.phrozn/config.yml'));
    }

    public function testInitFailed()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';

        $this->assertTrue(is_dir($path));

        $result = $this->getParseResult("phr init {$path}/.phrozn");

        $this->assertFalse(is_dir($path . '/.phrozn'));
        mkdir($path . '/.phrozn');
        $this->assertTrue(is_dir($path . '/.phrozn'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs('Initializing new project');
        $out->assertInLogs("[FAIL]    Project directory '.phrozn' already exists..");
    }

    public function testInitFailedPermissions()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chmod($path, 0555));

        $this->assertTrue(is_dir($path));

        $result = $this->getParseResult("phr init {$path}/.phrozn");

        $this->assertFalse(is_dir($path . '/.phrozn'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $out->assertInLogs('Initializing new project');
        $out->assertInLogs("[FAIL]    Error creating project directory..");

    }

    private function getParseResult($cmd)
    {
        $args = explode(' ', $cmd);
        return $this->parser->parse(count($args), $args);
    }


    private function removeProjectDirectory()
    {
        $path = dirname(__FILE__) . '/project';
        chmod($path, 0777);

        $path .= '/.phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
        }
    }
}

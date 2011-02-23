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
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Runner\CommandLine\Callback;
use Phrozn\Runner\CommandLine\Callback\Init as Callback,
    Phrozn\Runner\CommandLine as Runner,
    Phrozn\Runner\CommandLine\Parser;

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
        
        $this->outputter = new Outputter();
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
        $path = dirname(__FILE__) . '/project';

        $result = $this->getParseResult("phr init {$path}");

        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_dir($path . '/_phrozn/site'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains(
            'Initializing new project'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   config.yml'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   site/README'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   views/layouts/default.twig'));
        $this->assertTrue($this->outputter->contains(
            "Project path: {$path}"));

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_dir($path . '/_phrozn/site'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));
    }

    public function testInitWithImplicitPath()
    {
        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chdir($path));

        $result = $this->getParseResult("phr init");

        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_dir($path . '/_phrozn/site'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains(
            'Initializing new project'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   config.yml'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   site/README'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   views/layouts/default.twig'));
        $this->assertTrue($this->outputter->contains(
            "Project path: {$path}"));

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_dir($path . '/_phrozn/site'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));
    }

    public function testInitWithExplicitNonAbsolutePath()
    {
        $path = dirname(__FILE__) . '/';
        $this->assertTrue(chdir($path));

        $result = $this->getParseResult("phr init project");

        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_dir($path . '/_phrozn/site'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setParseResult($result)
            ->execute();


        $this->assertTrue($this->outputter->contains(
            'Initializing new project'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   config.yml'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   site/README'));
        $this->assertTrue($this->outputter->contains(
            '[ADDED]   views/layouts/default.twig'));
        $this->assertTrue($this->outputter->contains(
            "Project path: {$path}"));

        $this->assertTrue(is_dir($path . '/project/_phrozn'));
        $this->assertTrue(is_dir($path . '/project/_phrozn/site'));
        $this->assertTrue(is_readable($path . '/project/_phrozn/config.yml'));
    }

    public function testInitFailed()
    {
        $path = dirname(__FILE__) . '/project';

        $this->assertTrue(is_dir($path));

        $result = $this->getParseResult("phr init {$path}");

        $this->assertFalse(is_dir($path . '/_phrozn'));
        mkdir($path . '/_phrozn');
        $this->assertTrue(is_dir($path . '/_phrozn'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains(
            'Initializing new project'));
        $this->assertTrue($this->outputter->contains(
            "[FAIL]    Project directory '_phrozn' already exists.."));
    }

    public function testInitFailedPermissions()
    {
        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chmod($path, 0555));

        $this->assertTrue(is_dir($path));

        $result = $this->getParseResult("phr init {$path}");

        $this->assertFalse(is_dir($path . '/_phrozn'));

        $this->runner
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains(
            'Initializing new project'));
        $this->assertTrue($this->outputter->contains(
            "[FAIL]    Error creating project directory.."));

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

        $path .= '/_phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
        }
    }
}

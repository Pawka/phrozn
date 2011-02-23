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
use Phrozn\Runner\CommandLine\Callback\Clobber as Callback,
    Phrozn\Runner\CommandLine as Runner,
    Phrozn\Runner\CommandLine\Parser;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class ClobberTest 
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

    public function testClobberYesWithExplicitPath()
    {
        $path = dirname(__FILE__) . '/project';
        mkdir($path . '/_phrozn');
        touch($path . '/_phrozn/config.yml');


        $result = $this->getParseResult("phr clobber {$path}");

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains('Purging project data'));
        $this->assertTrue($this->outputter->contains("Located project folder: {$path}/_phrozn"));
        $this->assertTrue($this->outputter->contains("[DELETED]  {$path}/_phrozn"));


        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));
    }

    public function testClobberNoWithExplicitPath()
    {
        $path = dirname(__FILE__) . '/project';
        mkdir($path . '/_phrozn');
        touch($path . '/_phrozn/config.yml');


        $result = $this->getParseResult("phr clobber {$path}");

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains('Purging project data'));
        $this->assertTrue($this->outputter->contains("Located project folder: {$path}/_phrozn"));
        $this->assertTrue($this->outputter->contains("[FAIL]     Aborted.."));


        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));
    }

    public function testClobberYesWithImplicitPath()
    {
        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chdir($path));
        mkdir($path . '/_phrozn');
        touch($path . '/_phrozn/config.yml');


        $result = $this->getParseResult("phr clobber");

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains('Purging project data'));
        $this->assertTrue($this->outputter->contains("Located project folder: {$path}/_phrozn"));
        $this->assertTrue($this->outputter->contains("[DELETED]  {$path}/_phrozn"));


        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));
    }

    public function testClobberYesWithNonAbsolutePath()
    {
        $path = dirname(__FILE__) . '/project';
        $this->assertTrue(chdir($path . '/../'));
        mkdir($path . '/_phrozn');
        touch($path . '/_phrozn/config.yml');


        $result = $this->getParseResult("phr clobber project");

        $this->assertTrue(is_dir($path . '/_phrozn'));
        $this->assertTrue(is_readable($path . '/_phrozn/config.yml'));

        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();

        $this->assertTrue($this->outputter->contains('Purging project data'));
        $this->assertTrue($this->outputter->contains("Located project folder: {$path}/_phrozn"));
        $this->assertTrue($this->outputter->contains("[DELETED]  {$path}/_phrozn"));


        $this->assertFalse(is_dir($path . '/_phrozn'));
        $this->assertFalse(is_readable($path . '/_phrozn/config.yml'));
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

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
use Phrozn\Runner\CommandLine\Callback\Bundle as Callback,
    Phrozn\Runner\CommandLine as Runner,
    Phrozn\Runner\CommandLine\Parser,
    Phrozn\Outputter\TestOutputter as Outputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class BundleTest 
    extends \PHPUnit_Framework_TestCase
{
    private $runner;
    private $outputter;
    private $parser;
    private $previousPath;

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

        $path = dirname(__FILE__) . '/project';
        $this->previousPath = \getcwd();
        chdir($path);

        $this->resetProjectDirectory();
        
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
        $this->resetProjectDirectory(true);
        chdir($this->previousPath);
    }

    public function testBundleList()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle list processor.test");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Test processor plugin - used to demonstrate how');
    }

    public function testBundleListHiddenBundles()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle list");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Test processor plugin - used to demonstrate how');
    }

    public function testBundleListWrongProjectPath()
    {
        $out = $this->outputter;

        $path = '/wrong-path';
        
        $result = $this->getParseResult("phr-dev bundle list test {$path}");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    No project found at /wrong-path');

    }

    public function testBundleInfoById()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle info processor.test");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Test processor plugin - used to demonstrate how');
    }

    public function testBundleInfoByQue()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle info test");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Test processor plugin - used to demonstrate how');
    }
    
    public function testBundleInfoNotFound()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle info no-such-bundle");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    Bundle "no-such-bundle" not found..');
    }

    public function testBundleApplyByIdWithNoWithImplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';

        $result = $this->getParseResult("phr-dev bundle apply processor.test");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[FAIL]     Aborted..');
    }

    public function testBundleApplyByIdWithYesWithImplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle apply processor.test");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[OK]       Done..');

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $out->resetLogs();
        $result = $this->getParseResult("phr-dev bundle list -p test");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertNotInLogs('Test processor plugin - used to demonstrate how');

        $out->resetLogs();
        $result = $this->getParseResult("phr-dev bundle list -i test");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Test processor plugin - used to demonstrate how');
    }

    public function testBundleApplyByNameWithNoWithImplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';

        $result = $this->getParseResult("phr-dev bundle apply test");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[FAIL]     Aborted..');
    }

    public function testBundleApplyByNameWithYesWithImplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';

        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle apply test");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[OK]       Done..');

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));
    }

    public function testBundleApplyByIdWithNoWithExplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $result = $this->getParseResult("phr-dev bundle apply test {$path}");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[FAIL]     Aborted..');
    }

    public function testBundleApplyByIdWithYesWithExplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle apply test {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[OK]       Done..');

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));
    }

    public function testBundleApplyWrongProjectPath()
    {
        $out = $this->outputter;

        $path = '/wrong-path';
        
        $result = $this->getParseResult("phr-dev bundle apply test {$path}");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    No project found at /wrong-path');

    }

    public function testBundleApplyEmptyBundle()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $result = $this->getParseResult("phr-dev bundle apply empty.bundle {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    Invalid or empty bundle');
    }

    public function testBundleClobberNonInstalledBundle()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle clobber test {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    Bundle "processor.test" is NOT installed.');
    }

    public function testBundleClobberByIdWithYesWithExplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle apply test {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[OK]       Done..');

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $out->resetLogs();
        $result = $this->getParseResult("phr-dev bundle clobber test {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Bundle files are to be removed.');
        $out->assertInLogs('[OK]       Done..');

        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));
    }

    public function testBundleClobberByIdWithNoWithExplicitPath()
    {
        $out = $this->outputter;

        $path = dirname(__FILE__) . '/project';
        
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $result = $this->getParseResult("phr-dev bundle apply test {$path}");
        $this->runner
            ->setUnitTestData('yes')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Do you wish to install this bundle?');
        $out->assertInLogs('[OK]       Done..');

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));

        $out->resetLogs();
        $result = $this->getParseResult("phr-dev bundle clobber test {$path}");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('Located project folder: ' . $path . '/.phrozn');
        $out->assertInLogs('Bundle content:');
        $out->assertInLogs('./plugins/Processor/Test.php');
        $out->assertInLogs('./plugins/Site/View/Test.php');
        $out->assertInLogs('Bundle files are to be removed.');
        $out->assertInLogs("[FAIL]     Aborted..");

        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/.phrozn/plugins/Site/View/Test.php'));
    }

    public function testBundleClobberWrongProjectPath()
    {
        $out = $this->outputter;

        $path = '/wrong-path';
        
        $result = $this->getParseResult("phr-dev bundle clobber test {$path}");
        $this->runner
            ->setUnitTestData('no')
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs('[FAIL]    No project found at /wrong-path');

    }
    
    public function testNoSubActionSpecified()
    {
        $out = $this->outputter;
        $result = $this->getParseResult("phr-dev bundle");
        $this->runner
            ->setOutputter($out)
            ->setParseResult($result)
            ->execute();
        $out->assertInLogs("[FAIL]    No sub-command specified. Use 'phr ? bundle' for more info.");
    }

    private function getParseResult($cmd)
    {
        $args = explode(' ', $cmd);
        return $this->parser->parse(count($args), $args);
    }

    private function resetProjectDirectory($justPurge = false)
    {
        $path = dirname(__FILE__) . '/project';
        chmod($path, 0777);

        $path .= '/.phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
        }
        if (false === $justPurge) {
            $path = dirname($path);
            $phrdev = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/bin/phrozn.php';
            `{$phrdev} init {$path}`;
        }
    }
}

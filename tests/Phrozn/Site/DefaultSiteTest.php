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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site;
use Phrozn\Site\DefaultSite as Site,
    Phrozn\Outputter\TestOutputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class DefaultSiteTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // purge project directory
        $this->cleanOutputDirectory();
        
    }

    public function tearDown()
    {
        // purge project directory
        $this->cleanOutputDirectory();
    }

    public function testSiteCompilation()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/';
        $site = new Site($path, $path . 'site');

        $outputter = new TestOutputter($this);
        $this->assertFalse(is_readable($path . 'site/2011-02-24-default-site.html'));
        $site
            ->setOutputter($outputter)
            ->compile();
        $this->assertTrue(is_readable($path . 'site/2011-02-24-default-site.html'));
    }

    public function testSiteCompilationEntriesNotFound()
    {
        $path = dirname(__FILE__) . '/not/found/';
        $site = new Site($path, $path . 'site');
        $outputter = new TestOutputter($this);

        $this->setExpectedException('Exception', "Entries folder not found");

        $site
            ->setOutputter($outputter)
            ->compile();
    }

    public function testSiteCompilationWithCustomOutputter()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/';
        $site = new Site($path, $path . 'site');
        $outputter = new TestOutputter($this);

        $this->assertFalse(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertFalse(is_readable($path . 'site/2011-02-24-factory-test.html'));
        $this->assertFalse(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));
        $site
            ->setOutputter($outputter)
            ->compile();
        $this->assertTrue(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-24-factory-test.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));

        $outputter->assertInLogs("2011-02-24-wrong-file-type.wrong: Page of type 'wrong' not found..");
        $outputter->assertInLogs("2011-02-21-phrozn-generated-first-page-today.twig parsed");
        $outputter->assertInLogs("2011-02-24-default-site.twig parsed");
        $outputter->assertInLogs("2011-02-24-factory-test.twig parsed");

        $parsed = file_get_contents($path .  'site/2011-02-24-default-site.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-default-site.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-24-factory-test.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-factory-test.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-21-phrozn-generated-first-page-today.html');
        $loaded = file_get_contents($path .  'test/2011-02-21-phrozn-generated-first-page-today.html');
        $this->assertSame($loaded, $parsed);
    }

    public function testSiteCompilationProjectGuess()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/';
        $site = new Site(realpath($path . '/../'), $path . 'site');
        $outputter = new TestOutputter($this);

        $this->assertFalse(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertFalse(is_readable($path . 'site/2011-02-24-factory-test.html'));
        $this->assertFalse(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));
        $site
            ->setOutputter($outputter)
            ->compile();
        $this->assertTrue(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-24-factory-test.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));

        $outputter->assertInLogs("2011-02-24-wrong-file-type.wrong: Page of type 'wrong' not found..");
        $outputter->assertInLogs("2011-02-21-phrozn-generated-first-page-today.twig parsed");
        $outputter->assertInLogs("2011-02-24-default-site.twig parsed");
        $outputter->assertInLogs("2011-02-24-factory-test.twig parsed");

        $parsed = file_get_contents($path .  'site/2011-02-24-default-site.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-default-site.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-24-factory-test.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-factory-test.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-21-phrozn-generated-first-page-today.html');
        $loaded = file_get_contents($path .  'test/2011-02-21-phrozn-generated-first-page-today.html');
        $this->assertSame($loaded, $parsed);
    }

    private function cleanOutputDirectory()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/site';
        if (is_dir($path)) {
            `rm -rf {$path}`;
            mkdir($path);
            touch($path . '/README');
        }
    }
}

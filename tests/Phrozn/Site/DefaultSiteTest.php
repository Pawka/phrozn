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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
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
        $path = dirname(__FILE__) . '/project/.phrozn/';
        $in  = $path;
        $out = $path . 'site/';
        $site = new Site($in, $out);

        $outputter = new TestOutputter($this);

        // sanity checks
        $this->assertFileNotExists($out . '2011-02-24-default-site.html');
        $this->assertFileNotExists($out . 'markdown.html');
        $this->assertFileNotExists($out . 'textile.html');
        $this->assertFileNotExists($out . 'media/skipped.bak');

        $site
            ->setOutputter($outputter)
            ->compile();

        // test existence of generated files
        $this->assertFileExists($out . '2011-02-24-default-site.html',
            "Process Twig files into HTML files");
        $this->assertFileExists($out . 'markdown.html',
            "Process Markdown files into HTML files");
        $this->assertFileExists($out . 'textile.html',
            "Process Textile files into HTML files");
        $this->assertFileNotExists($out . 'media/skipped.bak',
            "Skip files whose name match at least one of config.yml skip regexes");


        // test processor renderers
        $expected = file_get_contents($path . 'test/markdown.html');
        $rendered = file_get_contents($out . 'markdown.html');
        $this->assertSame($expected, $rendered);

        $expected = file_get_contents($path . 'test/textile.html');
        $rendered = file_get_contents($out . 'textile.html');
        $this->assertSame($expected, $rendered);

        $outputter->assertInLogs('entries/skipped.twig SKIPPED',
            "Skip files with skip:true in the frontmatter : expected to find '%s' in logs:\n\n%s");
    }

    public function testSiteCompilationEntriesNotFound()
    {
        $path = dirname(__FILE__) . '/not/found/';
        $site = new Site($path, $path . 'site');
        $outputter = new TestOutputter($this);

        $this->setExpectedException('RuntimeException', "Entries folder not found");

        $site
            ->setOutputter($outputter)
            ->compile();
    }

    /**
     * @medium
     */
    public function testSiteCompilationWithCustomOutputter()
    {
        $path = dirname(__FILE__) . '/project/.phrozn/';
        $site = new Site($path, $path . 'site');
        $outputter = new TestOutputter($this);

        $this->assertFalse(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertFalse(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));
        $site
            ->setOutputter($outputter)
            ->compile();
        $this->assertTrue(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));

        $outputter->assertInLogs("2011-02-24-wrong-file-type.wrong written");
        $outputter->assertInLogs("2011-02-21-phrozn-generated-first-page-today.twig parsed");
        $outputter->assertInLogs("2011-02-24-default-site.twig parsed");

        $parsed = file_get_contents($path .  'site/2011-02-24-default-site.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-default-site.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-21-phrozn-generated-first-page-today.html');
        $loaded = file_get_contents($path .  'test/2011-02-21-phrozn-generated-first-page-today.html');
        $this->assertSame($loaded, $parsed);
    }

    public function testSiteCompilationProjectGuess()
    {
        $path = dirname(__FILE__) . '/project/.phrozn/';
        $site = new Site(realpath($path . '/../'), $path . 'site');
        $outputter = new TestOutputter($this);

        $this->assertFalse(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertFalse(is_readable($path . 'site/media/img/test.png'));
        $this->assertFalse(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));
        $site
            ->setOutputter($outputter)
            ->compile();

        $this->assertTrue(is_readable($path . 'site/2011-02-24-default-site.html'));
        $this->assertTrue(is_readable($path . 'site/2011-02-21-phrozn-generated-first-page-today.html'));
        $this->assertTrue(is_readable($path . 'site/media/img/test.png'));

        $outputter->assertInLogs("2011-02-24-wrong-file-type.wrong written");
        $outputter->assertInLogs("2011-02-21-phrozn-generated-first-page-today.twig parsed");
        $outputter->assertInLogs("2011-02-24-default-site.twig parsed");

        $parsed = trim(file_get_contents($path .  'site/media/img/test.png'));
        $loaded = 'PNG Image Pretender';
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-24-default-site.html');
        $loaded = file_get_contents($path .  'test/2011-02-24-default-site.html');
        $this->assertSame($loaded, $parsed);

        $parsed = file_get_contents($path .  'site/2011-02-21-phrozn-generated-first-page-today.html');
        $loaded = file_get_contents($path .  'test/2011-02-21-phrozn-generated-first-page-today.html');
        $this->assertSame($loaded, $parsed);
    }

    private function cleanOutputDirectory()
    {
        $path = dirname(__FILE__) . '/project/.phrozn/site';
        if (is_dir($path)) {
            `rm -rf {$path}`;
            mkdir($path);
            touch($path . '/README');
        }
    }
}

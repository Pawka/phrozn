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
        // purge project's output directory
        $this->cleanOutputDirectory();
    }

    public function tearDown()
    {
        // purge project's output directory
        $this->cleanOutputDirectory();
    }

    protected function getMockProjectPath()
    {
        return dirname(__FILE__) . '/project/';
    }

    public function testSiteCompilation()
    {
        $path = $this->getMockProjectPath();
        $in  = $path . '.phrozn/';
        $out = $path . 'public/';

        $site = new Site($in, $out);
        $outputter = new TestOutputter($this);

        // sanity checks
        $this->assertFileNotExists($out . '2011-02-24-default-site.html');
        $this->assertFileNotExists($out . 'markdown.html');
        $this->assertFileNotExists($out . 'textile.html');
        $this->assertFileNotExists($out . 'media/img/test.png');
        $this->assertFileNotExists($out . 'media/skipped.bak');

        $site
            ->setOutputter($outputter)
            ->compile();

        // test existence of generated files
        // -> redundant with assertFileEquals below
        $this->assertFileExists($out . '2011-02-24-default-site.html',
            "Process Twig files into HTML files");
        $this->assertFileExists($out . 'markdown.html',
            "Process Markdown files into HTML files");
        $this->assertFileExists($out . 'textile.html',
            "Process Textile files into HTML files");
        $this->assertFileExists($out . 'media/img/test.png',
            "Copy files in media folder");

        // test processor renderers
        $this->assertFileEquals($path.'expected/2011-02-24-default-site.html', $out.'2011-02-24-default-site.html',
            "Compile Twig files as expected");
        $this->assertFileEquals($path.'expected/markdown.html', $out.'markdown.html',
            "Compile Markdown files as expected");
        $this->assertFileEquals($path.'expected/textile.html', $out.'textile.html',
            "Compile Textile files as expected");

        // test copy integrity
        $this->assertFileEquals($in.'media/img/test.png', $out.'media/img/test.png',
            "Fully copy file contents");
        $this->assertFileEquals($in.'favicon.ico', $out.'favicon.ico',
            "Copy files specified in config.yml `copy` array");

        // test skipping
        $this->assertFileNotExists($out . 'media/skipped.bak',
            "Skip files whose name match at least one of config.yml skip regexes");
        $outputter->assertInLogs('entries/skipped.twig SKIPPED',
            "Skip files with skip:true in the frontmatter : expected to find '%s' in logs:\n\n%s");

        // test outputter
        $outputter->assertInLogs("2011-02-24-default-site.twig parsed");
        $outputter->assertInLogs("2011-02-24-default-site.html written");
        $outputter->assertInLogs("2011-02-24-wrong-file-type.wrong written");
        //$outputter->assertNotInLogs("2011-02-24-wrong-file-type.wrong parsed");
    }

    public function testSiteCompilationEntriesNotFound()
    {
        $path = dirname(__FILE__) . '/not/found/';
        $site = new Site($path, $path . 'public');
        $outputter = new TestOutputter($this);

        $this->setExpectedException('RuntimeException', "Entries folder not found");

        $site
            ->setOutputter($outputter)
            ->compile();
    }

    public function testSiteCompilationProjectGuess()
    {
        $path = $this->getMockProjectPath();
        $in  = $path . '.phrozn/'; // will guess that
        $out = $path . 'public/';

        $site = new Site($path, $out);
        $outputter = new TestOutputter($this);

        // sanity checks
        $this->assertFileNotExists($out . '2011-02-24-default-site.html');
        $this->assertFileNotExists($out . 'media/img/test.png');

        $site
            ->setOutputter($outputter)
            ->compile();

        // It may be enough here to simply test that no Exception is thrown
        // Still... TESTS TESTS TESTS

        // test existence of generated files
        $this->assertFileExists($out . '2011-02-24-default-site.html',
            "Process Twig files into HTML files");
        $this->assertFileExists($out . 'media/img/test.png',
            "Copy files in media folder");

        // test copy integrity
        $this->assertFileEquals($in.'media/img/test.png', $out.'media/img/test.png',
            "Fully copy file contents");

        // test processor renderers
        $this->assertFileEquals($path.'expected/2011-02-24-default-site.html', $out.'2011-02-24-default-site.html',
            "Compile Twig files as expected");
    }

    private function cleanOutputDirectory()
    {
        $outputDir = $this->getMockProjectPath() . 'public/';
        if (is_dir($outputDir)) {
            `rm -rf {$outputDir}`; // DANGER ZONE ™
            mkdir($outputDir);
            touch($outputDir . '/README');
        }
    }
}

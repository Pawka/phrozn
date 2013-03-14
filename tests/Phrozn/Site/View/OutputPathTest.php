<?php
/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this view except in compliance with the License.
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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site\View;
use Phrozn\Site\View\OutputPath,
    Phrozn\Site\View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class OutputPathTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testEntriesPaths()
    {
        $view = new View\Twig();
        $view->setInputRootDir('/var/www/phrozn-test/');

        $view
            ->setInputFile('/var/www/phrozn-test/entries/some-entry.twig')
            ->setOutputDir('/var/www/output');
        $path = new OutputPath\Entry($view);
        $this->assertSame('/var/www/output/some-entry.html', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/entries/sub/folder/some-entry.twig')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/sub/folder/some-entry.html', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/sub/folder/some-entry.twig')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/sub/folder/some-entry.html', $path->get());
    }

    public function testStylesPaths()
    {
        $view = new View\Less();
        $view->setInputRootDir('/var/www/phrozn-test/');

        $view
            ->setInputFile('/var/www/phrozn-test/styles/some-entry.less')
            ->setOutputDir('/var/www/output');
        $path = new OutputPath\Style($view);
        $this->assertSame('/var/www/output/styles/some-entry.css', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/styles/sub/folder/some-entry.less')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/styles/sub/folder/some-entry.css', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/sub/folder/some-entry.less')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/sub/folder/some-entry.css', $path->get());
    }

    public function testScriptsPaths()
    {
        $view = new View\Less();
        $view->setInputRootDir('/var/www/phrozn-test/');

        $view
            ->setInputFile('/var/www/phrozn-test/scripts/some-entry.js')
            ->setOutputDir('/var/www/output');
        $path = new OutputPath\Script($view);
        $this->assertSame('/var/www/output/scripts/some-entry.js', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/scripts/sub/folder/some-entry.js')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/scripts/sub/folder/some-entry.js', $path->get());

        $view
            ->setInputFile('/var/www/phrozn-test/sub/folder/some-entry.js')
            ->setOutputDir('/var/www/output');
        $path->setView($view);
        $this->assertSame('/var/www/output/sub/folder/some-entry.js', $path->get());
    }

}

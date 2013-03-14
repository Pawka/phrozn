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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site\View;
use Phrozn\Site\View\Markdown as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class MarkdownTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/../project/.phrozn/entries/markdown.markdown';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Markdown', $view);
    }

    public function testViewRendering()
    {
        $entry = dirname(__FILE__) . '/../project/.phrozn/entries/markdown.markdown';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/markdown.html';
        $view = new View($entry);

        $rendered = $view->render();
        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $entry = dirname(__FILE__) . '/../project/.phrozn/entries/markdown.markdown';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/markdown.html';
        $path = dirname(__FILE__) . '/out/';
        $view = new View($entry, $path);
        $view->setInputRootDir(dirname(__FILE__) . '/../project/.phrozn');

        $this->assertSame('markdown.markdown', basename($view->getInputFile()));
        $this->assertSame('markdown.html', basename($view->getOutputFile()));

        @unlink($path . 'markdown.html');
        $this->assertFalse(is_readable($path . 'markdown.html'));

        $rendered = $view->compile();

        $this->assertTrue(is_readable($path . 'markdown.html'));

        $loaded = file_get_contents($html);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . 'markdown.html');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        unlink($path . 'markdown.html');
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('RuntimeException', "View input file not specified");
        $view = new View();

        $rendered = $view->render(array());

    }


}

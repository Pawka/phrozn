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
use Phrozn\Site\View\Plain as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class PlainTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/entries/htaccess';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Plain', $view);
    }

    public function testViewRendering()
    {
        $content = dirname(__FILE__) . '/entries/htaccess';
        $view = new View($content);

        $rendered = $view->render();
        $loaded = file_get_contents($content . '.parsed');

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $content = dirname(__FILE__) . '/entries/htaccess';
        $path = dirname(__FILE__) . '/out';
        $view = new View($content, $path);
        $view->setInputRootDir(dirname(__FILE__));

        $this->assertSame('htaccess', basename($view->getInputFile()));
        $this->assertSame('.htaccess', basename($view->getOutputFile()));

        @unlink($path . '/.htaccess');
        $this->assertFalse(is_readable($path . '/.htaccess'));

        $rendered = $view->compile();

        $this->assertTrue(is_readable($path . '/.htaccess'));

        $loaded = file_get_contents($content . '.parsed');
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '/.htaccess');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        unlink($path . '/.htaccess');
    }

    public function testNoFrontMatter()
    {
        $content = dirname(__FILE__) . '/entries/htaccess.parsed';
        $view = new View($content);

        $rendered = $view->render();
        $loaded = file_get_contents($content);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('RuntimeException', "View input file not specified");
        $view = new View();

        $rendered = $view->render();
    }


}

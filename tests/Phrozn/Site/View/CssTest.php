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
use Phrozn\Site\View\Css as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class CssTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/styles/style.css';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Css', $view);
    }

    public function testViewRendering()
    {
        $css = dirname(__FILE__) . '/styles/style.css';
        $view = new View($css);

        $rendered = $view->render();
        $loaded = file_get_contents($css);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $css = dirname(__FILE__) . '/styles/style.css';
        $path = dirname(__FILE__) . '/out';
        $view = new View($css, $path);
        $view->setInputRootDir(dirname(__FILE__));

        $this->assertSame('style.css', basename($view->getInputFile()));
        $this->assertSame('style.css', basename($view->getOutputFile()));

        @unlink($path . '/styles/style.css');
        $this->assertFalse(is_readable($path . '/styles/style.css'));

        $rendered = $view->compile();

        $this->assertTrue(is_readable($path . '/styles/style.css'));

        $loaded = file_get_contents($css);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '/styles/style.css');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        unlink($path . '/styles/style.css');
    }

    public function testNoFrontMatter()
    {
        $css = dirname(__FILE__) . '/styles/style.css';
        $view = new View($css);

        $rendered = $view->render();
        $loaded = file_get_contents($css);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('RuntimeException', "View input file not specified");
        $view = new View();

        $rendered = $view->render();
    }


}

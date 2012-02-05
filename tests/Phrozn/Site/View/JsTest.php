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
use Phrozn\Site\View\Js as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class JsTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/scripts/jquery/test.js';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Js', $view);
    }

    public function testViewRendering()
    {
        $jsIn = dirname(__FILE__) . '/scripts/jquery/test.js';
        $jsOut = dirname(__FILE__) . '/scripts/jquery/test.js';
        $view = new View($jsIn);

        $rendered = $view->render();
        $loaded = file_get_contents($jsOut);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $jsIn = dirname(__FILE__) . '/scripts/jquery/test.js';
        $jsOut = dirname(__FILE__) . '/scripts/jquery/test.js';
        $path = dirname(__FILE__) . '/out';
        $view = new View($jsIn, $path);

        $this->assertSame('test.js', basename($view->getInputFile()));
        $this->assertSame('test.js', basename($view->getOutputFile()));

        @unlink($path . '/scripts/jquery/test.js');
        $this->assertFalse(is_readable($path . '/scripts/jquery/test.js'));

        $rendered = $view->compile();

        $this->assertTrue(is_readable($path . '/scripts/jquery/test.js'));

        $loaded = file_get_contents($jsOut);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '/scripts/jquery/test.js');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        unlink($path . '/scripts/jquery/test.js');
    }

    public function testNoFrontMatter()
    {
        $jsIn = dirname(__FILE__) . '/scripts/jquery/test.js';
        $jsOut = dirname(__FILE__) . '/scripts/jquery/test.js';
        $view = new View($jsIn);

        $rendered = $view->render();
        $loaded = file_get_contents($jsOut);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('RuntimeException', "View input file not specified");
        $view = new View();

        $rendered = $view->render();
    }


}

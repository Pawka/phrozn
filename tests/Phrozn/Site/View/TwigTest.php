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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site\View;
use Phrozn\Site\View\Twig as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class TwigTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/entries/2011-02-24-create.twig';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Twig', $view);
    }

    public function testViewRendering()
    {
        $twig = dirname(__FILE__) . '/entries/2011-02-24-render.twig';
        $html = dirname(__FILE__) . '/entries/2011-02-24-render.html';
        $view = new View($twig);

        $vars = array('the_answer' => 42);
        $rendered = $view->render($vars);

        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $twig = dirname(__FILE__) . '/entries/2011-02-24-compile.twig';
        $html = dirname(__FILE__) . '/entries/2011-02-24-compile.html';
        $path = dirname(__FILE__) . '/out/'; 
        $view = new View($twig, $path);

        $this->assertSame('2011-02-24-compile.twig', basename($view->getInputFile()));
        $this->assertSame('2011-02-24-compile.html', basename($view->getOutputFile()));

        @unlink($path . '2011-02-24-compile.html');
        $this->assertFalse(is_readable($path . '2011-02-24-compile.html'));

        $vars = array('the_answer' => 42);
        $rendered = $view->compile($vars);

        $this->assertTrue(is_readable($path . '2011-02-24-compile.html'));

        $loaded = file_get_contents($html);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '2011-02-24-compile.html');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        unlink($path . '2011-02-24-compile.html');
    }

    public function testNoFrontMatter()
    {
        $twig = dirname(__FILE__) . '/entries/2011-02-24-no-front-matter.twig';
        $html = dirname(__FILE__) . '/entries/2011-02-24-no-front-matter.html';
        $view = new View($twig);

        $vars = array('the_answer' => 42);
        $rendered = $view->render($vars);

        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('Exception', "View input file not specified");
        $view = new View();

        $rendered = $view->render(array());

    }


}

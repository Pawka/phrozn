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
        $in = dirname(__FILE__) . '/../project/.phrozn/entries/2011-02-24-create.twig';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\View\Twig', $view);
    }

    public function testViewRenderingWithFrontMatter()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/twig-child-with-fm.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/twig-child-with-fm.html';
        $view = new View($twig);

        $vars = array('the_answer' => 42);
        $rendered = $view->render($vars);

        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewRenderingWithIncludes()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/twig-include.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/twig-include.html';
        $view = new View($twig);

        $vars = array('the_answer' => 42);
        $rendered = $view->render($vars);

        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testViewCompiling()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/2011-02-24-compile.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/2011-02-24-compile.html';
        $path = dirname(__FILE__) . '/out/';
        $view = new View($twig, $path);
        $view->setInputRootDir(dirname(__FILE__) . '/../project/.phrozn');

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

    public function testViewCompilingPermalinkSetParametrizedIndexAdded()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/compile-permalink-append-index.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/compile-permalink.html';
        $path = dirname(__FILE__) . '/out/';
        $view = new View($twig, $path);

        $this->assertSame('compile-permalink-append-index.twig', basename($view->getInputFile()));
        $this->assertSame('index.html', basename($view->getOutputFile()));

        @unlink($path . '/2011/03/17/testing-permalink-generation/index.html');
        $this->assertFalse(is_readable($path . '/2011/03/17/testing-permalink-generation/index.html'));

        $vars = array('the_answer' => 42);
        $rendered = $view->compile($vars);

        $this->assertTrue(is_readable($path . '/2011/03/17/testing-permalink-generation/index.html'));

        $loaded = file_get_contents($html);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '/2011/03/17/testing-permalink-generation/index.html');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        @unlink($path . '/2011/03/17/testing-permalink-generation/index.html');
    }

    public function testViewCompilingPermalinkSetParametrized()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/compile-permalink-parametrized.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/compile-permalink.html';
        $path = dirname(__FILE__) . '/out/';
        $view = new View($twig, $path);

        $this->assertSame('compile-permalink-parametrized.twig', basename($view->getInputFile()));
        $this->assertSame('testing-permalink-generation.html', basename($view->getOutputFile()));

        @unlink($path . '/2011/03/17/testing-permalink-generation.html');
        $this->assertFalse(is_readable($path . '/2011/03/17/testing-permalink-generation.html'));

        $vars = array('the_answer' => 42);
        $rendered = $view->compile($vars);

        $this->assertTrue(is_readable($path . '/2011/03/17/testing-permalink-generation.html'));

        $loaded = file_get_contents($html);
        $this->assertSame(trim($loaded), trim($rendered));

        // load from out
        $loaded = file_get_contents($path . '/2011/03/17/testing-permalink-generation.html');
        $this->assertSame(trim($loaded), trim($rendered));

        // cleanup
        @unlink($path . '/2011/03/17/testing-permalink-generation.html');
    }


    public function testNoFrontMatter()
    {
        $twig = dirname(__FILE__) . '/../project/.phrozn/entries/2011-02-24-no-front-matter.twig';
        $html = dirname(__FILE__) . '/../project/.phrozn/entries/2011-02-24-no-front-matter.html';
        $view = new View($twig);

        $vars = array('the_answer' => 42);
        $rendered = $view->render($vars);

        $loaded = file_get_contents($html);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('RuntimeException', "View input file not specified");
        $view = new View();

        $rendered = $view->render(array());

    }


}

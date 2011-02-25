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
 * @package     Phrozn\Site\Layout
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site\Layout;
use Phrozn\Site\Layout\DefaultLayout as Layout,
    Phrozn\Processor;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\Layout
 * @author      Victor Farazdagi
 */
class DefaultLayoutTest 
    extends \PHPUnit_Framework_TestCase
{
    private $processor;

    public function setUp()
    {}

    public function testTwigConstructorInjection()
    {
        $layoutPath = dirname(__FILE__) . '/layouts/test1.twig';
        $processor = new Processor\Twig();

        $layout = new Layout($layoutPath, $processor);
        $out = $layout->render(array('content' => 'CONTENT'));

        $compiled = file_get_contents(dirname($layoutPath) . '/test1.html');
        $this->assertSame($compiled, $out);
    }

    public function testTwigSetterInjection()
    {
        $layoutPath = dirname(__FILE__) . '/layouts/test1.twig';
        $processor = new Processor\Twig();

        $layout = new Layout($layoutPath, $processor);
        $layout
            ->setSourcePath($layoutPath)
            ->setProcessor($processor);
        $out = $layout->render(array('content' => 'CONTENT'));

        $compiled = file_get_contents(dirname($layoutPath) . '/test1.html');
        $this->assertSame($compiled, $out);
    }

    public function testTwigLayoutFileNotReadable()
    {
        $this->setExpectedException('Exception', "Layout file 'not-found.twig' cannot be read.");

        $layoutPath = dirname(__FILE__) . '/layouts/not-found.twig';
        $processor = new Processor\Twig();

        $layout = new Layout($layoutPath, $processor);
        $out = $layout->render(array('content' => 'CONTENT'));
    }

    public function testTwigLayoutFileNotSpecified()
    {
        $this->setExpectedException('Exception', "Layout's source file not specified.");

        $layoutPath = dirname(__FILE__) . '/layouts/not-found.twig';
        $processor = new Processor\Twig();

        $layout = new Layout();
        $layout->setProcessor($processor);
        $out = $layout->render(array('content' => 'CONTENT'));

    }

}

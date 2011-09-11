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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Processor;
use Phrozn\Processor\Twig as Processor;

/**
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class TwigTest 
    extends \PHPUnit_Framework_TestCase
{
    private $path;

    public function setUp()
    {
        $this->path = dirname(__FILE__) . '/templates/';
    }

    /**
     * @group cur
     */
    public function testRender()
    {
        $processor = new Processor();
        $tpl = file_get_contents($this->path . 'tpl1.twig');
        $rendered = $processor->render($tpl, array(
            'a_variable' => 'Aha!',
            'navigation' => array(
                array(
                    'href'      => 'link1',
                    'caption'   => 'caption1'
                ),
                array(
                    'href'      => 'link1',
                    'caption'   => 'caption1'
                )
            )
        ));
        $static = file_get_contents($this->path . 'tpl1.html');
        $this->assertSame(trim($static), trim($rendered));
    }

    public function testRenderConstructorInjection()
    {
        $processor = new Processor(array(
            'cache' => dirname(__FILE__) . '/templates/',
        ));
        $tpl = file_get_contents($this->path . 'tpl1.twig');
        $rendered = $processor->render($tpl, array(
            'a_variable' => 'Aha!',
            'navigation' => array(
                array(
                    'href'      => 'link1',
                    'caption'   => 'caption1'
                ),
                array(
                    'href'      => 'link1',
                    'caption'   => 'caption1'
                )
            )
        ));
        
        $static = file_get_contents(dirname(__FILE__) . '/templates/tpl1.html');
        $this->assertSame(trim($static), trim($rendered));
    }

    public function testTwigInclude()
    {
        $processor = $this->getProcessor($this->path . 'twig-include.twig');
        $rendered = $processor->render(null, array(
            'a_variable' => 'Aha!',
            'boxes' => array(
                array(
                    'size'      => 'huge',
                    'title'     => 'phelephant'
                ),
                array(
                    'size'      => 'tiny',
                    'title'     => 'mouse'
                )
            )
        ));
        
        $static = file_get_contents(dirname(__FILE__) . '/templates/twig-include.html');
        $this->assertSame(trim($static), trim($rendered));
    }

    /**
     * @group cur
     */
    public function testInheritedTemplates()
    {
        $processor = $this->getProcessor($this->path . 'twig-child.twig');
        $rendered = $processor->render(null, array(
            'a_variable' => 'Aha!',
            'boxes' => array(
                array(
                    'size'      => 'huge',
                    'title'     => 'phelephant'
                ),
                array(
                    'size'      => 'tiny',
                    'title'     => 'mouse'
                )
            )
        ));
        
        $static = file_get_contents(dirname(__FILE__) . '/templates/twig-inherit.html');
        $this->assertSame(trim($static), trim($rendered));
    }

}

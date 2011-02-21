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
    /**
     * @var \Phrozn\Processor\Twig
     */
    private $proc;

    public function setup()
    {
        $this->proc = new Processor();
        $this->proc->setConfig(array(
            'cache' => dirname(__FILE__) . '/_files/',
            'loader_paths'  => array(
                dirname(__FILE__) . '/_files/'
            )
        ));
    }

    public function testRender()
    {
        $rendered = $this->proc->render('tpl1.twig', array(
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
        
        $static = file_get_contents(dirname(__FILE__) . '/_files/tpl1.html');
        $this->assertSame(trim($static), trim($rendered));
    }

}

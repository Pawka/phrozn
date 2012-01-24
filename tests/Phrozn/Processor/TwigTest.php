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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Processor;
use Phrozn\Processor\Twig as Processor;

class TestProcessor
    extends Processor
{
    public function cleanup()
    {
        $this->getEnvironment()->clearCacheFiles();
    }
}

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

    public function testRender()
    {
        $processor = $this->getProcessor($this->path . 'tpl1.twig');
        $template = file_get_contents($this->path . 'tpl1.twig');
        $rendered = $processor->render($template, array(
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
        $cache_dir = dirname(__FILE__) . '/templates/cache/';
        $processor = $this->getProcessor(
            $this->path . 'tpl1.twig', array(
                'cache' => $cache_dir,
            )
        );
        $template = file_get_contents($this->path . 'tpl1.twig');
        $rendered = $processor->render($template, array(
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
        $processor->cleanup();      // purge cache
        `touch ${cache_dir}README`; // cache clears all files
    }

    /**
     * @group cur
     */
    public function testTwigInclude()
    {
        $processor = $this->getProcessor($this->path . 'twig-include.twig');
        $template = file_get_contents($this->path . 'twig-include.twig');
        $rendered = $processor->render($template, array(
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

    public function testInheritedTemplates()
    {
        $processor = $this->getProcessor($this->path . 'twig-child.twig');
        $template = file_get_contents($this->path . 'twig-child.twig');
        $rendered = $processor->render($template, array(
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


    public function testStripFrontmatter()
    {
        $processor = $this->getProcessor($this->path . 'twig-child-with-fm.twig');
        $template = file_get_contents($this->path . 'twig-child-with-fm.twig');
        $rendered = $processor->render($template, array(
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

    private function getProcessor($inputFile, $extraOpts = array())
    {
        $options = array(
            'phr_template_filename' => basename($inputFile),
            'phr_template_dir'      => dirname($inputFile),
        );
        return new TestProcessor(array_merge($options, $extraOpts));
    }


}

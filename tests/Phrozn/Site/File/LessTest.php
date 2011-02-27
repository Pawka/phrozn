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
 * @package     Phrozn\Site\File
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site\File;
use Phrozn\Site\File\Less as File;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\File
 * @author      Victor Farazdagi
 */
class LessTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testFileCreation()
    {
        $in = dirname(__FILE__) . '/styles/style.less';
        $out = dirname(__FILE__) . '/out';
        $page = new File($in , $out);

        $this->assertInstanceOf('\Phrozn\Site\File\Less', $page);
    }

    public function testFileRendering()
    {
        $less = dirname(__FILE__) . '/styles/style.less';
        $css = dirname(__FILE__) . '/styles/style.css';
        $page = new File($less);

        $rendered = $page->render();
        $loaded = file_get_contents($css);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testFileCompiling()
    {
        $less = dirname(__FILE__) . '/styles/style.less';
        $css = dirname(__FILE__) . '/styles/style.css';
        $path = dirname(__FILE__) . '/out'; 
        $page = new File($less, $path);

        $this->assertSame('style.less', $page->getName());

        @unlink($path . '/styles/style.css');
        $this->assertFalse(is_readable($path . '/styles/style.css'));

        $rendered = $page->compile();

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
        $less = dirname(__FILE__) . '/styles/style.less';
        $css = dirname(__FILE__) . '/styles/style.css';
        $page = new File($less);

        $rendered = $page->render();
        $loaded = file_get_contents($css);

        $this->assertSame(trim($loaded), trim($rendered));
    }

    public function testNoSourcePathSpecified()
    {
        $this->setExpectedException('Exception', "Source file not specified");
        $page = new File();

        $rendered = $page->render();
    }


}

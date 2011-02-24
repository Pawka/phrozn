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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Site;
use Phrozn\Site\PageFactory as Factory,
    Phrozn\Page;

/**
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class PageFactoryTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testImplicitHtmlPageCreation()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . '2011-02-24-factory-test.twig';
        $output = realpath($path . '/../site');
        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->createPage();

        $page->setDestinationPath($output);

        $this->assertInstanceOf('\Phrozn\Site\Page\Twig', $page);
    }

    public function testNoFrontMatter()
    {
        $this->setExpectedException('Exception', 'Page front matter not found');

        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . '2011-02-24-no-front-matter.twig';

        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->createPage();
    }

    public function testSourceFileCanNotBeRead()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . 'not-found.twig';

        $this->setExpectedException('Exception',
            "Page source file cannot be read: {$input}");

        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->createPage();
    }

    public function testNoSourceFileSpecified()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . 'not-found.twig';

        $this->setExpectedException('Exception',
            "Page's source file not specified");

        $factory = new Factory();
        $page = $factory
                    ->createPage();
    }

    public function testWrongFileType()
    {
        $this->setExpectedException('Exception',
           "Page of type 'wrong' not found");

        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . '2011-02-24-wrong-file-type.twig';

        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->createPage();
    }

}

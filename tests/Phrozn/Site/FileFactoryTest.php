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
use Phrozn\Site\FileFactory as Factory,
    Phrozn\File;

/**
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class FileFactoryTest 
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testImplicitHtmlFileCreation()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . '2011-02-24-factory-test.twig';
        $output = realpath($path . '/../site');
        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->create();

        $page->setDestinationPath($output);

        $this->assertInstanceOf('\Phrozn\Site\File\Twig', $page);
    }

    public function testSourceFileCanNotBeRead()
    {
        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . 'not-found.twig';

        $this->setExpectedException('Exception',
            "File source file cannot be read: {$input}");

        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->create();
    }

    public function testWrongFileType()
    {
        $this->setExpectedException('Exception',
           "File of type 'wrong' not found");

        $path = dirname(__FILE__) . '/project/_phrozn/entries/';
        $input = $path . '2011-02-24-wrong-file-type.wrong';

        $factory = new Factory();
        $page = $factory
                    ->setSourcePath($input)
                    ->create();
    }

}

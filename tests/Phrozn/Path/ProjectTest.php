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
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;
use Phrozn\Path,
    Phrozn\Path\Project as ProjectPath,
    \PHPUnit_Framework_TestCase as TestCase;

/**
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 */
class ProjectTest 
    extends TestCase
{
    public function testPathCalculation()
    {
        $basePath = dirname(__FILE__) . '/project/';
        $path = new ProjectPath($basePath);
        $this->assertSame($basePath . '.phrozn', $path->get());

        $this->assertSame(
            $basePath . '.phrozn', 
            $path->set($basePath . 'sub')->get());
        $this->assertSame(
            $basePath . '.phrozn',
            $path->set($basePath . 'sub/')->get());
        $this->assertSame(
            $basePath . '.phrozn',
            $path->set($basePath . 'sub/folder')->get());
        $this->assertSame(
            $basePath . '.phrozn',
            $path->set($basePath . 'sub/folder/')->get());
        $this->assertSame(
            $basePath . 'sub/folder/subsub/.phrozn',
            $path->set($basePath . 'sub/folder/subsub')->get());
        $this->assertSame(
            $basePath . 'sub/folder/subsub/.phrozn',
            $path->set($basePath . 'sub/folder/subsub/')->get());
        $this->assertSame(
            $basePath . 'sub/folder/subsub/.phrozn',
            $path->set($basePath . 'sub/folder/subsub/.phrozn')->get());
        $this->assertSame(
            $basePath . 'sub/folder/subsub/.phrozn',
            $path->set($basePath . 'sub/folder/subsub/.phrozn/')->get());

        $this->assertSame(null, $path->set("/var")->get());
    }

    public function testPathNotSetException()
    {
        $this->setExpectedException('Exception', 'Path not set');
        $path = new ProjectPath();
        $path->get();
    }
}


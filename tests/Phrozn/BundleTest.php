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
use Phrozn\Bundle,
    Phrozn\Config,
    Phrozn\Path\Project as ProjectPath;

/**
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 */
class BundleTest 
    extends \PHPUnit_Framework_TestCase
{
    public function testGetInfo()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $bundle = new Bundle('test', new Config($bundlesConfig));

        $data = $bundle->getInfo();
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('author', $data);

        $this->assertSame('processor.test', $data['id']);
        $this->assertSame('Test', $data['name']);
        $this->assertSame('Victor Farazdagi', $data['author']);

        $this->assertSame('processor.test', $bundle->getInfo('id'));
        $this->assertSame('Test', $bundle->getInfo('name'));
        $this->assertSame('Victor Farazdagi', $bundle->getInfo('author'));
    }

    public function testGetInfoById()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $bundle = new Bundle('processor.test', new Config($bundlesConfig));

        $this->assertSame('processor.test', $bundle->getInfo('id'));

        $data = $bundle->getInfo();
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('author', $data);

        $this->assertSame('processor.test', $data['id']);
        $this->assertSame('Test', $data['name']);
    }

    public function testGetInfoBundleNotFound()
    {
        $this->setExpectedException('Exception', 'Bundle "wrong.bundle" not found..');
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $bundle = new Bundle('wrong.bundle', new Config($bundlesConfig));
        $bundle->getInfo();
    }

    /**
     * @group cur
     */
    public function testListBundleFilesByName()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $bundle = new Bundle('test', new Config($bundlesConfig));
        $files = $bundle->getFiles();
        $this->assertFileInBundle('./plugins/Site/View/Test.php', $files);
        $this->assertFileInBundle('./plugins/Processor/Test.php', $files);
    }

    public function testExtractByName()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundle = new Bundle('test', new Config($bundlesConfig));

        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        @unlink(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        @unlink(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
    }

    private function assertFileInBundle($que, $files)
    {
        $result = false;
        foreach ($files as $file) {
            if ($file['filename'] == $que) {
                $result = true;
                break;
            }
        }
        $this->assertTrue($result);
    }

}

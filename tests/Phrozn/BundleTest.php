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
 * @package     Phrozn
 * @author      Victor Farazdagi
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
 * @medium
 */
class BundleTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->resetProjectDirectory();
    }

    public function tearDown()
    {
        $this->resetProjectDirectory(true);
    }

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

    public function testListBundleFilesByName()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $bundle = new Bundle('test', new Config($bundlesConfig));
        $files = $bundle->getFiles();
        $this->assertFileInBundle('./plugins/Site/View/Test.php', $files);
        $this->assertFileInBundle('./plugins/Processor/Test.php', $files);
    }

    public function testExtractByUri()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundlePath = 'https://github.com/farazdagi/phrozn-bundles/raw/master/processor.test.tgz';

        $bundle = new Bundle($bundlePath, new Config($bundlesConfig));
        $this->assertSame($bundle->getInputFile(), $bundlePath);

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');

        $this->assertFalse(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/plugins/Site/View/Test.php'));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');
    }

    public function testExtractByFileNameCustom()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundlePath = dirname(__FILE__) . '/Bundle/bundles/mybundle.tgz';

        $bundle = new Bundle($bundlePath, new Config($bundlesConfig));
        $this->assertSame($bundle->getInputFile(), $bundlePath);

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');

        $this->assertFalse(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/plugins/Site/View/Test.php'));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');
    }

    public function testExtractByFileNameConventional()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundlePath = dirname(__FILE__) . '/Bundle/bundles/processor.test.tgz';

        $bundle = new Bundle($bundlePath, new Config($bundlesConfig));
        $this->assertSame($bundle->getInputFile(), $bundlePath);

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');

        $this->assertFalse(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/plugins/Site/View/Test.php'));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');
    }

    public function testExtractById()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundle = new Bundle('processor.test', new Config($bundlesConfig));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');

        $this->assertFalse(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/plugins/Site/View/Test.php'));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');
    }

    public function testExtractByName()
    {
        $bundlesConfig = dirname(__FILE__) . '/../../configs/bundles.yml';
        $path = new ProjectPath(dirname(__FILE__) . '/Bundle/project/');
        $bundle = new Bundle('test', new Config($bundlesConfig));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');

        $this->assertFalse(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '/plugins/Site/View/Test.php'));
        $bundle->extractTo($path);
        $this->assertTrue(file_exists($path . '/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '/plugins/Site/View/Test.php'));

        @unlink($path . '/plugins/Processor/Test.php');
        @unlink($path . '/plugins/Site/View/Test.php');
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

    public function testInvalidConfigurationObjectException()
    {
        $this->setExpectedException('Exception', 'Configuration object must be an instance of Phrozn\Config');
        $bundle = new Bundle('test', new \StdClass);
    }

    private function resetProjectDirectory($justPurge = false)
    {
        $path = dirname(__FILE__) . '/Bundle/project';
        chmod($path, 0777);

        $path .= '/.phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
        }
        if (false === $justPurge) {
            $path = dirname($path);
            `phr-dev init {$path}`;
        }
    }
}

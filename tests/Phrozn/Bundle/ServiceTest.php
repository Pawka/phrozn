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
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Bundle;
use Phrozn\Bundle\Service as BundleService,
    Phrozn\Outputter\TestOutputter,
    Phrozn\Config,
    Phrozn\Bundle,
    Phrozn\Registry\Container\Bundles as Container;

/**
 * @category    Phrozn
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 */
class ServiceTest
    extends \PHPUnit_Framework_TestCase
{
    private $phr;
    private $service;
    private $container;

    public function setUp()
    {
        $this->phr = realpath(__DIR__ . '/../../../bin/phrozn.php');
        $this->resetProjectDirectory();

        $path = dirname(__FILE__) . '/project';

        $config = new Config(dirname(__FILE__) . '/../../../configs/');
        $this->service = new BundleService($config, $path);

        $this->container = new Container();
        $this->container
            ->getDao()
            ->setProjectPath($path);
    }

    public function tearDown()
    {
        $this->resetProjectDirectory(true);
    }

    public function testListNoParams()
    {
        $bundles = $this->service->getBundles();
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertArrayHasKey('processor.hatena', $bundles);
    }

    /**
     * @medium
     */
    public function testListAll()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_ALL);
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertArrayHasKey('processor.hatena', $bundles);
    }

    /**
     * @medium
     */
    public function testListAllSearch()
    {
        // test search exact (by id)
        $bundles = $this->service->getBundles(Bundle::TYPE_ALL, 'processor.test');
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertFalse(isset($bundles['processor.hatena']));

        // test search exact (by name)
        $bundles = $this->service->getBundles(Bundle::TYPE_ALL, 'HatenaSyntax');
        $this->assertArrayHasKey('processor.hatena', $bundles);
        $this->assertFalse(isset($bundles['processor.test']));

        // test search several items
        $bundles = $this->service->getBundles(Bundle::TYPE_ALL, 'processor'); // list all processors
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertArrayHasKey('processor.hatena', $bundles);
    }

    /**
     * @large
     */
    public function testListInstalled()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED);
        $this->assertSame(array(), $bundles);

        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service
            ->setProjectPath($path)
            ->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED);

        $this->arrayHasKey('processor.test', $bundles);
        $this->arrayHasKey('id', $bundles['processor.test']);
        $this->assertSame('processor.test', $bundles['processor.test']['id']);

    }

    /**
     * @large
     */
    public function testListInstalledSearch()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED);
        $this->assertSame(array(), $bundles);

        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED, 'HatenaSyntax');
        $this->assertSame(array(), $bundles);

        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED, 'test');
        $this->arrayHasKey('processor.test', $bundles);
        $this->arrayHasKey('id', $bundles['processor.test']);
        $this->assertSame('processor.test', $bundles['processor.test']['id']);
   }

    /**
     * @large
     */
    public function testListAvailable()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_AVAILABLE);
        $this->assertTrue(isset($bundles['processor.test']));
        $this->arrayHasKey('processor.test', $bundles);
        $this->arrayHasKey('id', $bundles['processor.test']);
        $this->assertSame('processor.test', $bundles['processor.test']['id']);

        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service
            ->setProjectPath($path)
            ->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $bundles = $this->service->getBundles(Bundle::TYPE_AVAILABLE);
        $this->assertFalse(isset($bundles['processor.test']));
        $this->assertTrue(isset($bundles['processor.hatena']));
   }

    /**
     * @large
     */
    public function testListAvailableSearch()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_AVAILABLE, 'test');
        $this->assertTrue(isset($bundles['processor.test']));
        $this->assertFalse(isset($bundles['processor.hatena']));
        $this->arrayHasKey('processor.test', $bundles);
        $this->arrayHasKey('id', $bundles['processor.test']);
        $this->assertSame('processor.test', $bundles['processor.test']['id']);

        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service
            ->setProjectPath($path)
            ->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $bundles = $this->service->getBundles(Bundle::TYPE_AVAILABLE);
        $this->assertFalse(isset($bundles['processor.test']));
        $this->assertTrue(isset($bundles['processor.hatena']));
    }

    public function testSetConfigException()
    {
        $this->setExpectedException('RuntimeException', 'Configuration object must be an instance of Phrozn\Config');
        $this->service->setConfig('wrong');
    }

    public function testListWrongType()
    {
        $this->setExpectedException('RuntimeException', 'Invalid bundle type "invalid-type"');
        $bundles = $this->service->getBundles('invalid-type', 'processor'); // list all processors
    }

    /**
     * @medium
     */
    public function testGetBundleInfo()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $info = $this->service->getBundleInfo($bundle);
        $this->assertSame('processor.test', $info['id']);
        $this->assertSame('Test', $info['name']);
        $this->assertSame('Victor Farazdagi', $info['author']);
    }

    /**
     * @medium
     */
    public function testGetBundleFiles()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $files = $this->service->getBundleFiles($bundle);
        $this->assertTrue(isset($files[6]['filename']));
        $this->assertSame('./plugins/Site/View/Test.php', $files[6]['filename']);
    }

    /**
     * @medium
     */
    public function testRegistryValues()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $registry = $this->service->getRegistryContainer();
        $this->assertTrue($registry->isInstalled('processor.test'));
        $files = $registry->getFiles('processor.test');
        $this->assertTrue(isset($files[6]['filename']));
        $this->assertSame('./plugins/Site/View/Test.php', $files[6]['filename']);
    }

    /**
     * @medium
     */
    public function testRegistryValuesWithExternalContainer()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $registry = $this->service
            ->setRegistryContainer($this->container)
            ->getRegistryContainer();

        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $this->assertTrue($registry->isInstalled('processor.test'));
        $files = $registry->getFiles('processor.test');
        $this->assertTrue(isset($files[6]['filename']));
        $this->assertSame('./plugins/Site/View/Test.php', $files[6]['filename']);
    }

    public function testClobberNotInstalledExcdeption()
    {
        $this->setExpectedException('RuntimeException', 'Bundle "processor.test" is NOT installed');
        $path = dirname(__FILE__) . '/project/';

        $this->assertFalse($this->service->getRegistryContainer()->isInstalled('processor.test'));
        $this->service->clobberBundle('test');
    }


    /**
     * @large
     */
    public function testApplyOfficialBundleByName()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $bundle = 'hatenasyntax';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Hatena.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Hatena.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Hatena.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Hatena.php'));

        $this->service->clobberBundle('test');
        $this->assertFalse($this->service->getRegistryContainer()->isInstalled('processor.test'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Hatena.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Hatena.php'));
    }

    /**
     * @medium
     */
    public function testAlreadyInstalledException()
    {
        $this->setExpectedException('RuntimeException', 'Bundle "processor.test" is already installed.');
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $this->service->applyBundle($bundle);
    }

    private function resetProjectDirectory($justPurge = false)
    {
        $path = dirname(__FILE__) . '/project';
        chmod($path, 0777);

        $path .= '/.phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
        }
        if (false === $justPurge) {
            $path = dirname($path);
            `{$this->phr} init {$path}`;
        }
    }
}

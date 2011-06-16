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
 * @package     Phrozn\Bundle
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
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
class DefaultSiteTest 
    extends \PHPUnit_Framework_TestCase
{
    private $service;
    private $container;

    public function setUp()
    {
        $config = new Config(dirname(__FILE__) . '/../../../configs/');
        $this->service = new BundleService();
        $this->service->setConfig($config);

        $this->container = new Container();
        $path = dirname(__FILE__) . '/../project';
        $this->container->getDao()->setProjectPath($path);

        $this->resetProjectDirectory();
    }

    public function testListNoParams()
    {
        $bundles = $this->service->getBundles();
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertArrayHasKey('processor.hatena', $bundles);
    }

    public function testListAll()
    {
        $bundles = $this->service->getBundles(Bundle::TYPE_ALL);
        $this->assertArrayHasKey('processor.test', $bundles);
        $this->assertArrayHasKey('processor.hatena', $bundles);
    }

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

    public function testListBundles()
    {
        $this->markTestIncomplete('YOU NEED TO TEST DIFF TYPES');
        $bundles = $this->service->getBundles(Bundle::TYPE_INSTALLED);
        $bundles = $this->service->getBundles(Bundle::TYPE_AVAILABLE);
    }

    public function testSetConfigException()
    {
        $this->setExpectedException('Exception', 'Configuration object must be an instance of Phrozn\Config');
        $this->service->setConfig('wrong');
    }

    public function testListWrongType()
    {
        $this->setExpectedException('Exception', 'Invalid bundle type "invalid-type"');
        $bundles = $this->service->getBundles('invalid-type', 'processor'); // list all processors
    }

    /**
     * @group cur
     */
    public function testRegistryValues()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($path, $bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $registry = $this->service->getRegistryContainer();
        $this->assertTrue($registry->isInstalled('processor.test'));
        $files = $registry->getFiles('processor.test');
        $this->assertTrue(isset($files[6]['filename']));
        $this->assertSame('./plugins/Site/View/Test.php', $files[6]['filename']);
    }

    public function testApplyOfficialBundleByName()
    {
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($path, $bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
    }

    public function testApplyBundleFromProjectDir()
    {}

    public function testApplyBundleFromPhroznDir()
    {}

    public function testAlreadyInstalledException()
    {
        $this->setExpectedException('Exception', 
            'Bundle "processor.test" is already installed.');
        $path = dirname(__FILE__) . '/project/';
        $bundle = 'test';
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertFalse(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));
        $this->service->applyBundle($path, $bundle);
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Processor/Test.php'));
        $this->assertTrue(file_exists($path . '.phrozn/plugins/Site/View/Test.php'));

        $this->service->applyBundle($path, $bundle);
    }

    private function resetProjectDirectory()
    {
        $path = dirname(__FILE__) . '/project';
        chmod($path, 0777);

        $path .= '/.phrozn';
        if (is_dir($path)) {
            `rm -rf {$path}`;
            $path = dirname($path);
            `phr-dev init {$path}`;
        }
    }
}

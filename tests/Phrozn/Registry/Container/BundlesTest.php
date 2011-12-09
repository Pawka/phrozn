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
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;
use Phrozn\Registry\Container\Bundles as Container,
    Phrozn\Registry\Dao\Serialized as Dao,
    \PHPUnit_Framework_TestCase as TestCase;

/**
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
class BundlesTest
    extends TestCase
{
    /**
     * @var \Phrozn\Registry\Container
     */
    private $container;

    public function setUp()
    {
        $this->container = new Container();
        $path = dirname(__FILE__) . '/../project';
        $this->container->getDao()->setProjectPath($path);
    }

    public function testBundleInstall()
    {
        // container is inited with default values
        $this->assertSame(array(), $this->container->get('installed'));

        $this->assertFalse($this->container->isInstalled('test.bundle'));
        $this->container->markAsInstalled('test.bundle', array());
        $this->assertTrue($this->container->isInstalled('test.bundle'));

        $container = new Container();
        $path = dirname(__FILE__) . '/../project';
        $container
            ->getDao()
            ->setProjectPath($path);
        $this->assertSame(array(), $container->get('installed'));
        $container->read();
        $this->assertSame(array('test.bundle'), $container->get('installed'));
    }
}

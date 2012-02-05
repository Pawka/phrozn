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
use Phrozn\Registry\Container,
    Phrozn\Registry\Dao\Serialized as Dao,
    \PHPUnit_Framework_TestCase as TestCase;

/**
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
class SerializedTest
    extends TestCase
{
    public function testInit()
    {
        $container = new Container();
        $dao = new Dao($container);
        $this->assertInstanceOf('Phrozn\Registry\Container', $container);
        $this->assertSame($container, $dao->getContainer());

        // test project path setting
        $this->assertNull($dao->getProjectPath());
        $projectPath = dirname(__FILE__) . '/../project/';
        $dao->setProjectPath($projectPath);
        $this->assertSame(realpath($projectPath . '/.phrozn'), $dao->getProjectPath());

        // test output file
        $this->assertSame('.registry', $dao->getOutputFile());
        $this->assertSame('.bundles', $dao->setOutputFile('.bundles')->getOutputfile());
    }

    public function testSaveRead()
    {
        $path = dirname(__FILE__) . '/../project/';

        $container = new Container();
        $container->set('bundle', 'test.me');
        $container->set('template', array(1, 2, 3));

        $dao = new Dao($container);
        $dao->setProjectPath($path);

        @unlink($path . '/.phrozn/.registry');
        $this->assertFalse(file_exists($path . '/.phrozn/.registry'));
        $dao->save();
        $this->assertTrue(file_exists($path . '/.phrozn/.registry'));
        $this->assertSame(
            file_get_contents(dirname(__FILE__) . '/../project/registry.serialized'),
            file_get_contents($path . '/.phrozn/.registry'));

        // test read
        unset($container);
        $container = new Container($dao);
        $this->assertNull($container->get('bundle'));
        $this->assertNull($container->get('template'));
        $container->read();
        $this->assertSame('test.me', $container->get('bundle'));
        $this->assertSame(array(1, 2, 3), $container->get('template'));

        @unlink($path . '/.phrozn/.registry');
    }

    public function testNoRegistryFile()
    {
        $container = new Container();
        $arr = array(1, 2, 3);
        $container->set('template', $arr);
        $dao = new Dao($container);
        $dao->setOutputFile('not-found');
        $this->assertSame($arr, $container->get('template'));
        $dao->read();
        $this->assertNull($container->getValues());
        $this->assertNull($container->get('template'));
    }

    public function testNoPathException()
    {
        $this->setExpectedException('RuntimeException', 'No project path provided');

        $container = new Container();
        $dao = new Dao($container);
        $dao->save();
    }
}

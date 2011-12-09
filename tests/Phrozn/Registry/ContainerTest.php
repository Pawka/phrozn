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
 * @author      Victor Farazdagi
 */
class ContainerTest
    extends TestCase
{
    public function testParameterSetting()
    {
        $container = new Container();
        $container->set('foo', 'bar');
        $this->assertSame('bar', $container->get('foo'));
        $container->remove('foo');
        $this->assertNull($container->get('foo'));
    }

    public function testArrayAccess()
    {
        $container = new Container();
        $this->assertFalse(isset($container['foo']));
        $container['foo'] = 'bar';
        $this->assertSame('bar', $container['foo']);
        $this->assertSame('bar', (string)$container['foo']);
        $this->assertTrue(isset($container['foo']));
        unset($container['foo']);
        $this->assertFalse(isset($container['foo']));

        $container['installed'] = array(
            'sub' => array('hub' => 'bub')
        );
        $this->assertSame('bub', $container['installed']['sub']['hub']);
    }


    public function testSave()
    {
        $dao = new Dao();
        $path = dirname(__FILE__) . '/project';
        $dao->setProjectPath($path);
        $this->assertSame($path . '/.phrozn', $dao->getProjectPath());

        $container = new Container($dao);
        $this->assertSame($dao, $container->getDao());
        $container
            ->set('bundle', 'test.me')
            ->set('template', array(1, 2, 3));

        @unlink($path . '/.phrozn/.registry');
        $this->assertFalse(file_exists($path . '/.phrozn/.registry'));
        $container->save();
        $this->assertTrue(file_exists($path . '/.phrozn/.registry'));
        $this->assertSame(file_get_contents($path . '/registry.serialized'), file_get_contents($path . '/.phrozn/.registry'));

        unset($container);
        $container = new Container($dao);
        $this->assertNull($container->get('bundle'));
        $this->assertNull($container->get('template'));
        $container->read();
        $this->assertSame('test.me', $container->get('bundle'));
        $this->assertSame(array(1, 2, 3), $container->get('template'));
    }

    public function testSaveWithImplicitDao()
    {
        $path = dirname(__FILE__) . '/project';

        $container = new Container();
        $this->assertInstanceOf('Phrozn\Registry\Dao', $container->getDao());

        $container->getDao()->setProjectPath($path);
        $this->assertSame($path . '/.phrozn', $container->getDao()->getProjectPath());

        $container
            ->set('bundle', 'test.me')
            ->set('template', array(1, 2, 3));

        @unlink($path . '/.phrozn/.registry');
        $this->assertFalse(file_exists($path . '/.phrozn/.registry'));
        $container->save();
        $this->assertTrue(file_exists($path . '/.phrozn/.registry'));
        $this->assertSame(file_get_contents($path . '/registry.serialized'), file_get_contents($path . '/.phrozn/.registry'));

        unset($container);
        $container = new Container();
        $container->getDao()->setProjectPath($path);

        $this->assertNull($container->get('bundle'));
        $this->assertNull($container->get('template'));
        $container->read();
        $this->assertSame('test.me', $container->get('bundle'));
        $this->assertSame(array(1, 2, 3), $container->get('template'));
    }
}

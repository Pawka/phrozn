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
 * @category    PhroznTest
 * @package     Phrozn\Registry
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;
use Phrozn\Registry\Item,
    Phrozn\Registry\Container,
    Phrozn\Registry\Dao\Yaml as Dao,
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
        $container->foo = 'bar';
        $this->assertSame('bar', (string)$container->foo);
        $container->foo = 'baz';
        $this->assertSame('baz', (string)$container->foo);
        $container->foo->bar = 'foobar';
        $container->foo->baz = 'foobaz';
        $this->assertSame('baz', (string)$container->foo);
        $this->assertSame('foobar', (string)$container->foo->bar->value);
        $this->assertSame('foobar', (string)$container->foo->bar);
        $this->assertSame('foobaz', (string)$container->foo->baz->value);
        $this->assertSame('foobaz', (string)$container->foo->baz);

        $container->foo = 'baz';
        $this->assertSame('baz', (string)$container->foo);
        unset($container->foo);
        $this->assertNull($container->foo->value);

        $container->foo->bar = 'foobar';
        $this->assertSame('foobar', (string)$container->foo->bar->value);
        unset($container->foo->bar);
        $this->assertNull($container->foo->bar->value);

        $container->foo->baz = 'foobaz';
        $this->assertSame('foobaz', (string)$container->foo->baz->value);
        unset($container->foo->baz);
        $this->assertNull($container->foo->baz->value);

        $arr = array(1,2,3);
        $container->foo->bar = $arr;
        $this->assertSame($arr, $container->foo->bar->value);
        $this->assertInstanceOf('Phrozn\Registry\Item', $container->foo->bar);
        unset($container->foo->bar);
        $this->assertSame(null, $container->foo->bar->value);
    }

    public function testSave()
    {
        $dao = new Dao();
        $path = dirname(__FILE__) . '/project';
        $dao->setProjectPath($path);
        $this->assertSame($path . '/.phrozn', $dao->getProjectPath());

        $container = new Container($dao);
        $this->assertSame($dao, $container->getDao());
        $container->bundle->sub->hub = 12;
        $container->bundle->dub = array(1, 2, 3);

        @unlink($path . '/.phrozn/.registry');
        $this->assertFalse(file_exists($path . '/.phrozn/.registry'));
        $container->save();
        $this->assertTrue(file_exists($path . '/.phrozn/.registry'));
        $this->assertSame(file_get_contents($path . '/registry'), file_get_contents($path . '/.phrozn/.registry'));

        unset($container);
        $container = new Container($dao);
        $this->assertSame('', (string)$container->bundle->sub->hub);
        $container->read();
        $this->assertSame('12', (string)$container->bundle->sub->hub);
        $this->assertSame(array(1, 2, 3), $container->bundle->dub->value);
    }

    public function testSaveWithImplicitDao()
    {
        $path = dirname(__FILE__) . '/project';

        $container = new Container();
        $this->assertInstanceOf('Phrozn\Registry\Dao', $container->getDao());

        $container->getDao()->setProjectPath($path);
        $this->assertSame($path . '/.phrozn', $container->getDao()->getProjectPath());

        $container->bundle->sub->hub = 12;
        $container->bundle->dub = array(1, 2, 3);

        @unlink($path . '/.phrozn/.registry');
        $this->assertFalse(file_exists($path . '/.phrozn/.registry'));
        $container->save();
        $this->assertTrue(file_exists($path . '/.phrozn/.registry'));
        $this->assertSame(file_get_contents($path . '/registry'), file_get_contents($path . '/.phrozn/.registry'));

        unset($container);
        $container = new Container();
        $container->getDao()->setProjectPath($path);

        $this->assertSame('', (string)$container->bundle->sub->hub);
        $this->assertNull($container->bundle->sub->hub->value);
        $this->assertNull($container->bundle->sub->hub->getValue());
        $container->read();
        $this->assertSame('12', (string)$container->bundle->sub->hub);
        $this->assertSame(12, $container->bundle->sub->hub->value);
        $this->assertSame(12, $container->bundle->sub->hub->getValue());
        $this->assertSame(array(1, 2, 3), $container->bundle->dub->value);
    }
}

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
 * @author      Povilas Balzaravičius
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest;

use Phrozn\Phrozn;

class PhroznTest extends \PHPUnit_Framework_TestCase
{
    protected $phrozn;

    public function setUp()
    {
        $this->phrozn = new Phrozn;
    }

    public function testGetDefaultCommands()
    {
        $app = $this->phrozn;
        $refl = new \ReflectionMethod($app, 'getDefaultCommands');
        $refl->setAccessible(true);
        $list = $refl->invoke($app);

        //0 - HelpCommand, 1 - ListCommand by Console component.
        $this->assertInstanceOf('Phrozn\Command\InitCommand', $list[2]);
        $this->assertInstanceOf('Phrozn\Command\ClobberCommand', $list[3]);
        $this->assertInstanceOf('Phrozn\Command\SingleCommand', $list[4]);
        $this->assertInstanceOf('Phrozn\Command\BuildCommand', $list[5]);
    }
}

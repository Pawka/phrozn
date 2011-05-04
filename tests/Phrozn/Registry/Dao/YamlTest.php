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
 * @subpackage  Dao
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
 * @subpackage  Dao
 * @author      Victor Farazdagi
 */
class YamlTest 
    extends TestCase
{
    public function testSaveRetrive()
    {
        $r1 = new Registry();

        $r1->set('foo') = 'bar';
        $dao = new Dao($registry);

        $r2 = new Registry();
        $this->assertFalse(isset($r2->get('foo')))

        $dao->save();

        $r2 = new Registry();
        $this->assertTrue(isset($r2->get('foo')));
        $tihs->assertSame('bar', $r2->get('foo'));
    }
}

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
 * @package     Phrozn\Outputter
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Outputter;
use Phrozn\Outputter\TestOutputter as Outputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Outputter
 * @author      Victor Farazdagi
 */
class TestOutputterTest
    extends \PHPUnit_Framework_TestCase
{
    public function testStdOut()
    {
        $outputter = new Outputter($this);
        $outputter->assertNotInLogs('sending output');
        $outputter->stdout('sending output');
        $outputter->assertInLogs('sending output');
    }

    public function testStdErr()
    {
        $outputter = new Outputter($this);
        $outputter->assertNotInLogs('sending output');
        $outputter->stderr('sending output');
        $outputter->assertInLogs('sending output');
    }

    public function testStdOutWithResource()
    {
        $fp = fopen('/tmp/stdout', 'w+');
        define('STDOUT', $fp);
        fclose($fp);

        $outputter = new Outputter($this);
        $outputter->stdout('sending output', '');

        $this->assertSame('sending output', trim(file_get_contents('/tmp/stdout')));
    }

    public function testStdErrWithResource()
    {
        $fp = fopen('/tmp/stderr', 'w+');
        define('STDERR', $fp);
        fclose($fp);

        $outputter = new Outputter($this);
        $outputter->stderr('sending output', '');

        $this->assertSame('sending output', trim(file_get_contents('/tmp/stderr')));
    }

    public function testAssertInLogsFail()
    {
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', 'this should produce error');
        $outputter = new Outputter($this);
        $outputter->assertInLogs('not found', 'this should produce error');
    }

    public function testAssertNotInLogsFail()
    {
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', 'this should produce error');
        $outputter = new Outputter($this);
        $outputter->stdout('this string exists');
        $outputter->assertNotInLogs('this string exists', 'this should produce error');
    }
}

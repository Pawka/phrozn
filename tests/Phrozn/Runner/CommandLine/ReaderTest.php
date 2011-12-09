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
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Runner\CommandLine;
use Phrozn\Runner\CommandLine\Reader,
    Phrozn\Outputter\TestOutputter as Outputter;

/**
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class ReaderTest
    extends \PHPUnit_Framework_TestCase
{
    public function testReader()
    {
        $handle = fopen(dirname(__FILE__) . '/stdin', 'w+');
        $outputter = new Outputter($this);
        $reader = new Reader($handle, $outputter);

        fputs($handle, "yes\n");

        $out = $reader->readLine("Input prompt:");
        //$this->assertSame("yes", $out);

        ob_start();
        var_dump($handle);
        $dump = trim(ob_get_clean());
        $this->assertTrue(strpos($dump, 'of type (stream)') > 0);

        unset($reader); // free up handler

        ob_start();
        var_dump($handle);
        $dump = trim(ob_get_clean());
        $this->assertTrue(strpos($dump, 'of type (Unknown)') > 0);

        unlink(dirname(__FILE__) . '/stdin');
    }


}

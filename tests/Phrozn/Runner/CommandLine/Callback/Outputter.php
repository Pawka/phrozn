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
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Runner\CommandLine\Callback;
use Console_Color as Color;

/**
 * Helper allowing to redirect ouput from CLI command runner
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
class Outputter 
{
    /**
     * Output lines
     * @var array
     */
    private $lines = array();

    /**
     * Reference to current test case
     * @var \PHPUnit_Framework_TestCase 
     */
    private $testCase;

    public function __construct($testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * Add line to output
     *
     * @param string $str Line to add
     *
     * @return \PhroznTest\Runner\CommandLine\Callback\Outputter
     */
    public function stdout($str)
    {
        $str = Color::strip(Color::convert($str));
        $this->lines[] = trim($str);
        return $this;
    }

    /**
     * Get all outputted lines
     *
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Asserts that the log buffer contains specified message
     *
     * @param string $expected Message subsctring
     * @param string $errmsg The error message to display.
     *
     * @return void
     */
    public function assertInLogs($expected, $errorMsg = "Expected to find '%s' in logs:\n\n%s")
    {
        foreach($this->getLines() as $log) {
            if (false !== stripos($log, $expected)) {
                $this->testCase->assertEquals(1, 1); // increase number of positive assertions
                return;
            }
        }
        $this->testCase->fail(sprintf($errorMsg, $expected, var_export($this->getLines(),true)));
    }

    /**
     * Asserts that the log buffer does NOT contain specified message
     *
     * @param string $expected Message subsctring
     * @param string $errmsg The error message to display.
     *
     * @return void
     */
    public function assertNotInLogs($message, $errorMsg = "Unexpected string '%s' found in logs:\n\n%s")
    {
        foreach($this->getLines() as $log) {
            if (false !== stripos($log, $message)) {
                $this->testCase->fail(sprintf($errorMsg, $message, var_export($this->getLines(), true)));
            }
        }

        $this->testCase->assertEquals(1, 1); // increase number of positive assertions
    }
}

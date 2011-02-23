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

    public function stdout($str)
    {
        $str = Color::strip(Color::convert($str));
        $this->lines[] = $str;
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function contains($str)
    {
        foreach ($this->getLines() as $line) {
            if (strpos(trim($line), $str) !== false) {
                return true;
            }
        }
        return false;
    }
}

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
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner\CommandLine;

/**
 * Subcommand interface
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine\Callback
 * @author      Victor Farazdagi
 */
interface Callback
{
    /**
     * Executes the callback action 
     *
     * @return string
     */
    public function execute();

    /**
     * Set CLI outputter
     *
     * @param Console_CommandLine_Outputter $outputter Where to forward output
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setOutputter($out);

    /**
     * Get CLI outputter
     *
     * @return Console_CommandLine_Outputter CLI outputter instance
     */
    public function getOutputter();

    /**
     * Result object of CLI input parsing
     *
     * @param Console_CommandLine_Result $result Parser's result
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setParseResult($result);

    /**
     * Get parsed result object
     *
     * @return Console_CommandLine_Result 
     */
    public function getParseResult();

    /**
     * Set config data for a given callback
     *
     * @param array $config Config array
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setConfig($config);

    /**
     * Get command config array
     *
     * @return array
     */
    public function getConfig();

}

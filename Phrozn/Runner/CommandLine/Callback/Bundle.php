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

namespace Phrozn\Runner\CommandLine\Callback;
use Phrozn\Runner\CommandLine;

/**
 * phrozn bundle command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Bundle 
    extends Base
    implements CommandLine\Callback
{
    /**
     * List of available sub-commands
     * @var array
     */
    private $availableCommands = array(
        'apply', 'list', 'info', 'clobber'
    );

    /**
     * Executes the callback action 
     *
     * @return string
     */
    public function execute()
    {
        if (false === $this->getCommand()) {
            $this->out($this->getHeader());
            $this->out(self::STATUS_FAIL . "No sub-command specified. Use 'phr ? bundle' for more info.");
            $this->out($this->getFooter());
        }
        if (isset($this->getParseResult()->command->command_name)) {
            $command = $this->getParseResult()->command->command_name;
            if (in_array($command, $this->availableCommands)) {
                return $this->{'exec' . ucfirst($command)}();
            }
        }
    }

    private function execList()
    {
        $bundle = $this->getBundleName();
        var_dump($bundle);
    }

    private function getBundleName()
    {
        $bundle = 'https://github.com/farazdagi/phrozn-bundles/'; // official bundle repository
        if (null !== $this->getCommand()->args["bundle"]) {
            $bundle = $this->getCommand()->args["bundle"];
        }
        return $bundle;
    }

    /**
     * Handy short-cut to subcommand
     *
     * @return Console_CommandLine_Result
     */
    private function getCommand()
    {
        return $this->getParseResult()->command->command;
    }
}

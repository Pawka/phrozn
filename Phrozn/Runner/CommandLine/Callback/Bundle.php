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
use Phrozn\Runner\CommandLine,
    Phrozn\Outputter\Console\Color,
    Symfony\Component\Yaml\Yaml,
    Console_Table as ConsoleTable;

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
     * @return void
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
                $this->out($this->getHeader());
                $this->{'exec' . ucfirst($command)}();
                $this->out($this->getFooter());
            }
        }
    }

    /**
     * List bundles
     *
     * @return void
     */
    private function execList()
    {
        $config = $this->getConfig();
        $bundles = Yaml::load($config['paths']['configs'] . 'bundles.yml');
        $que = $this->getBundleParam(); // user searches for a certain bundle


        $tbl = new ConsoleTable();
        $tbl->setHeaders(
            array('S', 'Name', 'Version', 'Author', 'Description',)
        );
        foreach ($bundles as $bundle) {
            if (strlen($que) && false === stripos($bundle['name'], $que)) {
                continue;
            } 
            $tbl->addRow(array(
                'p', 
                $bundle['name'], 
                $bundle['version'],
                $bundle['author'],
                $bundle['description'],
            ));
        }
        if (strlen($que)) { // hi-light search results
            $callback = array($this, 'highlightSearchTerm');
            $tbl->addFilter(1, $callback);
            $this->out(sprintf("Search bundles having \"%s\"..", $que));
        }
        $tbl->setAlign(2, CONSOLE_TABLE_ALIGN_CENTER);
        $this->out($tbl->getTable()) ;
    }

    /**
     * Extract bundle name/uri argument
     *
     * @return string
     */
    private function getBundleParam()
    {
        $bundle = null;
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

    /**
     * Wraps Console color codes around $value,
     * depending if its larger or smaller 0.
     *
     * @param float $value Value (column 1)
     *
     * @return string Colorful value
     */
    public function highlightSearchTerm($value)
    {
        $que = $this->getBundleParam();
        $value = preg_replace('/(' . preg_quote($que). ')/si', '[\1]', $value);
        return $value;
    }
}

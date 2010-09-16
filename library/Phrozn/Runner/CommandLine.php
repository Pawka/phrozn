<?php
/**
 * Copyright 2010 Victor Farazdagi
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
 * @package     Phrozn_Runner
 * @version     $Id$
 * @author      Victor Farazdagi
 * @copyright   2010 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

require_once 'Console/CommandLine.php';
require_once('Phrozn/Runner/Abstract.php');

/**
 * CLI version of framework invoker.
 *
 * @category    Phrozn
 * @package     Phrozn_Runner
 * @version     $Id$
 * @author      Victor Farazdagi
 */
class Phrozn_Runner_CommandLine extends Phrozn_Runner_Abstract
{
    public static function run()
    {
        $path = dirname(__FILE__) . '/CommandLine/CommandDefinition.xml';
        $parser = Console_CommandLine::fromXmlFile($path);

        try {
            $result = $parser->parse();
            $opts = isset($result->command->command->options) 
                  ? $result->command->command->options 
                  : array();
            $args = isset($result->command->command->args) 
                  ? $result->command->command->args 
                  : array();
            $command = ($result->command_name === false) 
                     ? 'site' 
                     : $result->command_name;
            $target = isset($result->command->command_name)
                    ? $result->command->command_name
                    : 'help';

            $build = new self($command, $target,  $opts, $args);
            $build->execute();
        } catch (Exception $e) {
            $parser->displayError($e->getMessage());
        }
    }

}

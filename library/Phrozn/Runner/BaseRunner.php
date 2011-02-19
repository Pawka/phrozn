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
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 * @copyright   2010 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner;
use \Console_CommandLine as Parser,
    \Console_CommandLine_Result as ParseResult;

/**
 * Base class for framework invoker instances.
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
abstract class BaseRunner
    implements \Phrozn\Runner
{

    /**
     * @var \Console_CommandLine
     */
    private $parser;

    /**
     * @var \Console_CommandLine_Result
     */
    private $result;
    protected function __construct(Parser $parser, ParseResult $result)
    {
        $this->parser = $parser;
        $this->result = $result;
    }

    /**
     * Locate build-file and execute specified target
     */
    public function execute()
    {
        $opts = $this->result->options;
        $command = $this->result->command_name;
        $optionSet = $argumentSet = false;

        switch ($command) {
            case 'help':
                $opts['help'] = true;
                break;
        }

        foreach ($opts as $name => $value) {
            if ($value === true) {
                $option = $this->parser->options[$name];
                if (isset($option->callback) && is_callable($option->callback)) {
                    call_user_func($option->callback, 
                        $value, 
                        $option, 
                        $this->result, $this->parser, 
                        $option->action_params);
                }
                $optionSet = true;
                break;
            }
        }

        if ($command === false && $optionSet === false && $argumentSet === false) {
            $this->parser->outputter->stdout("Type 'phrozn help' for usage.\n");
        }
    }

}

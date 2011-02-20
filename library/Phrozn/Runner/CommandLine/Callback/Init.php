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
use Console_Color as Color,
    Symfony\Component\Yaml\Yaml,
    Phrozn\Runner\CommandLine;

/**
 * phrozn init command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Init 
    extends BaseCallback
    implements CommandLine\Callback
{
    /**
     * Executes the callback action 
     *
     * @return string
     */
    public function execute()
    {
        $out = '';

        $path = isset($this->getParseResult()->command->args['path'])
               ? $this->getParseResult()->command->args['path'] : \getcwd();
        $out .= sprintf("Create project: %s \n", $path);

        $this->display($out);
    }
}

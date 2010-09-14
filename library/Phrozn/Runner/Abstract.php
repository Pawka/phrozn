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

/**
 * Base class for framework invoker instances.
 *
 * @category    Phrozn
 * @package     Phrozn_Runner
 * @version     $Id$
 * @author      Victor Farazdagi
 */
abstract class Phrozn_Runner_Abstract
{
    /**
     * phrozn command/target to invoke
     * @var string
     */
    private $command;

    /**
     * Target arguments
     * @var array
     */
    private $args;

    /**
     * Target options
     * @var array
     */
    private $opts;

    protected function __construct($command, $args = null, $opts = null)
    {
        $this->command = $command;
        $this->args = $args;
        $this->opts = $opts;
    }

    /**
     * Fire the passed command execution
     */
    abstract public function execute();

}

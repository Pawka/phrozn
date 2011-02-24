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
 * @package     Phrozn
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;

/**
 * Outputter interface
 *
 * @category    Phrozn
 * @package     Phrozn
 * @author      Victor Farazdagi
 */
interface Outputter 
{

    /**
     * Send normal output (to stdout by default)
     *
     * @param string $message Output message
     *
     * @return \Phrozn\Outputter
     */
    public function out($message);

    /**
     * Send error output (to stderr by default)
     *
     * @param string $message Output message
     *
     * @return \Phrozn\Outputter
     */
    public function err($message);
}

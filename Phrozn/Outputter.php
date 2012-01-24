<?php
/**
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
     * Status constants
     */
    const STATUS_FAIL       = '  [%rFAIL%n]    ';
    const STATUS_ADDED      = '  [%gADDED%n]   ';
    const STATUS_DELETED    = '  [%gDELETED%n] ';
    const STATUS_OK         = '  [%gOK%n]      ';

    /**
     * Processes the output for a message that should be displayed on STDOUT.
     *
     * @param string $msg The message to output
     * @param string $status Output status
     *
     * @return \Phrozn\Outputter
     */
    public function stdout($msg, $status = self::STATUS_OK);

    /**
     * Processes the output for a message that should be displayed on STDERR.
     *
     * @param string $msg The message to output
     * @param string $status Output status
     *
     * @return \Phrozn\Outputter
     */
    public function stderr($msg, $status = self::STATUS_FAIL);
}

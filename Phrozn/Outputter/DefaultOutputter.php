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
 * @package     Phrozn\Outputter
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Outputter;
use Phrozn\Outputter\Console\Color;

/**
 * Default outputter
 *
 * @category    Phrozn
 * @package     Phrozn\Outputter
 * @author      Victor Farazdagi
 */
class DefaultOutputter
    implements \Phrozn\Outputter
{
    /**
     * Whether to spice output with ANSI colors
     */
    private $useAnsiColors;

    public function __construct($useAnsiColors = true)
    {
        $this->useAnsiColors = $useAnsiColors;
    }

    /**
     * Writes the message $msg to STDOUT.
     *
     * @param string $msg The message to output
     * @param string $status Output status
     *
     * @return \Phrozn\Outputter
     */
    public function stdout($msg, $status = self::STATUS_OK)
    {
        $msg = Color::convert($status . $msg . "\n");
        if ($this->useAnsiColors === false) {
            $msg = Color::strip($msg);
        }
        if (defined('STDOUT')) {
            fwrite(STDOUT, $msg);
        } else {
            echo $msg;
            if (count(\ob_get_status()) !== 0) {
                ob_flush();
            }
        }
        return $this;
    }

    /**
     * Writes the message $msg to STDERR.
     *
     * @param string $msg The message to output
     * @param string $status Output status
     *
     * @return \Phrozn\Outputter
     */
    public function stderr($msg, $status = self::STATUS_FAIL)
    {
        $msg = Color::convert($status . $msg . "\n");
        if ($this->useAnsiColors === false) {
            $msg = Color::strip($msg);
        }
        if (defined('STDERR')) {
            fwrite(STDERR, $msg);
        } else {
            echo $msg;
            if (count(\ob_get_status()) !== 0) {
                ob_flush();
            }
        }
        return $this;
    }
}

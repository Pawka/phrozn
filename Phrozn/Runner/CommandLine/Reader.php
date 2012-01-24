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
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner\CommandLine;
use Phrozn\Has,
    Phrozn\Outputter\PlainOutputter as Outputter;

/**
 * Command-line input reader
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Reader
    implements Has\Outputter
{
    /**
     * @var \Phrozn\Outputter
     */
    private $outputter;

    /**
     * Input handler resource
     * @var resource
     */
    private $handle;

    /**
     * Setup reader
     *
     * @param resource $handle STDIN if not set, parametrized for flexibility during testing
     *
     * @return void
     */
    public function __construct($handle = null, $outputter = null)
    {
        // initilize input resource
        if (null === $handle) {
            if(defined("STDIN")) {
                $handle = STDIN;
            } else {
                $handle = fopen('php://stdin','r');
            }
        }
        if (null === $outputter) {
            $outputter = new Outputter();
        }
        $this
            ->setHandle($handle)
            ->setOutputter($outputter);
    }

    public function __destruct()
    {
        if(defined('STDIN') === false) {
            fclose($this->handle);
        }
    }

    /**
     * Get line from standard input
     *
     * @param string $prompt Input prompt
     *
     * @return string
     */
    public function readLine($prompt)
    {
        $this
            ->getOutputter()
            ->stdout($prompt);
        $out = fgets($this->getHandle());
        $this->getOutputter()->stdout("\n");
        return rtrim($out);
    }

    /**
     * Set outputter
     *
     * @param \Phrozn\Outputter $outputter Outputter instance
     *
     * @return \Phrozn\Has\Outputter
     */
    public function setOutputter($outputter)
    {
        $this->outputter = $outputter;
        return $this;
    }

    /**
     * Get outputter instance
     *
     * @return \Phrozn\Outputter
     */
    public function getOutputter()
    {
        return $this->outputter;
    }

    /**
     * Set input handle
     *
     * @param resource $handle Resource pointer
     *
     * @return \Phrozn\Runner\CommandLine\Reader
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Get input handle
     *
     * @return resource
     */
    public function getHandle()
    {
        return $this->handle;
    }
}


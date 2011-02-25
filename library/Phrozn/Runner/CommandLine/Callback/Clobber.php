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
 * phrozn clobber command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Clobber 
    extends BaseCallback
    implements CommandLine\Callback
{
    /**
     * Data to be used as an answer to confirmation in UTs
     * @var string
     */
    private $unitTestData;

    /**
     * Executes the callback action 
     *
     * @return string
     */
    public function execute()
    {
        $this->purgeProject();
    }

    private function purgeProject()
    {
        $path = isset($this->getParseResult()->command->args['path'])
               ? $this->getParseResult()->command->args['path'] : \getcwd();

        if ($path[0] != '/') { // not an absolute path
            $path = \getcwd() . '/./' . $path;
        }
        $path = realpath($path);

        $config = $this->getConfig();

        $path .= '/_phrozn/'; // where to copy skeleton

        $this->out($this->getHeader());
        $this->out("Purging project data..");
        $this->out(
            "\nLocated project folder: {$path}");
        $this->out( 
            "Project folder is to be removed.\n" .
            "This operation %rCAN NOT%n be undone.\n");

        if ($this->readLine() === 'yes') {
            `rm -rf $path`;
            $this->out(self::STATUS_DELETED . " {$path}");
        } else {
            $this->out(self::STATUS_FAIL . " Aborted..");
        }
        $this->out($this->getFooter());
    }

    /**
     * Decide whether we are faking getting data from STDIN
     *
     * @return string
     */
    public function readLine()
    {
        if (null !== $this->unitTestData) {
            return $this->unitTestData;
        } else {
            return readline("Type 'yes' to continue: ");
        }
    }

    /**
     * Since Unit Testing readline can be tricky, confirm answer is exposed
     * to unit test via this method. Simply pass the string you want to be used
     * in place of readline() result.
     *
     * @return \Phrozn\Runner\CommandLine\Callback
     */
    public function setUnitTestData($data)
    {
        $this->unitTestData = $data;
        return $this;
    }

}

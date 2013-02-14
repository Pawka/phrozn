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

namespace Phrozn\Runner\CommandLine\Callback;
use Phrozn\Outputter\Console\Color,
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
    extends Base
    implements CommandLine\Callback
{
    /**
     * Executes the callback action
     *
     * @return string
     */
    public function execute()
    {
        try {
            $this->purgeProject();
        } catch (\Exception $e) {
            $this->out(self::STATUS_FAIL . $e->getMessage());
            $this->out($this->getFooter());
        }
    }

    private function purgeProject()
    {
        $path = isset($this->getParseResult()->command->args['path'])
               ? $this->getParseResult()->command->args['path'] : \getcwd() . '/.phrozn';

        if (!$this->isAbsolute($path)) { // not an absolute path
            $path = realpath(\getcwd() . '/./' . $path);
        }

        $config = $this->getConfig();

        $this->out($this->getHeader());
        $this->out("Purging project data..");
        $this->out("\nLocated project folder: {$path}");
        $this->out(
            "Project folder is to be removed.\n" .
            "This operation %rCAN NOT%n be undone.\n");

        if (!$path || is_dir($path) === false) {
            throw new \RuntimeException("No project found at \"{$path}\"");
        }

        if ($this->readLine() === 'yes') {
            `rm -rf $path`;
            $this->out(self::STATUS_DELETED . " {$path}");
        } else {
            $this->out(self::STATUS_FAIL . " Aborted..");
        }
        $this->out($this->getFooter());
    }
}

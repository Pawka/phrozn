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
        $this->initializeNewProject();
    }

    private function initializeNewProject()
    {
        $path = isset($this->getParseResult()->command->args['path'])
               ? $this->getParseResult()->command->args['path'] : \getcwd() . '/.phrozn';

        $config = $this->getConfig();

        if (!$this->isAbsolute($path)) { // not an absolute path
            $path = \getcwd() . '/./' . $path;
        }

        ob_start();
        $this->out($this->getHeader());
        $this->out("Initializing new project");
        $this->out("\n  Project path: {$path}");

        if (is_dir($path)) {
            $this->out(self::STATUS_FAIL . "Project directory '" . basename($path) . "' already exists..");
            $this->out($this->pad(self::STATUS_FAIL) . "Type 'phrozn help clobber' to get help on removing existing project.");
            return $this->out($this->getFooter());
        } else {
            if (!@mkdir($path)) {
                $this->out(self::STATUS_FAIL . "Error creating project directory..");
                return $this->out($this->getFooter());
            }
        }

        // copy skeleton to newly inited project
        $skeletonPath = $config['paths']['skeleton'];
        $this->copy($skeletonPath, $path, function ($that, $destPath, $status) use ($path) {
            $destPath = str_replace('//', '/', $destPath);
            $destPath = str_replace($path, '', $destPath);
            if ($status) {
                $that->out(Base::STATUS_ADDED . "{$destPath}");
            } else {
                $that->out(Base::STATUS_FAIL . "{$destPath}");
            }
        });

        return $this->out($this->getFooter());
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @link http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param string $source Source path
     * @param string $dest Destination path
     * @return bool Returns TRUE on success, FALSE on failure
     */
    private function copy($source, $dest, $callback)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            $result = @copy($source, $dest);
            $callback($this, $dest, $result);
            return $result;
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $this->copy("$source/$entry", "$dest/$entry", $callback);
        }

        // Clean up
        $dir->close();
        return true;
    }

}

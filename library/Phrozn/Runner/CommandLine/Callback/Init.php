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
        $out = $this->initializeNewProject();

        $this->display($out);
    }

    private function initializeNewProject()
    {
        $path = isset($this->getParseResult()->command->args['path'])
               ? $this->getParseResult()->command->args['path'] : \getcwd();

        $config = $this->getConfig();

        if ($path[0] != '/') { // not an absolute path
            $path = \getcwd() . '/./' . $path;
        }
        $path = realpath($path);

        $path .= '/_phrozn/'; // where to copy skeleton

        ob_start();
        echo "\nInitializing new project: {$path} \n";

        if (is_dir($path)) {
            echo self::STATUS_FAIL . "Project directory '_phrozn' already exists..\n";
            echo $this->pad(self::STATUS_FAIL) . "Type 'phrozn help clobber' to get help on removing existing project.\n";
            return ob_get_clean();
        } else {
            if (!mkdir($path)) {
                echo self::STATUS_FAIL . "Error creating project directory..\n";
                return ob_get_clean();
            }
        }


        $skeletonPath = $config['paths']['skeleton'];
        $dir = new \RecursiveDirectoryIterator($skeletonPath);
        $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $dirname = '';
        foreach ($it as $item) {
            $baseName = $item->getBaseName();
            if ($baseName != '.' && $baseName != '..') {
                if ($item->isFile()) {
                    $destPath= $dirname . $item->getBaseName();
                    if (@copy($item->getPathname(), $path . $destPath)) {
                        echo self::STATUS_ADDED . "{$destPath}\n";
                    } else {
                        echo self::STATUS_FAIL . "{$destPath}\n";
                    }
                    //echo 'copy ' . $item->getPathname() . ' -> ' . $path . $destPath . "\n";
                } else if ($item->isDir()) {
                    $dirname = str_replace($skeletonPath, '', $item->getRealPath()) . '/';
                    mkdir($path . $dirname);
                }
            }
        }

        return ob_get_clean();
    }

}

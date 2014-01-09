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
 * @author      Povilas Balzaravičius
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * phrozn init command
 *
 * @author      Povilas Balzaravičius
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 */
class InitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('init')
            ->setAliases(array('initialize'))
            ->setDescription('Initialize Phrozn project')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Destination directory'
            );
    }   
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path') ?: \getcwd() . '/.phrozn';

        $config = $this->getConfig();

        if (!$this->isAbsolute($path)) { // not an absolute path
            $path = \getcwd() . '/./' . $path;
        }

        ob_start();
        $output->writeln("Initializing new project");
        $output->writeln("Project path: <info>{$path}</info>");

        if (is_dir($path)) {
            $output->writeln("<error>Project directory '" . basename($path) . "' already exists.</error>");
            $output->writeln("<info>Type 'phrozn help clobber' to get help on removing existing project.</info>");
            return 1;
        } else {
            if (!@mkdir($path)) {
                $output->writeln("<error>Error creating project directory.</error>");
                return 1;
            }
        }

        // copy skeleton to newly inited project
        $skeletonPath = $config['paths']['skeleton'];
        $this->copy($skeletonPath, $path, function ($that, $destPath, $status) use ($path, $output) {
            $destPath = str_replace('//', '/', $destPath);
            $destPath = str_replace($path, '', $destPath);
            if ($status) {
                $output->writeln("<comment>{$destPath}</comment>");
            } else {
                $output->writeln("<error>{$destPath}</error>");
            }
        });

        return 0;
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

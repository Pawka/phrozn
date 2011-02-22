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

namespace Phrozn\Runner\CommandLine;
use Symfony\Component\Yaml\Yaml,
    Console_CommandLine as CommandParser,
    Phrozn\Runner\CommandLine;

/**
 * Completely loaded main phrozn command (with subcommands loaded as well)
 * Internally Console_CommandLine is used as base
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Parser
    extends \Console_CommandLine
{
    /**
     * Contents of phrozn.yml is loaded into this attribute on startup
     */
    private $config;

    /**
     * Create phrozn command 
     *
     * @param array $paths Folder paths
     */
    public function __construct($paths)
    {
        // load main config
        $config = Yaml::load($paths['configs'] . 'phrozn.yml');
        parent::__construct($config['command']);
        $this->configureCommand($paths, $config); // load all necessary sub-commands
    }

    /**
     * Fine tune main command by adding subcommands
     *
     * @para array $paths Paths to various Phrozn directories
     * @param array $config Loaded contents of phrozn.yml
     *
     * @return \Phrozn\Runner\Command
     */
    private function configureCommand($paths, $config)
    {
        // options
        foreach ($config['command']['options'] as $name => $option) {
            $this->addOption($name, $option);
        }

        // sub-commands
        $commands = CommandLine\Commands::getInstance()
                            ->setPath($paths['configs'] . 'commands');
        foreach ($commands as $name => $data) {
            $command = $data['command'];
            $cmd = $this->addCommand($name, $command);
            // command arguments
            $args = isset($command['arguments']) ? $command['arguments'] : array();
            foreach ($args as $name => $argument) {
                $cmd->addArgument($name, $argument);
            }
            // command options
            $opts = isset($command['options']) ? $command['options'] : array();
            foreach ($opts as $name => $option) {
                $cmd->addOption($name, $option);
            }
        }

        return $this;
    }

    public function __toString()
    {
        $out = get_class($this) . "\n";
        $out .= "OPTIONS: " . print_r($this->options, true);
        $out .= "ARGS: " . print_r($this->args, true);
        $out .= "COMMANDS: " . print_r(array_keys($this->commands), true);
        return $out;
    }
}

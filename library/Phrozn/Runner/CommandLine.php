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
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner;
use Symfony\Component\Yaml\Yaml,
    Console_CommandLine as CommandParser,
    Phrozn\Runner\CommandLine\Commands,
    Phrozn\Runner\CommandLine\Command;

/**
 * CLI version of framework invoker.
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class CommandLine 
    implements \Phrozn\Runner
{
    /**
     * System paths
     */
    private $paths = array(
        'app'       => null,
        'bin'       => null,
        'lib'       => null,
        'configs'   => null,
    );

    /**
     * @var \Console_CommandLine
     */
    private $parser;

    /**
     * @var \Console_CommandLine_Result
     */
    private $result;

    /**
     * Contents of phrozn.yml is loaded into this attribute on startup
     */
    private $config;

    /**
     * Create runner
     *
     * @param \Zend\Loader\SplAutoloader $loader Instance of auto-loader
     * @param array $paths Folder paths
     */
    public function __construct($loader, $paths)
    {
        $this->paths = $paths;
        $this->loader = $loader;

        // load main config
        $this->config = Yaml::load($paths['configs'] . 'phrozn.yml');
    }

    /**
     * Process the request
     *
     * @return void
     */
    public function run()
    {
        $this->parser = self::createParser();

        try {
            $this->result = $this->parser->parse();
        } catch (\Exception $e) {
            $this->parser->displayError($e->getMessage());
        }

        $this->process();
    }

    /**
     * Parse input and invoke necessary processor callback
     */
    private function process()
    {
        $opts = $this->result->options;
        $commandName = $this->result->command_name;
        $command = null;
        $optionSet = $argumentSet = false;

        // special treatment for -h --help main command options
        if ($opts['help'] === true) {
            $commandName = 'help';
        }

        if ($commandName) {
            $configFile = $this->paths['configs'] . 'commands/' . $commandName . '.yml';
            $command = new Command($configFile);
        }

        // check if any option is set
        // basically check for --version -v --help -h options
        foreach ($opts as $name => $value) {
            if ($value === true) {
                $optionSet = true;
                break;
            }
        }

        // fire up subcommand
        if (isset($command['callback'])) {
            $this->invoke($command['callback'], $command);
        }

        if ($commandName === false && $optionSet === false && $argumentSet === false) {
            $this->parser->outputter->stdout("Type 'phrozn help' for usage.\n");
        }
    }

    private function createParser()
    {

        $parser = new CommandParser($this->config['command']);

        // options
        foreach ($this->config['command']['options'] as $name => $option) {
            $parser->addOption($name, $option);
        }

        // sub-commands
        $commands = Commands::getInstance()
                            ->setPath($this->paths['configs'] . 'commands');
        foreach ($commands as $name => $data) {
            $command = $data['command'];
            $cmd = $parser->addCommand($name, $command);
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

        return $parser;
    }

    /**
     * Invoke callback
     */
    private function invoke($callback, $data)
    {
        list($class, $method) = $callback;
        $class = 'Phrozn\\Runner\\CommandLine\\Callback\\' . $class;


        $runner = new $class;
        $data['paths'] = $this->paths; // inject paths
        $runner
            ->setParser($this->parser)
            ->setParseResult($this->result)
            ->setConfig($data);
        $callback = array($runner, $method);
        if (is_callable($callback)) {
            call_user_func($callback);
        }
    }
}

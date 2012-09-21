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
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner;
use Symfony\Component\Yaml\Yaml,
    Phrozn\Runner\CommandLine\Parser,
    Phrozn\Runner\CommandLine\Command,
    Phrozn\Outputter\DefaultOutputter as Outputter;

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
    private $paths = array();

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
     * @var \Phrozn\Autoloader
     */
    private $loader;

    /**
     * Create runner
     *
     * @param \Phrozn\Autoloader $loader Instance of auto-loader
     * @param array $paths Folder paths
     */
    public function __construct($loader)
    {
        $this->paths = $loader->getPaths();
        $this->loader = $loader;

        // load main config
        $this->config = Yaml::load($this->paths['configs'] . 'phrozn.yml');

        // auto detect platform that did not support console color (like windows)
        if ($this->config['use_ansi_colors'] === true) {
            $supportsColors = DIRECTORY_SEPARATOR != '\\'
                && function_exists('posix_isatty') && @posix_isatty(STDOUT);
            $this->config['use_ansi_colors'] = $supportsColors;
        }
    }

    /**
     * Process the request
     *
     * @param array $params Runner options
     *
     * @return void
     */
    public function run($params = null)
    {
        $this->parser = new Parser($this->paths);

        try {
            $argc = ($params === null) ? null : count($params);
            $this->result = $this->parser->parse($argc, $params);
        } catch (\Exception $e) {
            $this->parser->displayError($e->getMessage());
        }

        $this->parse();
    }

    /**
     * Parse input and invoke necessary processor callback
     *
     * @return void
     */
    private function parse()
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

    /**
     * Invoke callback
     */
    private function invoke($callback, $data)
    {
        list($class, $method) = $callback;
        $class = 'Phrozn\\Runner\\CommandLine\\Callback\\' . $class;

        $useAnsiColors = (bool)$this->config['use_ansi_colors'];

        $runner = new $class;
        $data['paths'] = $this->paths; // inject paths
        $runner
            ->setOutputter(new Outputter($useAnsiColors))
            ->setParseResult($this->result)
            ->setConfig($data);
        $callback = array($runner, $method);
        if (is_callable($callback)) {
            call_user_func($callback);
        }
    }
}

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
use Phrozn\Runner\CommandLine,
    Phrozn\Outputter\Console\Color,
    Phrozn\Outputter\DefaultOutputter as Outputter,
    Phrozn\Outputter\PlainOutputter,
    Phrozn\Runner\CommandLine\Reader,
    Symfony\Component\Yaml\Yaml,
    Phrozn\Runner\CommandLine\Commands;

/**
 * Base methods for phrozn command callbacks
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
abstract class Base
    implements CommandLine\Callback
{
    /**
     * Status constants
     */
    const STATUS_FAIL       = '  [%rFAIL%n]    ';
    const STATUS_ADDED      = '  [%gADDED%n]   ';
    const STATUS_DELETED    = '  [%gDELETED%n] ';
    const STATUS_OK         = '  [%gOK%n]      ';

    /**
     * @var \Console_CommandLine_Outputter
     */
    private $outputter;

    /**
     * @var \Console_CommandLine_Result
     */
    private $result;

    /**
     * Contents of command-name.yml
     */
    private $config;

    /**
     * Loaded phrozn.yml
     *
     * @array
     */
    private $commandMeta;

    /**
     * Data to be used as an answer to confirmation in UTs
     * @var string
     */
    private $unitTestData;

    /**
     * Set CLI outputter
     *
     * @param Console_CommandLine_Outputter $outputter Where to forward output
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setOutputter($out)
    {
        $this->outputter = $out;
        return $this;
    }

    /**
     * Get CLI outputter
     *
     * @return Console_CommandLine_Outputter CLI outputter instance
     */
    public function getOutputter()
    {
        if (null === $this->outputter) {
            $config = $this->getConfig();
            $meta = Yaml::load($config['paths']['configs'] . 'phrozn.yml');
            $useAnsiColors = (bool)$meta['use_ansi_colors'];
            $this->outputter = new Outputter($useAnsiColors);
        }
        return $this->outputter;
    }

    /**
     * Result object of CLI input parsing
     *
     * @param Console_CommandLine_Result $result Parser's result
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setParseResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get parsed result object
     *
     * @return Console_CommandLine_Result
     */
    public function getParseResult()
    {
        return $this->result;
    }

    /**
     * Set config data for a given callback
     *
     * @param array $config Config array
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get command config array
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Output string to stdout (flushes output). Wrapper around Outputter
     *
     * @param string $str String to output
     *
     * @return \Phrozn\Runner\CommandLine\Callback
     */
    public function out($str)
    {
        $this->getOutputter()->stdout($str, '');
        if (count(\ob_get_status()) !== 0) {
            ob_flush();
        }
    }

    /**
     * Read-line either from STDIN or mock unit test data
     *
     * @return string
     */
    public function readLine()
    {
        if (null !== $this->unitTestData) {
            return $this->unitTestData;
        } else {
            $outputter = new PlainOutputter();
            $reader = new Reader();
            return $reader
                ->setOutputter($outputter)
                ->readLine("Type 'yes' to continue: ");
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

    /**
     * Combine command documentation
     *
     * @param string $file Command file to combine
     * @param boolean $verbose Whether to provide full documentation or just summary
     *
     * @return string
     */
    protected function combine($file, $verbose = false)
    {
        $config = $this->getConfig();
        $file = $config['paths']['configs'] . 'commands/' . $file . '.yml';
        $data = Yaml::load($file);

        if ($data === $file) {
            return false;
        }
        $docs = $data['docs'];
        $command = $data['command'];

        $out = '';
        $out .= sprintf("%s: %s\n", $docs['name'], $docs['summary']);
        $out .= 'usage: ' . trim($docs['usage']) . "\n";
        $out .= "\n  " . $this->pre($docs['description']) . "\n";

        $hasOptions = false;
        if (isset($command['options']) && count($command['options'])) {
            $out .= "Available options:\n";
            foreach ($command['options'] as $opt) {
                $spaces = str_repeat(' ', 30 - strlen($opt['doc_name']));
                $out .= "  {$opt['doc_name']} {$spaces} : {$opt['description']}\n";
            }
            $hasOptions = true;
        }
        if (isset($command['arguments']) && count($command['arguments'])) {
            $out .= $hasOptions ? "\n": "";
            $out .= "Valid arguments:\n";
            foreach ($command['arguments'] as $arg) {
                $spaces = str_repeat(' ', 30 - strlen($arg['help_name']));
                $out .= "  {$arg['help_name']} {$spaces} : {$arg['description']}\n";
            }
        }
        if (isset($command['commands']) && count($command['commands'])) {
            $out .= "Valid commands:\n";
            foreach ($command['commands'] as $subcommand) {
                $spaces = str_repeat(' ', 30 - strlen($subcommand['name']));
                $out .= "  {$subcommand['name']} {$spaces} : {$subcommand['description']}\n";
            }
        }

        if ($verbose) {
            if (isset($docs['extradesc'])) {
                $out .= "\nExtended Documentation:";
                $out .= "\n  " . $this->pre(trim($docs['extradesc'])) . "\n";
            }
            if (isset($docs['examples'])) {
                $out .= "\nExamples:";
                $out .= "\n  " . $this->pre(trim($docs['examples'])) . "\n";
            }
        } else {
            $out .= "\nUse help with -v or --verbose option to get more information.\n";
        }

        return $out;
    }

    /**
     * Prepend all lines in array with intendation
     *
     * @param array $arr Array to combine
     *
     * @return string
     */
    protected function pre($arr)
    {
        return implode("\n  ", explode("\n", $arr));
    }


    /**
     * Phrozn CLI header
     *
     * @return string
     */
    protected function getHeader()
    {
        $meta = $this->getCommandMeta();
        return $out = "%P{$meta['name']} {$meta['version']} by {$meta['author']}\n%n";
    }

    /**
     * Phrozn CLI footer
     *
     * @return string
     */
    protected function getFooter()
    {
        $meta = $this->getCommandMeta();
        $out = "\n{$meta['description']}\n";
        $out .= "For additional information, see %9http://phrozn.info%n";
        return $out;
    }

    protected function pad($str)
    {
        return str_repeat(' ', strlen(Color::strip(Color::convert($str))));
    }

    /**
     * See whether given path is absolute or relative
     *
     * @param string $path Path to check
     *
     * @return boolean
     */
    protected function isAbsolute($path)
    {
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            $pattern = '/^[a-zA-z]:.*[^.lnk]$/';
            return preg_match($pattern, $path);
        } else {
            return $path{0} == '/';
        }
    }

    /**
     * Extract path argument or fallback to cwd
     *
     * @param string $name Name of the path argument
     * @param boolean $realpath Whether to apply realpath() to path
     * @param \Console_CommandLine_Result $command Command line result to use
     *
     * @return string
     */
    protected function getPathArgument($name, $realpath = true, $command = null)
    {
        if (null == $command) {
            $command = $this->getParseResult()->command;
        }
        $path = isset($command->args[$name]) ? $command->args[$name] : \getcwd();

        if (!$this->isAbsolute($path)) { // not an absolute path
            $path = \getcwd() . '/./' . $path;
        }

        if ($realpath) {
            $path = realpath($path);
        }

        return $path;
    }

    private function getCommandMeta()
    {
        if (null === $this->commandMeta) {
            $config = $this->getConfig();
            $this->commandMeta = Yaml::load($config['paths']['configs'] . 'phrozn.yml');
        }
        return $this->commandMeta;
    }


}

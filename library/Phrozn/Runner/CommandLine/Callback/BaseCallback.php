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
    Phrozn\Runner\CommandLine\Commands;

/**
 * Base methods for phrozn command callbacks
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class BaseCallback
{
    const STATUS_FAIL       = '  [%rFAIL%n]    ';
    const STATUS_ADDED      = '  [%gADDED%n]   ';
    const STATUS_DELETED    = '  [%gDELETED%n] ';

    /**
     * @var \Console_CommandLine
     */
    private $parser;

    /**
     * @var \Console_CommandLine_Result
     */
    private $result;

    /**
     * Contents of command-name.yml 
     */
    private $config;

    public function display($content, $header = true, $footer = true)
    {
        $config = $this->getConfig();
        $meta = Yaml::load($config['paths']['configs'] . 'phrozn.yml');

        $out = '';
        if ($header) {
            $out .= $this->header($meta);
        }
        $out .= $content;
        if ($footer) {
            $out .= $this->footer($meta);
        }

        $out = Color::convert($out);
        if ($meta['use_ansi_colors'] === false) {
            $out = Color::strip($out);
        }
        $this->getParser()->outputter->stdout($out);
    }

    /**
     * Main command line parser object
     *
     * @param Console_CommandLine $parser CLI Parser instance
     *
     * @return Phrozn\Runner\CommandLine\Callback
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * Get command line parser
     *
     * @return Console_CommandLine CLI Parser instance
     */
    public function getParser()
    {
        return $this->parser;
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
        $out .= 'usage: ' . $docs['usage'] . "\n";
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

        if ($verbose && isset($docs['examples'])) {
            $out .= "\nExamples:";
            $out .= "\n  " . $this->pre($docs['examples']) . "\n";
        }

        return $out;
    }

    protected function pre($arr)
    {
        return implode("\n  ", explode("\n", $arr));
    }


    protected function header($meta)
    {
        return $out = "%P{$meta['name']} {$meta['version']} by {$meta['author']}\n%n";
    }

    protected function footer($meta)
    {
        $out = "\n{$meta['description']}\n";
        $out .= "For additional information, see %9http://phrozn.info%n\n";
        return $out;
    }

    protected function pad($str)
    {
        return str_repeat(' ', strlen(Color::strip(Color::convert($str))));
    }

}

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
    Phrozn\Runner\CommandLine\Commands;

/**
 * CLI version of framework invoker.
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class CommandLine 
    extends \Phrozn\Runner\BaseRunner
    implements \Phrozn\Runner
{
    /**
     * Process the request
     *
     * @param \Zend\Loader\SplAutoloader $loader Instance of auto-loader
     *
     * @return void
     */
    public static function run(\Zend\Loader\SplAutoloader $loader)
    {
        $parser = self::createParser($loader);

        try {
            $result = $parser->parse();
        } catch (\Exception $e) {
            $parser->displayError($e->getMessage());
        }

        $runner = new self($parser, $result);
        $runner->execute();
    }

    private static function createParser($loader)
    {
        $meta = Yaml::load(PHROZN_PATH_CONFIGS . 'phrozn.yml');
        $parser = new CommandParser($meta['command']);

        // options
        foreach ($meta['command']['options'] as $name => $option) {
            // update callback with full class name
            if (isset($option['callback'])) {
                list($class, $method) = $option['callback'];
                $class = 'Phrozn\\Runner\\CommandLine\\Callback\\' . $class;
                $option['callback'] = array($class, $method);
            }
            $parser->addOption($name, $option);
        }

        // sub-commands
        $commands = Commands::getInstance();
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
}

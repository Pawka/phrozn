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
 * Extended help messages
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class HelpExtended 
    extends Base
{
    /**
     * Executes the action with the value entered by the user.
     *
     * @param mixed $value  The value of the option
     * @param Console_CommandLine_Option $option Parser option instance
     * @param Console_CommandLine $parser CLI Parser instance
     * @param Console_CommandLine_Result $result Parser's result
     * @param array $params Params passed from Yaml configuration
     *
     * @return string
     */
    public static function execute($value, $option, $result, $parser, $params = array())
    {
        $meta = Yaml::load(PHROZN_PATH_CONFIGS . 'phrozn.yml');
        self::header($parser, $meta);

        $topic = isset($result->command->args) ? $result->command->args['topic'] : null;

        if (null === $topic) {
            self::displayUsage($value, $option, $result, $parser, $params);
        } else {
            self::displayTopic($topic, $value, $option, $result, $parser, $params);
        }
        self::footer($parser, $meta);
    }

    private static function displayTopic($topic, $value, $option, $result, $parser, $params = array())
    {
        if ($help = self::combine($topic, $result->command->options['verbose'])) {
            return $parser->outputter->stdout(Color::convert($help));
        }
        $error = Color::convert("%rHelp topic '$topic' not found..%n\n");
        $parser->outputter->stdout($error);
    }

    private static function displayUsage($value, $option, $result, $parser, $params = array())
    {
        if (isset($params['use_colors']) && $params['use_colors'] === true) {
            $commands = Commands::getInstance();
            
            $out = "usage: %bphrozn%n %g<command>%n [options] [args]\n\n";
            $out .= "Type 'phrozn help <command>' for help on a specific command.\n";
            $out .= "Type 'phrozn ? help' for help on using help.\n";
            $out .= "Type 'phrozn --version' to see the program version and installed plugins.\n";

            $out .= "\nAvailable commands:\n";
            foreach ($commands as $name => $data) {
                $command = $data['command'];
                $out .= '    ' . $name;
                if (null !== $command['aliases']) {
                    $out .= ' (' . implode(', ', $command['aliases']) . ')';
                }
                $out .= "\n";
            }

            $parser->outputter->stdout(Color::convert($out));
        } else {
            return $parser->displayUsage();
        }
    }
}

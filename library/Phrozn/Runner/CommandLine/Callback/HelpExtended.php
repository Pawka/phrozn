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
 * @copyright   2010 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Runner\CommandLine\Callback;
use Console_Color as Color,
    Symfony\Component\Yaml\Yaml;

/**
 * Extended help messages
 *
 * @category    Phrozn
 * @package     Phrozn\Runner
 * @author      Victor Farazdagi
 */
class HelpExtended 
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
            $callback = array('Phrozn\Runner\CommandLine\Callback\HelpExtended', 'display' . ucfirst($topic));
            if (is_callable($callback)) {
                call_user_func($callback, $value, $option, $result, $parser, $params);
            } else {
                $error = Color::convert("%rHelp topic '$topic' not found..%n\n");
                $parser->outputter->stdout($error);
            }
        }
        self::footer($parser, $meta);
    }

    private static function displayInit($value, $option, $result, $parser, $params = array())
    {
        $help = self::combine('init');
        $parser->outputter->stdout(Color::convert($help));
    }

    private static function displayUsage($value, $option, $result, $parser, $params = array())
    {
        if (isset($params['use_colors']) && $params['use_colors'] === true) {
            $commands = Yaml::load(PHROZN_PATH_CONFIGS . 'commands.yml');
            
            $out = "usage: %bphrozn%n %g<subcommand>%n [options] [args]\n\n";
            $out .= "Type 'phrozn help <subcommand>' for help on a specific subcommand.\n";
            $out .= "Type 'phrozn --version' to see the program version and installed plugins.\n";

            $out .= "\nAvailable subcommands:\n";
            foreach ($commands as $name => $command) {
                $out .= '    ' . $name;
                if (null !== $command['short_name']) {
                    $out .= " (${command['short_name']})";
                }
                $out .= "\n";
            }

            $parser->outputter->stdout(Color::convert($out));
        } else {
            return $parser->displayUsage();
        }
    }

    private static function combine($file)
    {
        $file = PHROZN_PATH_DOCS . 'phr-' . $file . '.yml';
        $docs = Yaml::load($file);

        $out = '';
        $out .= sprintf("%s: %s\n", $docs['name'], $docs['summary']);
        $out .= '%busage:%n ' . $docs['usage'] . "\n";
        $out .= "\n  " . implode("\n  ", explode("\n", $docs['description'])) . "\n";

        return $out;
    }

    private static function header($parser, $meta)
    {
        $out = "%P{$meta['name']} {$meta['version']} by {$meta['author']}\n%n";
        $parser->outputter->stdout(Color::convert($out));
    }

    private static function footer($parser, $meta)
    {
        $out = "\n{$meta['description']}\n";
        $out .= "For additional information, see %9http://phrozn.info%n\n";
        $parser->outputter->stdout(Color::convert($out));
    }

}

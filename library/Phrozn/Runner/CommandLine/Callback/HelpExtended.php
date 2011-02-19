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
use Console_Color as Color;

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
        $topic = isset($result->command->args) ? $result->command->args['topic'] : null;

        $parser->outputter->stdout(Color::convert('%P' . $parser->description . '%n') . "\n\n");

        if (null === $topic) {
            return self::displayUsage($value, $option, $result, $parser, $params);
        } else {
            $callback = array('Phrozn\Runner\CommandLine\Callback\HelpExtended', 'display' . ucfirst($topic));
            if (is_callable($callback)) {
                var_dump($topic);
            } else {
                $error = Color::convert("%rHelp topic '$topic' not found..%n\n");
                $parser->outputter->stdout($error);
            }
        }
    }

    private static function displayInit($value, $option, $result, $parser, $params = array())
    {
        $parser->outputter->stdout('init help');
    }

    private static function displayUsage($value, $option, $result, $parser, $params = array())
    {
        if (isset($params['use_colors']) && $params['use_colors'] === true) {
            $out = "Usage:\n  phrozn command [option]";
            $out .= "\n";
            $parser->outputter->stdout($out);
        } else {
            return $parser->displayUsage();
        }
    }
}

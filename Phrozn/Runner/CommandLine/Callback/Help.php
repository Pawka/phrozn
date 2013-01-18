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
use Phrozn\Outputter\Console\Color,
    Symfony\Component\Yaml\Yaml,
    Phrozn\Runner\CommandLine;

/**
 * Extended help messages
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Help
    extends Base
    implements CommandLine\Callback
{
    /**
     * Executes the callback action
     *
     * @return string
     */
    public function execute()
    {
        $out = '';

        $topic = isset($this->getParseResult()->command->args['topic'])
               ? $this->getParseResult()->command->args['topic'] : null;
        if (null === $topic) {
            $out = $this->getUsageHelp();
        } else {
            $out = $this->getTopicHelp($topic);
        }

        $this->out($this->getHeader());
        $this->out($out);
        $this->out($this->getFooter());
    }

    private function getTopicHelp($topic)
    {
        if ($help = $this->combine($topic, $this->getParseResult()->command->options['verbose'])) {
            return $help;
        }
        return "%rHelp topic '$topic' not found..%n\n";
    }

    private function getUsageHelp()
    {
        $commands = $this->sortCommands(CommandLine\Commands::getInstance());

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

        return $out;
    }

    private function sortCommands($iter)
    {
        $commands = array();
        foreach ($iter as $name => $data) {
            $commands[$name] = $data;
        }
        ksort($commands);
        return $commands;
    }
}

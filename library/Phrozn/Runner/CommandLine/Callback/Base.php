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
class Base 
{
    protected static function combine($file, $verbose = false)
    {
        $file = PHROZN_PATH_CONFIGS . 'commands/' . $file . '.yml';
        $data = Yaml::load($file);

        if ($data === $file) {
            return false;
        }
        $docs = $data['docs'];
        $command = $data['command'];

        $out = '';
        $out .= sprintf("%s: %s\n", $docs['name'], $docs['summary']);
        $out .= 'usage: ' . $docs['usage'] . "\n";
        $out .= "\n  " . self::pre($docs['description']) . "\n";
        if ($verbose && isset($docs['examples'])) {
            $out .= 'examples:';
            $out .= "\n  " . self::pre($docs['examples']) . "\n";
        }

        if (isset($command['options']) && count($command['options'])) {
            $out .= "Available options:\n";
            foreach ($command['options'] as $opt) {
                $spaces = str_repeat(' ', 30 - strlen($opt['doc_name']));
                $out .= "  {$opt['doc_name']} {$spaces} : {$opt['description']}\n";
            }
        }

        return $out;
    }

    protected static function pre($arr)
    {
        return implode("\n  ", explode("\n", $arr));
    }


    protected static function header($parser, $meta)
    {
        $out = "%P{$meta['name']} {$meta['version']} by {$meta['author']}\n%n";
        $parser->outputter->stdout(Color::convert($out));
    }

    protected static function footer($parser, $meta)
    {
        $out = "\n{$meta['description']}\n";
        $out .= "For additional information, see %9http://phrozn.info%n\n";
        $parser->outputter->stdout(Color::convert($out));
    }

}

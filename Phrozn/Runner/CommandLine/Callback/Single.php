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
    Phrozn\Runner\CommandLine,
    Phrozn\Site\PieceOfSite as Site;

/**
 * phrozn up command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Walter Dal Mut
 */
class Single
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
        try {
            $this->updateFile();
        } catch (\Exception $e) {
            $this->out(self::STATUS_FAIL . $e->getMessage());
            $this->out($this->getFooter());
        }
    }

    private function updateFile()
    {
        list($file, $in, $out) = $this->getPaths();

        ob_start();
        $this->out($this->getHeader());
        $this->out("Starting static file compilation.\n");

        $proceed = true;
        if (!is_dir($in)) {
            $this->out(
                self::STATUS_FAIL . "Source directory '{$in}' not found.");
            $proceed = false;
        } else {
            $this->out(self::STATUS_OK . "Source directory located: {$in}");
        }
        if (!is_dir($out)) {
            $this->out(
                self::STATUS_FAIL . "Destination directory '{$out}' not found.");
            $proceed = false;
        } else {
            $this->out(self::STATUS_OK . "Destination directory located: {$out}");
        }
        if (!is_file($file)) {
            $this->out(
                self::STATUS_FAIL . "Source file '{$file}' not found.");
            $proceed = false;
        } else {
            $this->out(self::STATUS_OK . "Source file located: {$file}");
        }

        if ($proceed === false) {
            $this->out($this->getFooter());
            return;
        }

        $site = new Site($in, $out);
        $site->setSingleFile($file);
        $site
            ->setOutputter($this->getOutputter())
            ->compile();

        $this->out($this->getFooter());

        ob_end_clean();
    }

    private function getPaths()
    {
        $in = $out = null;

        $file = $this->getParseResult()->command->args['file'];
        $in  = $this->getPathArgument('in');
        $out = $this->getPathArgument('out');

        if (strpos($in, '.phrozn') === false) {
            return array(
                $in . '/.phrozn/entries/' . $file,
                $in . '/.phrozn/',
                $out . '/'
            );
        } else {
            return array(
                $in . '/' . $file,
                $in . '/',
                $out . '/../'
            );
        }
    }
}


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
    Phrozn\Config,
    Phrozn\Path\Project as ProjectPath,
    Symfony\Component\Yaml\Yaml,
    Console_Table as ConsoleTable,
    Phrozn\Bundle\Service as BundleService;

/**
 * phrozn bundle command
 *
 * @category    Phrozn
 * @package     Phrozn\Runner\CommandLine
 * @author      Victor Farazdagi
 */
class Bundle
    extends Base
    implements CommandLine\Callback
{
    /**
     * @var \Phrozn\Bundle\Service
     */
    private $service;

    /**
     * List of available sub-commands
     * @var array
     */
    private $availableCommands = array(
        'apply', 'list', 'info', 'clobber'
    );

    public function __construct()
    {
        $this->service = new BundleService();
    }

    /**
     * Executes the callback action
     *
     * @return void
     */
    public function execute()
    {
        if (false === $this->getCommand()) {
            $this->out($this->getHeader());
            $this->out(self::STATUS_FAIL . "No sub-command specified. Use 'phr ? bundle' for more info.");
            $this->out($this->getFooter());
        }

        // setup service
        $config = $this->getConfig();
        $this->service->setConfig(
            new Config($config['paths']['configs'] . 'bundles.yml'));

        if (isset($this->getParseResult()->command->command_name)) {
            $command = $this->getParseResult()->command->command_name;
            if (in_array($command, $this->availableCommands)) {
                $this->out($this->getHeader());
                try {
                    $this->{'exec' . ucfirst($command)}();
                } catch (\Exception $e) {
                    $this->out(self::STATUS_FAIL . $e->getMessage());
                }
                $this->out($this->getFooter());
            }
        }
    }

    /**
     * Wraps Console color codes around $value,
     * depending if its larger or smaller 0.
     *
     * @param float $value Value (column 1)
     *
     * @return string Colorful value
     */
    public function highlightSearchTerm($value)
    {
        $que = $this->getBundleParam();
        $value = preg_replace('/(' . preg_quote($que). ')/si', '[\1]', $value);
        return $value;
    }

    /**
     * List bundles
     *
     * @return void
     */
    private function execList()
    {
        $pathArg = $this->getPathArgument('path', false, $this->getCommand());
        $path = new ProjectPath($pathArg);

        $que = $this->getBundleParam(); // user searches for a certain bundle
        $bundles = $this->service
            ->setProjectPath($path)
            ->getBundles(
                $this->getTypeParam(), $this->getBundleParam());

        $this->out("Located project folder: {$path->get()}\n");
        if (is_dir($path->get()) === false) {
            throw new \RuntimeException("No project found at {$pathArg}");
        }

        $tbl = new ConsoleTable();
        $tbl->setHeaders(
            array('S', 'Id', 'Version', 'Author', 'Description')
        );
        foreach ($bundles as $bundle) {
            if (isset($bundle['hide']) && $bundle['hide'] == true) {
                continue;
            }
            $tbl->addRow(array(
                $this->service->getRegistryContainer()->isInstalled($bundle['id']) ? 'i' : 'p',
                $bundle['id'],
                $bundle['version'],
                $bundle['author'],
                $bundle['description'],
            ));
        }
        if (strlen($que)) { // hi-light search results
            $callback = array($this, 'highlightSearchTerm');
            $tbl->addFilter(1, $callback);
            $this->out(sprintf("Search bundles having \"%s\"..", $que));
        }
        $tbl->setAlign(3, CONSOLE_TABLE_ALIGN_CENTER);
        $this->out($tbl->getTable()) ;
    }

    /**
     * Get info about specific bundle
     *
     * @return void
     */
    private function execInfo()
    {
        $tbl = new ConsoleTable();
        $tbl->setHeaders(
            array('Param', 'Value')
        );
        $bundle = $this->service
                       ->getBundleInfo($this->getBundleParam());
        if (is_array($bundle)) {
            foreach ($bundle as $param => $value) {
                $tbl->addRow(array(
                    $param, $value
                ));
            }
        }
        $this->out($tbl->getTable());
    }

    /**
     * Apply bundle
     *
     * @return void
     */
    private function execApply()
    {
        $pathArg = $this->getPathArgument('path', false, $this->getCommand());
        $path = new ProjectPath($pathArg);
        $bundle = $this->getBundleParam();

        $files = $this->service->getBundleFiles($bundle);
        if (!is_array($files) || !count($files)) {
            throw new \RuntimeException('Invalid or empty bundle');
        }

        $this->out("Located project folder: {$path->get()}\n");
        if (is_dir($path->get()) === false) {
            throw new \RuntimeException("No project found at {$pathArg}");
        }

        $this->out('Bundle content:');
        foreach ($files as $file) {
            // Archive_Tar defines dir as typeflag 5
            if(in_array($file['typeflag'], array(5)) === false) {
                $this->out('    ' . $file['filename']);
            }
        }

        $this->out("\nDo you wish to install this bundle?");
        if ($this->readLine() === 'yes') {
            $this
                ->service
                ->setProjectPath($path)
                ->applyBundle($bundle);
            $this->out(self::STATUS_OK . " Done..");
        } else {
            $this->out(self::STATUS_FAIL . " Aborted..");
        }
    }

    /**
     * Clobber bundle
     *
     * @return void
     */
    private function execClobber()
    {
        $pathArg = $this->getPathArgument('path', false, $this->getCommand());
        $path = new ProjectPath($pathArg);
        $bundle = $this->getBundleParam();

        $files = $this->service->getBundleFiles($bundle);

        $this->out("Located project folder: {$path->get()}\n");
        if (is_dir($path->get()) === false) {
            throw new \RuntimeException("No project found at {$pathArg}");
        }

        $this->out('Bundle content:');
        foreach ($files as $file) {
            // Archive_Tar defines dir as typeflag 5
            if(in_array($file['typeflag'], array(5)) === false) {
                $this->out('    ' . $file['filename']);
            }
        }

        $this->out(
            "\nBundle files are to be removed.\n" .
            "This operation %rCAN NOT%n be undone.\n");
        if ($this->readLine() === 'yes') {
            $this
                ->service
                ->setProjectPath($path)
                ->clobberBundle($bundle);
            $this->out(self::STATUS_OK . " Done..");
        } else {
            $this->out(self::STATUS_FAIL . " Aborted..");
        }
    }

    /**
     * Get type -i, -n, -a of bundles to work on
     *
     * @return string
     */
    private function getTypeParam()
    {
        $options = $this->getCommand()->options;
        $type = \Phrozn\Bundle::TYPE_ALL;
        if ($options['installed']) {
            $type = \Phrozn\Bundle::TYPE_INSTALLED;
        } else if ($options['available']) {
            $type = \Phrozn\Bundle::TYPE_AVAILABLE;
        }
        return $type;
    }

    /**
     * Extract bundle name/uri argument
     *
     * @return string
     */
    private function getBundleParam()
    {
        $bundle = null;
        if (null !== $this->getCommand()->args["bundle"]) {
            $bundle = $this->getCommand()->args["bundle"];
        }
        return $bundle;
    }

    /**
     * Handy short-cut to subcommand
     *
     * @return Console_CommandLine_Result
     */
    private function getCommand()
    {
        return $this->getParseResult()->command->command;
    }
}

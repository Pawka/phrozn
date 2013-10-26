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
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site;
use Phrozn\Site\View;

/**
 * Default implementation of Phrozn Site
 *
 * @category    Phrozn
 * @package     Phrozn\Site
 * @author      Victor Farazdagi
 */
class DefaultSite
    extends Base
    implements \Phrozn\Site
{
    /**
     * Create static version of site.
     * Ideally, only parts that changed should be recompiled into Phrozn site.
     *
     * @return void
     */
    public function compile()
    {
        $this
            ->buildQueue()
            ->processQueue()
            ->copyStatic();
    }

    /**
     * Process view by view compilation
     *
     * @return \Phrozn\Site
     */
    private function processQueue()
    {
        $vars = array();

        // render textual (markup, css, templates) files
        foreach ($this->getQueue() as $view) {
            $inputFile = str_replace(getcwd(), '.', $view->getInputFile());
            $outputFile = str_replace(getcwd(), '.', $view->getOutputFile());
            try {
                if ($view->getParam('page.skip', false)) {
                    $this->getOutputter()
                        ->stdout('%b' . $inputFile . '%n %rSKIPPED%n');
                } else {
                    $view->compile($vars);
                    $this->getOutputter()
                        ->stdout('%b' . $inputFile . '%n parsed')
                        ->stdout('%b' . $outputFile . '%n written');
                }
            } catch (\Exception $e) {
                $this->getOutputter()
                     ->stderr($inputFile . ': ' . $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Some files are just copied over w/o any additional processing.
     * Media files, for example.
     * You can add more folders and files to be processed using `config.yml` `copy` option.
     *
     * @return \Phrozn\Site
     */
    private function copyStatic()
    {
        $config = $this->getSiteConfig();

        if (isset($config['copy'])) {
            $to_copy = (array) $config['copy'];
        } else {
            $to_copy = array();
        }

        // media folder is hardcoded into copy
        // maybe we should remove this when we can break BC
        $to_copy = array_merge(array('media'), $to_copy);

        $to_copy = array_unique($to_copy);

        foreach ($to_copy as $folder) {
            $folderInfo = new \SplFileInfo($this->getProjectDir() . DIRECTORY_SEPARATOR . $folder);
            $this->copyFolder($folderInfo);
        }

        return $this;
    }

    /**
     * Files are just copied over w/o any additional processing.
     * Some files are skipped, as per `config.yml` `skip` regexes.
     * @param \SplFileInfo $folder
     *
     * @return void
     */
    private function copyFolder($folder)
    {
        if (!$folder->isDir()) {
            return;
        }

        $config = $this->getSiteConfig();

        $inDir  = new \SplFileInfo($this->getProjectDir());
        $outDir = new \SplFileInfo($this->getOutputDir());

        $dir = new \RecursiveDirectoryIterator($folder->getRealPath());
        $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $item) {
            $this->tryToCopyFile($item, $inDir, $outDir, isset($config['skip']) ? $config['skip'] : array());
        }
    }

    /**
     * Tries to copy $file as-is from the input directory to the output directory.
     * It will skip copy if filename matches one of $skip regexes, or if $file is a folder.
     *
     * Introduces a dependency on the SPL extension, but as the doc states :
     * "As of PHP 5.3.0 this extension can no longer be disabled and is therefore always available."
     *
     * @param \SplFileInfo $file
     * @param \SplFileInfo $inDir
     * @param \SplFileInfo $outDir
     * @param string[] $skip Array of regexes
     * @throws \RuntimeException
     */
    private function tryToCopyFile($file, $inDir, $outDir, $skip=array())
    {
        // collect info
        $baseName  = $file->getBaseName();
        $inputFile = $file->getRealPath();
        $inputDir  = $inDir->getRealPath();
        $outputDir = $outDir->getRealPath();

        // skip if not a file
        if (!$file->isFile()) {
            return;
        }

        // skip if matches any skip regex
        if (count($skip)) {
            // this is smart, but not cool. What if I want foo-!SKIP!-bar.jpg ? Speed does not matter THAT much !
            $skipToken = '-!SKIP!-';
            $baseName = preg_replace($skip, array_fill(0, count($skip), $skipToken), $baseName);
            if (strpos($baseName, $skipToken) !== false) {
                return;
            }
        }

        // sanity check -- REALLY not supposed to happen
        if (strpos($inputFile, $inputDir) !== 0) {
            throw new \RuntimeException(sprintf('File "%s" is not a child of input folder "%s"', $inputFile, $inputDir));
        }

        $relativePath = substr($inputFile, strlen($inputDir)+1);
        $outputFile = $outputDir . DIRECTORY_SEPARATOR . $relativePath;

        // copy the file
        try {
            $destinationDir = dirname($outputFile);
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            if (!copy($inputFile, $outputFile)) {
                throw new \RuntimeException(sprintf('Failed copy to "%s"', $outputFile));
            }
            $cwd = realpath(getcwd());
            $inputFile  = str_replace($cwd, '.', $inputFile);
            $outputFile = str_replace($cwd, '.', $outputFile);
            $this->getOutputter()
                ->stdout('%b' . $outputFile . '%n copied');
        } catch (\Exception $e) {
            $this->getOutputter()
                ->stderr($inputFile . ': ' . $e->getMessage());
        }
    }
}

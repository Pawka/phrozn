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
            ->processMedia();
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
     * Media files are just copied over w/o any additional processing
     *
     * @return \Phrozn\Site
     */
    private function processMedia()
    {
        $projectDir = $this->getProjectDir();
        $outputDir = $this->getOutputDir();

        $dir = new \RecursiveDirectoryIterator($projectDir . '/media');
        $it = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $item) {
            $baseName = $item->getBaseName();
            if ($item->isFile()) {
                $inputFile = $item->getRealPath();

                $path = $it->getSubPath();

                $outputFile = $outputDir . '/media/' . $path
                            . (!empty($path) ? '/' : '')
                            . basename($inputFile);

                // copy media files
                try {
                    $destinationDir = dirname($outputFile);
                    if (!is_dir($destinationDir)) {
                        mkdir($destinationDir, 0777, true);
                    }
                    if (!copy($inputFile, $outputFile)) {
                        throw new \RuntimeException(sprintf('Failed transfering "%s" from media folder', $inputFile));
                    }
                    $inputFile = str_replace(getcwd(), '.', $inputFile);
                    $outputFile = str_replace(getcwd(), '.', realpath($outputFile));
                    $this->getOutputter()
                         ->stdout('%b' . $outputFile . '%n copied');
                } catch (\Exception $e) {
                    $this->getOutputter()
                         ->stderr($inputFile . ': ' . $e->getMessage());
                }
            }
        }

    }
}

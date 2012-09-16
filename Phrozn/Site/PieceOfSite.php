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
 * @author      Walter Dal Mut
 */
class PieceOfSite
    extends DefaultSite
    implements \Phrozn\Site
{
    private $single;

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
            ->processQueue();
    }

    public function setSingleFile($file)
    {
        $this->single = $file;
    }

    public function getSingleFile()
    {
        return $this->single;
    }

    protected function buildQueue()
    {
        $projectDir = $this->getProjectDir();
        $outputDir = $this->getOutputDir();

        $item = new \SplFileObject($this->getSingleFile());
        if ($item->isFile()) {
            try {
                $factory = new View\Factory($item->getRealPath());
                $view = $factory->create();
                $view
                    ->setSiteConfig($this->getSiteConfig())
                    ->setOutputDir($outputDir);
                $this->addView($view);
            } catch (\Exception $e) {
                $this->getOutputter()
                    ->stderr(str_replace($projectDir, '', $item->getRealPath()) . ': ' . $e->getMessage());
            }
        }

        return $this;
    }

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

}

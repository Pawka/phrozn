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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\View\OutputPath;
use Phrozn\Site\View;

/**
 * Base implementation of output path builder
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
abstract class Base
    implements View\OutputPath
{
    /**
     * Reference to \Phrozn\Site\View object
     * @var \Phrozn\Site\View
     */
    private $view;

    /**
     * Initialize path builder
     *
     * @param \Phrozn\Site\View View for which output path needs to be determined
     *
     * @return void
     */
    public function __construct(\Phrozn\Site\View $view)
    {
        $this->setView($view);
    }

    /**
     * Set view
     *
     * @param \Phrozn\Site\View $view View object
     *
     * @return \Phrozn\Site\View\OutputPath
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get view
     *
     * @return \Phrozn\Site\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Extract input file from view (remove extension)
     *
     * @return string
     */
    protected function getInputFileWithoutExt()
    {
        $info = pathinfo($this->getView()->getInputFile());

        return $info['dirname']
            . DIRECTORY_SEPARATOR
            . ($info['filename']?:$info['basename']); // allow dot files
    }

    /**
     * Get the file extension for the input file
     *
     * @return string
     */
    protected function getInputFileExtension($includeDot = true)
    {
        $extension = pathinfo($this->getView()->getInputFile(), PATHINFO_EXTENSION);

        if ($includeDot && $extension != '') {
            return '.' . $extension;
        }

        return $extension;
    }

    /**
     * Detect relative file path with respect to $base folder
     *
     * @param string $base Base folder name from which to start
     * @param boolean $prepend Whether to prepend base folder name to result
     *
     * @return string
     */
    protected function getRelativeFile($base = '', $prepend = true)
    {
        // find file path w/o extension
        $inputFile = $this->getInputFileWithoutExt();
        $inputRoot = $this->getView()->getInputRootDir();

        // Remove the input root from the input filename
        $inputFile = str_replace($inputRoot, '', $inputFile);

        // find relative path, wrt to output root dir
        if ($base) {
            $pos = strpos($inputFile, $base);
            if ($pos !== false) {
                $inputFile = substr($inputFile, $pos + ($prepend ? 0 : 1 + strlen($base)));
            }
        }

        return $inputFile;
    }
}

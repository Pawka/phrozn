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
 * Output path builder for site passthrough (no processing) entries
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class Plain
    extends Base
{
    /**
     * Get calculated path
     *
     * @return string
     */
    public function get()
    {
        $permalink = $this->getView()->getParam('this.permalink', null);

        if ($permalink === null) {
            return rtrim($this->getView()->getOutputDir(), '/')
                . '/'
                . ltrim($this->getRelativeFile('entries', false), '/')
                . $this->getInputFileExtension();
        }

        $class = 'Phrozn\\Site\\View\\OutputPath\\Entry\\' . ucfirst($permalink);
        if (!class_exists($class)) {
            $class = 'Phrozn\\Site\\View\\OutputPath\\Entry\\Parametrized';
        }

        $object = new $class($this->getView());
        return preg_replace("/\.html?/", "", $object->get());

    }
}

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

namespace Phrozn\Site\View\OutputPath\Entry;
use Phrozn\Site\View;

/**
 * Output path builder for site entries. Build output path using passed variables
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class Parametrized
    extends View\OutputPath\Base
{
    /**
     * Get calculated path
     *
     * @return string
     */
    public function get()
    {
        $path = $this->getView()->getParam('this.permalink');
        $params = $this->getView()->getParams();
        $permalink = $this->getView()->getParam('this.permalink', null);

        $params = array_merge($params['site'], $params['page']);
        foreach ($params as $name => $param) {
            // apply only scalar params
            if (is_scalar($param)) {
                $path = str_replace(':' . $name, $this->normalize($param), $path);
            }
        }

        $path = rtrim($this->getView()->getOutputDir(), '/') . '/'
              . ltrim($path, '/');

        if (substr($path, -1) === '/') {
            $path .= 'index.html';
        } else if (is_null($permalink) && substr($path, -5) !== '.html') {
            $path .= '.html';
        }

        return $path;
    }

    /**
     * Normalize parameter to be used in human readable URL
     *
     * @param string $param Parameter to normalize
     * @param string $space What to use as space separator
     *
     * @return string
     */
    private function normalize($param, $space = '-')
    {
        $param = trim($param);
        // preserve accented chars
        if (function_exists('iconv')) {
            $param = @iconv('utf-8', 'us-ascii//TRANSLIT', $param);
        }
        $param = preg_replace('/[^a-zA-Z0-9 -]/', '', $param);
        $param = strtolower($param);
        $param = preg_replace('/[\s-]+/', $space, $param);

        return $param;
    }
}

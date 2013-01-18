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
 * @package     Phrozn\Path
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Path;
use Phrozn\Path;

/**
 * Project path
 *
 * @category    Phrozn
 * @package     Phrozn\Path
 * @author      Victor Farazdagi
 */
class Project
    extends Base
    implements Path
{
    /**
     * Get calculated path
     *
     * @return string
     */
    public function get()
    {
        if (null === $this->path) {
            throw new \RuntimeException('Path not set.');
        }
        $path = $this->path;
        for ($i = 0, $mx = substr_count($path, DIRECTORY_SEPARATOR); $i <= $mx; $i++) {
            if (is_dir($path . DIRECTORY_SEPARATOR . '.phrozn')) {
                return realpath($path . DIRECTORY_SEPARATOR . '.phrozn');
            }
            $path =  dirname($path);
        }
        return '';
    }
}

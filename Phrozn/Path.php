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

namespace Phrozn;

/**
 * If path location requires some complex logic wrap them into separate class.
 *
 * @category    Phrozn
 * @package     Phrozn\Path
 * @author      Victor Farazdagi
 */
interface Path
{
    /**
     * Set source path
     *
     * @param string $path Source path
     *
     * @return \Phrozn\Path
     */
    public function set($path);

    /**
     * Get calculated path
     *
     * @return string
     */
    public function get();

    /**
     * Convert object to string
     *
     * @return string
     */
    public function __toString();
}

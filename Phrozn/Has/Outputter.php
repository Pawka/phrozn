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
 * @package     Phrozn\Has
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Has;

/**
 * Entity has outputter propety
 *
 * @category    Phrozn
 * @package     Phrozn\Has
 * @author      Victor Farazdagi
 */
interface Outputter
{
    /**
     * Set outputter
     *
     * @param \Phrozn\Outputter $outputter Outputter instance
     *
     * @return \Phrozn\Has\Outputter
     */
    public function setOutputter($outputter);

    /**
     * Get outputter instance
     *
     * @return \Phrozn\Outputter
     */
    public function getOutputter();
}

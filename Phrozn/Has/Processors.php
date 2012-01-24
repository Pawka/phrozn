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
 * Entity has list of text processors
 *
 * @category    Phrozn
 * @package     Phrozn\Has
 * @author      Victor Farazdagi
 */
interface Processors
{
    /**
     * Add text processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Has\Processors
     */
    public function addProcessor($processor);

    /**
     * Remove text processor
     *
     * @param \Phrozn\Processor
     *
     * @return \Phrozn\Has\Processors
     */
    public function removeProcessor($processor);

    /**
     * Get list of registered processors
     *
     * @return array of \Phrozn\Processor items
     */
    public function getProcessors();
}

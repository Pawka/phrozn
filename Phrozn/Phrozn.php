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
 * @package     Phrozn
 * @author      Povilas Balzaravičius
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Phrozn\Command\InitCommand;
use Phrozn\Command\ClobberCommand;
use Phrozn\Command\SingleCommand;
use Phrozn\Command\BuildCommand;

/**
 * Phrozn Application
 */
class Phrozn extends Application
{

    public function __construct()
    {
        parent::__construct("Phrozn", "1.0");
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $list = parent::getDefaultCommands();

        $list[] = new InitCommand;
        $list[] = new ClobberCommand;
        $list[] = new SingleCommand;
        $list[] = new BuildCommand;

        return $list;
    }
}

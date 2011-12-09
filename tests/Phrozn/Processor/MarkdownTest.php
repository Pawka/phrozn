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
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace PhroznTest\Processor;
use Phrozn\Processor\Markdown as Processor;

/**
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class MarkdownTest
    extends \PHPUnit_Framework_TestCase
{
    private $path;

    public function setUp()
    {
        $this->path = dirname(__FILE__) . '/templates/';
    }

    public function testRender()
    {
        $processor = new Processor();
        $tpl = file_get_contents($this->path . 'tpl1.markdown');
        $rendered = $processor->render($tpl);
        $static = file_get_contents($this->path . 'tpl1.markdown.html');
        $this->assertSame(trim($static), trim($rendered));
    }
}

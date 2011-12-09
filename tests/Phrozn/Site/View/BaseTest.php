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

namespace PhroznTest\Site\View;
use PhroznTest\Site\View\MockView as View;

/**
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class BaseTest
    extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {}

    public function testViewCreation()
    {
        $in = dirname(__FILE__) . '/../project/.phrozn/entries/fm-parsing.txt';
        $out = dirname(__FILE__) . '/out';
        $view = new View($in , $out);

        $this->assertInstanceOf('\PhroznTest\Site\View\MockView', $view);
    }

    public function testViewFrontMatterParsing()
    {
        $css = dirname(__FILE__) . '/../project/.phrozn/entries/fm-parsing.txt';
        $view = new View($css);
        $fm = $view->getFrontMatter();
        $this->assertTrue(true); // in non fixed version this throws InvalidArgument exception
    }
}

class MockView
    extends \Phrozn\Site\View\Base
    implements \Phrozn\Site\View
{
    /**
     * Initialize view
     *
     * @param string $inputFile Path to view source file
     * @param string $outputDir File destination path
     *
     * @return \Phrozn\Site\View
     */
    public function __construct($inputFile = null, $outputDir = null)
    {
        parent::__construct($inputFile, $outputDir);
        $this->addProcessor(new \Phrozn\Processor\Plain());
    }
}

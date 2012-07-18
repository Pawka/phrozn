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
 * @package     Phrozn\Outputter\Console
 * @author      Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Outputter\Console;
use Console_Color2 as ConsoleColorer;

/**
 * Due to wrong documentation Console_Color methods were used as static.
 * Anticipating update in code rather than in docs (fix is trivial and
 * BC will be honored) this class serves as adapter until this moment comes.
 *
 * @category    Phrozn
 * @package     Phrozn\Outputter\Console
 * @author      Victor Farazdagi
 */
class Color
{
    /**
     * @var \Phrozn\Outputter\Console\Color
     */
    private static $instance;

    /**
     * @var \Console_Color
     */
    private $consoleColorer;


    /**
     * Initialize Console_Color dependency
     *
     * @return void
     */
    private function __construct()
    {
        $this->consoleColorer = new ConsoleColorer;
    }

    /**
     * Singleton. Cloning is explicitly disallowed.
     */
    private function __clone()
    {}

    /**
     * Get instance of included Console_Color
     *
     * @return \Console_Color
     */
    public function getConsoleColorer()
    {
        return $this->consoleColorer;
    }

    /**
     * Get singleton instance
     *
     * @return \Phrozn\Outputter\Console\Color
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * converts colorcodes in the format %y (for yellow) into ansi-control
     * codes. the conversion table is: ('bold' meaning 'light' on some
     * terminals). it's almost the same conversion table irssi uses.
     * <pre>
     *                  text      text            background
     *      ------------------------------------------------
     *      %k %k %0    black     dark grey       black
     *      %r %r %1    red       bold red        red
     *      %g %g %2    green     bold green      green
     *      %y %y %3    yellow    bold yellow     yellow
     *      %b %b %4    blue      bold blue       blue
     *      %m %m %5    magenta   bold magenta    magenta
     *      %p %p       magenta (think: purple)
     *      %c %c %6    cyan      bold cyan       cyan
     *      %w %w %7    white     bold white      white
     *
     *      %f     blinking, flashing
     *      %u     underline
     *      %8     reverse
     *      %_,%9  bold
     *
     *      %n     resets the color
     *      %%     a single %
     * </pre>
     * first param is the string to convert, second is an optional flag if
     * colors should be used. it defaults to true, if set to false, the
     * colorcodes will just be removed (and %% will be transformed into %)
     *
     * @param string $string  string to convert
     * @param bool   $colored should the string be colored?
     *
     * @access public
     * @return string
     */
    public static function convert($string, $colored = true)
    {
        return self::getInstance()
            ->getConsoleColorer()
            ->convert($string, $colored);
    }

    /**
     * Escapes % so they don't get interpreted as color codes
     *
     * @param string $string String to escape
     *
     * @access public
     * @return string
     */
    public static function escape($string)
    {
        return self::getInstance()
            ->getConsoleColorer()
            ->escape($string);
    }

    /**
     * Strips ANSI color codes from a string
     *
     * @param string $string String to strip
     *
     * @acess public
     * @return string
     */
    public static function strip($string)
    {
        return self::getInstance()
            ->getConsoleColorer()
            ->strip($string);
    }

}

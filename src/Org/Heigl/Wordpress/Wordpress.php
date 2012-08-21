<?php
/**
 * Copyright (c) 2012-2012 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version 1.0.1
 * @since      17.08.2012
 */

namespace Org\Heigl\Wordpress;

/**
 * This is a simple wrapper for wordpress-specific-calls.
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version 1.0.1
 * @since      17.08.2012
 */
class Wordpress
{
    /**
     * Redirect unknown methods to their equivalend global functions
     *
     * @param string $method The name of the method to be called
     * @param array  $params The parameters array
     *
     * @return mixed
     */
    public static function __callstatic($method, $params)
    {
        if (! function_exists($method)) {
            return false;
        }
        return call_user_func_array($method, $params);
    }
}

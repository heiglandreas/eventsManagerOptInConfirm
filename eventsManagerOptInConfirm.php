<?php
/**
 * Copyright 2012-2012 Andreas Heigl <andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * Plugin Name: eventsManagerOptInConfirm
 * Plugin URI: https://github.com/heiglandreas/eventsManagerOptInConfirm
 * Description: Opt-In Confirmation for Bookings in Events Manager
 * Version: 1.0.7
 * Author: Andreas Heigl
 * Author URI: http://andreas.heigl.org
 * License: MIT
 *
 * @package   \Org\Heigl\Wordpress\EventsManager\OptInConfirm
 * @author    Andreas Heigl <andresa@heigl.org>
 * @copyright 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   1.0.7
 * @since     17.08.2012
 */
//namespace \Org\Heigl\Wordpress\EventsManager\OptInConfirm;
define('EM_OIC_BASEDIR', __DIR__);
require_once __DIR__ . '/src/Org/Heigl/Util/Autoloader.php';
\Org\Heigl\Util\Autoloader::registerAutoload();

\Org\Heigl\Wordpress\EventsManager\OptInConfirm\OptInConfirm::init();

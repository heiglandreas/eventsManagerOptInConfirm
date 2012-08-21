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
 * @version    1.0.3
 * @since      17.08.2012
 */

namespace Org\Heigl\Wordpress\EventsManager\OptInConfirm;

use Org\Heigl\Wordpress\Wordpress;
use Org\Heigl\Wordpress\EventManager\AdminUI;
use \DateTime;

/**
 * The main class for confirming a booking by opting in via an URL
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.3
 * @since      17.08.2012
 */
class OptInConfirm
{
    /**
     * Initialize the Package
     *
     * @return void
     */
    public static function init()
    {
        Wordpress::register_activation_hook('eventsManagerOptInConfirm/eventsManagerOptInConfirm.php', array(__CLASS__, 'checkDependencies'));
        Wordpress::register_activation_hook('eventsManagerOptInConfirm/eventsManagerOptInConfirm.php', array(__CLASS__, 'setDefaults'));
        Wordpress::register_activation_hook('eventsManagerOptInConfirm/eventsManagerOptInConfirm.php', array(__NAMESPACE__ . '\\Db', 'upgrade' ));
        Wordpress::add_action('admin_menu', array(__CLASS__, 'adminMenu'));
        Wordpress::add_filter('plugins_loaded', array(__CLASS__, 'pluginInit'));
        Wordpress::add_action( 'wp_router_generate_routes', array(__CLASS__, 'route'), 20 );
        Wordpress::add_action( 'shutdown', array(__CLASS__, 'freeOverdueBookings'), 20 );
        Wordpress::add_action( 'em_bookings_add_action', array(__CLASS__, 'createNewHash'), 20 );
        Wordpress::add_filter('em_booking_email_messages', array(__CLASS__, 'filter'));
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public static function pluginInit()
    {
        Wordpress::load_plugin_textdomain('em_oic', false, basename(EM_OIC_BASEDIR) . '/locale');
    }

    /**
     * Set the required default-values
     *
     * @return void
     */
    public static function setDefaults()
    {
        $now = new DateTime();
        Wordpress::add_option('em_oic_hash_not_found',            'Hash could not be found in the database');
        Wordpress::add_option('em_oic_hash_already_confirmed',    'Hash has already been confirmed');
        Wordpress::add_option('em_oic_hash_no_longer_valid',      'Hash is no longer valid');
        Wordpress::add_option('em_oic_booking_already_confirmed', 'Booking has already been confirmed');
        Wordpress::add_option('em_oic_booking_confirmed',         'Booking has been confirmed');
        Wordpress::add_option('em_oic_hash_lifetime',             'P2W');
        Wordpress::add_option('em_oic_last_gc_run',               $now->format('c'));
    }

    /**
     * Free bookings whose hash is overdue
     *
     * @return void
     */
    public static function freeOverdueBookings()
    {
        $now = new DateTime();
        $lastRun = Wordpress::get_option('em_oic_last_gc_run');
        if (! $lastRun) {
            $lastRun = '0000-00-00 00:00:00';
        }
        $lastRun = new DateTime($lastRun);
        Wordpress::update_option('em_oic_last_gc_run', $now->format('c'));
        $invalids = Db::getInvalidBetween($lastRun, $now);
        if (! $invalids) {
            return;
        }
        foreach ($invalids as $invalid) {
            $inv = new EM_Booking($invalid);
            $sql = $wpdb->prepare("DELETE FROM ". EM_BOOKINGS_TABLE . " WHERE booking_id=%d", $inv->booking_id);
            $result = $wpdb->query( $sql );
            if( $result !== false ){
                //delete the tickets too
                $inv->get_tickets_bookings()->delete();
                $inv->previous_status = $this->booking_status;
                $inv->booking_status = false;
            }
        }
    }

    /**
     * Create the admin menu
     *
     * @return void
     */
    public static function adminMenu()
    {
        Wordpress::add_options_page('OptIn-Confirm for Events Manager', 'EM-OptIn Confirm', 'manage_options','', array(__NAMESPACE__ . '\\AdminUIController', 'showAction'));
    }

    /**
     * Check dependencies for this plugin.
     *
     * @return void
     */
    public static function checkDependencies()
    {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        if (! Wordpress::is_plugin_active('events-manager/events-manager.php')) {
            Wordpress::deactivate_plugins( WP_PLUGINS_DIRECTORY . '/eventsManagerOptInConfirm/eventsManagerOptInConfirm.php');
            exit('This plugin requires the Events-Manager Plugin');
        }
        if (! Wordpress::is_plugin_active('wp-router/wp-router.php')) {
            Wordpress::deactivate_plugins( WP_PLUGINS_DIRECTORY . '/eventsManagerOptInConfirm/eventsManagerOptInConfirm.php');
            exit('This plugin requires the WP-Router Plugin');
        }
    }

    /**
     * Do the actual routing
     *
     * @param WP_Router $route The routing object
     *
     * @return void
     */
    public static function route(\WP_Router $router)
    {
        $class = new self();
       // $class = __NAMESPACE__ . '\\' . __CLASS__;
        $routeArguments = array(
                'path'    => '^optinconfirm/(.*?)$',
                'query_vars' => array(
                    'hash' => 1,
                ),
                'page_callback' => array($class, 'indexAction'),
                'page_arguments' => array(
                    'hash',
                ),
                'access_callback' => true,
                'title'           => __('Confirm booking', 'em_oic'),
                'template'        => array(
                    'page.php',
                    __DIR__ . '/page.php'
                ),
            );
        $router->add_route('optIn_Confirm', $routeArguments);
    }

    /**
     * This is the index-action
     *
     * @return string
     */
    public function indexAction($hash)
    {
        $hash = Hash::getInstance($hash);
        if ( ! $hash) {
            $this->addMessage(Wordpress::get_option('em_oic_hash_not_found'), 'warn');
            return;
        }
        if ($hash->isConfirmed()) {
            $this->addMessage(Wordpress::get_option('em_oic_hash_already_confirmed'), 'warn');
            return;
        }
        if (! $hash->isValid()) {
            $this->addMessage(Wordpress::get_option('em_oic_hash_no_longer_valid'), 'warn');
            return;

        }
        // Create a new EM_Booking-instance
        $booking = new \EM_Booking((int) $hash->getBooking());
        if ($booking->booking_status != 0) {
            $this->addMessage(Wordpress::get_option('em_oic_booking_already_confirmed'),'warn');
            return;
        }
        $booking->approve();
        if ($booking->booking_status != 0) {
            $hash->setConfirmationDate(new \DateTime());
            $hash->store();
            $this->addMessage(Wordpress::get_option('em_oic_booking_confirmed'), 'info');
        }
    }

    /**
     * Add a message
     *
     * @param string $message The Message to add
     * @param string $level   The level of the message
     *
     * @return OptInConfirm
     */
    public function addMessage($message, $level = 'info')
    {
        echo '<div class="message ' . $level . '">';
        echo $message;
        echo '</div>';
        return $this;
    }

    protected static $hash = null;

    /**
     * THis is the callback function to create a new Entry in the hash-table
     *
     * @param int EM_Bookings
     *
     * @return void
     */
    public static function createNewHash(\EM_Booking $booking)
    {
        echo $booking->booking_status;
        if ( $booking->booking_status != 0) {
            return;
        }
        self::$hash = Hash::factory($booking->booking_id);
        self::$hash->store();
    }

    /**
     * Filters content by replacing Placeholders
     *
     * @param $content
     *
     * @return string
     */
    public static function filter($content)
    {
        if (! self::$hash instanceof Hash) {
            return $content;
        }
        $URI = Wordpress::get_bloginfo('wpurl') . '/optinconfirm/' . self::$hash->getHash();
        foreach ($content as $key => $value) {
            if ( ! isset($value['body'])) {
                continue;
            }
            $content[$key]['body'] = str_replace('#_OPTIN_CONFIRM_URL', $URI, $value['body']);
        }
        return $content;
    }
}

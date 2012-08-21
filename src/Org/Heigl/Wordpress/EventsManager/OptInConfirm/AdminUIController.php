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
 * @version    1.0.0
 * @since      17.08.2012
 */

namespace Org\Heigl\Wordpress\EventsManager\OptInConfirm;

/**
 * This is the Admin-UI-Controller
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.0
 * @since      17.08.2012
 */
class AdminUIController
{
    /**
     * Initialize the Package
     *
     * @return void
     */
    public function showAction()
    {
        if($_POST['em_oic_save'])
        {
            update_option('em_oic_hash_not_found',            $_POST['em_oic_hash_not_found']);
            update_option('em_oic_hash_already_confirmed',    $_POST['em_oic_hash_already_confirmed']);
            update_option('em_oic_hash_no_longer_valid',      $_POST['em_oic_hash_no_longer_valid']);
            update_option('em_oic_booking_already_confirmed', $_POST['em_oic_booking_already_confirmed']);
            update_option('em_oic_hash_lifetime',             $_POST['em_oic_hash_lifetime']);
            update_option('em_oic_booking_confirmed',             $_POST['em_oic_booking_confirmed']);

            echo '<div class="updated"><p>' . __( 'Saved Options!', 'em_oic' ) . '</p></div>';
        }
        echo '<form method="post" action="">';
        echo '<h2>' . __('Token-Settings', 'em_oic') . '</h2>';
        echo '<table>';
        em_options_input_text ( __( 'Hash-Lifetime', 'em_oic' ), 'em_oic_hash_lifetime', sprintf(__('This is the lifetime of a hash. Add an intervall as described in %s', 'em_oic'), '<a href="http://php.net/DateInterval">http://php.net/DateInterval</a>'));
        echo '</table>';
        echo '<h2>' . __('Translations', 'em_oic') . '</h2>';
        echo '<table>';
        em_options_input_text ( __( 'Hash not found in database', 'em_oic' ), 'em_oic_hash_not_found', __('This message is displayed when the requested hash could not be found in the database', 'em_oic'));
        em_options_input_text ( __( 'Hash already confirmed', 'em_oic' ), 'em_oic_hash_already_confirmed', __('This message is displayed when the requested hash has already been confirmed', 'em_oic'));
        em_options_input_text ( __( 'Hash is not valid any more', 'em_oic' ), 'em_oic_hash_no_longer_valid', __('This message is displayed when the requested hash is older than the defined lifetime', 'em_oic'));
        em_options_input_text ( __( 'Booking has been confirmed already', 'em_oic' ), 'em_oic_booking_already_confirmed', __('This message is displayed when the requested booking has already been confirmed', 'em_oic'));
        em_options_input_text ( __( 'Booking has been confirmed', 'em_oic' ), 'em_oic_booking_confirmed', __('This message is displayed when the requested booking has been successfully confirmed', 'em_oic'));
        echo '</table>';
        echo '<p class="submit">';
        echo '<input type="submit" id="dbem_options_submit" name="em_oic_save" value="' . __('Save Changes', 'em_oic') . '" />';
		echo '<input type="hidden" name="_wpnonce" value="' . wp_create_nonce('em_oic') . '" />';
	    echo '</p>';
        echo '</form>';
        echo '<div><h2>' . __('Placeholder', 'em_oic') . '</h2>';
        echo '<dl>';
        echo '<dt>#_OPTIN_CONFIRM_URL</dt><dd>' . __('This will be replaced by the URL to the OptIn-Link', 'em_oic') . '</dd>';
        echo '</dl>';
        echo '</div>';
    }

    /**
     * Create the admin menu
     *
     * @return void
     */
    public static function adminMenu()
    {


        Wordpress::add_options_page('OptIn-Confirm for Events Manager', 'EM-OptIn Confirm', 'manage_options','', array(new AdminUIController, 'showAdminPanel'));
    }
}
OptIn-Confirmation for Bookings with Events Manager
===================================================

This plugin allows the following scenario:

* A user requests a booking for an event
* An email will be sent to the address the user gave containing a confirmation
  link
* On opening that link the booking will be confirmed for that user.

Requirements
------------ 

* PHP 5.3
* Events Manager Plugin
* wp-router Plugin

Installation
------------

# Download the plugin and install it into a folder ''eventsManagerOptInConfirm''
  if that folder not already exists.
# Install and activate the Events Manager and the wp-router plugins
# Activate the EventsManagerOptInConfirm-plugin
# Add the following line to the file *events-manager/classes/em-bookings.php*
  as line 69: 
  
::
     do_action('em_bookings_add_action', $EM_Booking); 
     
# Add the placeholder *#_OPTIN_CONFIRM_URL* for the email-link to Events 
  Managers email-template for pending approval emails.
  
Thats it.


Legal Stuff
-----

This plugin is published under the MIT-License. For more information see the 
file LICENSE or http://opensource.org/licenses/mit-license.php



<?php
/**
* @project uHotelBooking
* @copyright (c) 2018 ApPHP
* @author ApPHP <info@apphp.com>
* @site http://www.hotel-booking-script.com
* @license http://hotel-booking-script.com/license.php
*/

require_once('include/base.inc.php');
require_once('include/connection.php');

if(!$objLogin->IsLoggedIn()){

    ////////////////////////////////////////////////////////////////////////////
    // Cron - check if there is some work for cron 
    ////////////////////////////////////////////////////////////////////////////    

	Cron::Run();		

}    

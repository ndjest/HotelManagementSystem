<?php
/**
* @project uHotelBooking
* @copyright (c) 2018 ApPHP
* @author ApPHP <info@apphp.com>
* @site http://www.hotel-booking-script.com
* @license http://hotel-booking-script.com/license.php
*/

// *** Make sure the file isn't accessed directly
defined('APPHP_EXEC') or die('Restricted Access');
//--------------------------------------------------------------------------

if($objLogin->IsLoggedInAsAdmin()){
	
	$objAdmin           = new Admins($objLogin->GetLoggedID());
	$submit_type 	    = isset($_POST['submit_type']) ? prepare_input($_POST['submit_type']) : '';
	$preferred_language = isset($_POST['preferred_language']) ? prepare_input($_POST['preferred_language']) : '';
	$admin_email 	 	= isset($_POST['admin_email']) ? prepare_input($_POST['admin_email']) : '';
	$password_one 	 	= isset($_POST['password_one']) ? prepare_input($_POST['password_one']) : '';
	$password_two 	 	= isset($_POST['password_two']) ? prepare_input($_POST['password_two']) : '';
	$first_name 	 	= isset($_POST['first_name']) ? prepare_input($_POST['first_name']) : '';
	$last_name 	 	    = isset($_POST['last_name']) ? prepare_input($_POST['last_name']) : '';
	$consumer_domain    = isset($_POST['oa_consumer_domain']) ? $_POST['oa_consumer_domain'] : '';
	$msg                = '';
	$error_field        = '';
	
	// change password
	if($submit_type == '1'){
		$msg = $objAdmin->ChangeLang($preferred_language);
	}else if($submit_type == '2'){
		$msg = $objAdmin->SavePersonalInfo($admin_email, $first_name, $last_name, $error_field);
	}else if($submit_type == '3'){
		$msg = $objAdmin->ChangePassword($password_one, $password_two, $error_field);
	}else if($submit_type == '4'){
		$msg = $objAdmin->SaveApiInfo($consumer_domain, $error_field);
	}else if($submit_type == '5'){
		$msg = $objAdmin->SaveApiInfo($consumer_domain, $error_field, true);
	}else if($submit_type == '9'){
		$msg = $objAdmin->DeletePhoto();
	}
}

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
	
if($objLogin->IsLoggedInAs('owner','mainadmin','hotelowner','regionalmanager')){
	
	$action = MicroGrid::GetParameter('action');
	$rid    = MicroGrid::GetParameter('rid');
	$mode   = 'view';
	$msg 	= '';
	
	$objHotels = new Hotels();

	if($objLogin->IsLoggedInAs('hotelowner')){
		$arr_hotels_list = $objLogin->AssignedToHotels();
		if(!empty($rid) && !in_array($rid, $arr_hotels_list)){
			$msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);
			$action = '';
		}
	}

	if($action=='add'){		
		$mode = 'add';
	}else if($action=='create'){
		if($objHotels->AddRecord()){
			if($objLogin->IsLoggedInAs('hotelowner') && ALLOW_OWNERS_ADD_NEW_HOTELS){
				$objSession->SetMessage('notice', draw_success_message(_ADDING_OPERATION_COMPLETED, false));
				redirect_to('index.php?admin=hotels_info');
			}else{
				$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			}			
			$mode = 'view';
		}else{
			$msg = draw_important_message($objHotels->error, false);
			$mode = 'add';
		}
	}else if($action=='edit'){
		$mode = 'edit';
	}else if($action=='update'){
		if($objHotels->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objHotels->error, false);
			$mode = 'edit';
		}		
	}else if($action=='delete'){
		if($objHotels->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objHotels->error, false);
		}
		$mode = 'view';
	}else if($action=='details'){		
		$mode = 'details';		
	}else if($action=='cancel_add'){		
		$mode = 'view';		
	}else if($action=='cancel_edit'){				
		$mode = 'view';
	}
	
	// Start main content
    if(FLATS_INSTEAD_OF_HOTELS){
    	draw_title_bar(prepare_breadcrumbs(array(_FLATS_MANAGEMENT=>'',_FLATS=>'',_FLATS_INFO=>'',ucfirst($action)=>'')));	
    }else{
    	draw_title_bar(prepare_breadcrumbs(array(_HOTELS_MANAGEMENT=>'',_HOTELS_AND_ROOMS=>'',_HOTELS_INFO=>'',ucfirst($action)=>'')));	
    }
	
	if($objSession->IsMessage('notice')) $msg = $objSession->GetMessage('notice');
	echo $msg;

	draw_content_start();
	
	// Check if hotel owner is not assigned to any hotel
	$allow_viewing = true;
	if($objLogin->IsLoggedInAs('hotelowner')){
		$hotels_list = implode(',', $arr_hotels_list);
		if(empty($hotels_list)){
			$allow_viewing = false;
			echo draw_important_message(_OWNER_NOT_ASSIGNED, false);
		}
	}
	
	if($allow_viewing){
		if($mode == 'view'){
			if($objLogin->IsLoggedInAs('owner','mainadmin')) $objHotels->DrawOperationLinks(
				prepare_permanent_link('index.php?admin=hotels_locations', '[ '._LOCATIONS.' ]') . ' &nbsp; ' .
				prepare_permanent_link('index.php?admin=hotels_property_types', '[ '._PROPERTY_TYPES.' ]')				
			);
			$objHotels->SetAlerts(array('delete'=>FLATS_INSTEAD_OF_HOTELS ? _FLAT_DELETE_ALERT : _HOTEL_DELETE_ALERT));
			$objHotels->DrawViewMode();	
		}else if($mode == 'add'){		
			$objHotels->DrawAddMode();		
		}else if($mode == 'edit'){		
			$objHotels->DrawEditMode($rid);		
		}else if($mode == 'details'){		
			$objHotels->DrawDetailsMode($rid);		
		}
	}
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


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

if(($objLogin->IsLoggedInAs('owner','mainadmin') || ($objLogin->IsLoggedInAs('hotelowner') && $objLogin->HasPrivileges('view_hotel_payments'))) && Modules::IsModuleInstalled('booking')){

	$action 	= MicroGrid::GetParameter('action');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   	= 'view';
	$msg 		= '';
	
	$objHotelPaymentGateways = new HotelPaymentGateways();
    
	// Check hotel owner has permissions to edit this hotel's info
	if($objLogin->IsLoggedInAs('hotelowner')){
		$hotel_id = null;
		if(in_array($action, array('update'))){
			$hotel_id = MicroGrid::GetParameter('hotel_id', false);	
			if(empty($hotel_id)){
				$hotel_id = '-99';
			}
		}else if(in_array($action, array('edit', 'details', 'delete'))){
			$info = $objHotelPaymentGateways->GetInfoByID($rid);
			$hotel_id = isset($info['hotel_id']) ? $info['hotel_id'] : '';
			if(empty($hotel_id)){
				$hotel_id = '-99';
			}
		}
		
		if(!empty($hotel_id) && !in_array($hotel_id, $objLogin->AssignedToHotels())){
            $msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);
            $action = '';
			$mode = 'view';
		}
	}

	if($action=='add'){		
		$mode = 'view';
	}else if($action=='create'){
        $mode = 'view';
	}else if($action=='edit'){
		$mode = 'edit';
	}else if($action=='update'){
        $info = $objHotelPaymentGateways->GetInfoByID($rid);
        $hotel_id = isset($info['hotel_id']) ? $info['hotel_id'] : '';                
        if(!$objLogin->IsLoggedInAs('hotelowner') || in_array($hotel_id, $objLogin->AssignedToHotels())){
            if($objHotelPaymentGateways->UpdateRecord($rid)){
                $msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
                $mode = 'view';
            }else{
                $msg = draw_important_message($objHotelPaymentGateways->error, false);
                $mode = 'edit';
            }
        }else{
            $msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);
            $mode = 'view';
        }        
	}else if($action=='delete'){
		$mode = 'view';
	}else if($action=='details'){		
		$mode = 'details';		
	}else if($action=='cancel_add'){		
		$mode = 'view';		
	}else if($action=='cancel_edit'){				
		$mode = 'view';
    }else{
        $action = '';
	}
	
	// Start main content
    if(FLATS_INSTEAD_OF_HOTELS){
        draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>'',_SETTINGS=>'',_FLAT_PAYMENT_GATEWAYS=>'',ucfirst($action)=>'')));
    }else{
        draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>'',_SETTINGS=>'',_HOTEL_PAYMENT_GATEWAYS=>'',ucfirst($action)=>'')));
    }
    	
	//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
	echo $msg;

	draw_content_start();	

	// Check if hotel owner is not assigned to any hotel
	$allow_viewing = true;
	if($objLogin->IsLoggedInAs('hotelowner')){
		$hotels_list = implode(',', $objLogin->AssignedToHotels());
		if(empty($hotels_list)){
			$allow_viewing = false;
			echo draw_important_message(_OWNER_NOT_ASSIGNED, false);
		}
	}
	
	if($allow_viewing){
		if($mode == 'view'){		
			$objHotelPaymentGateways->DrawViewMode();	
		}else if($mode == 'edit'){		
			$objHotelPaymentGateways->DrawEditMode($rid);		
		}
	}
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


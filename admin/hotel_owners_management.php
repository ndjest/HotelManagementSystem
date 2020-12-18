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

if($objLogin->IsLoggedInAs('owner','mainadmin')){	

	$action 	= MicroGrid::GetParameter('action');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   	= 'view';
	$msg 		= '';
	
	$objAdmins = new AdminsAccounts($objLogin->GetLoggedType(), 'hotel_admins');
	
	if($action=='add'){		
		$mode = 'add';
	}else if($action=='create'){
		if($objAdmins->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objAdmins->error, false);
			$mode = 'add';
		}
	}else if($action=='edit'){
		$mode = 'edit';
	}else if($action=='update'){
		if($objAdmins->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objAdmins->error, false);
			$mode = 'edit';
		}		
	}else if($action=='delete'){
		if($objAdmins->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objAdmins->error, false);
		}
		$mode = 'view';
	}else if($action=='details'){		
		$mode = 'details';		
	}else if($action=='recreate_api'){		
		$msg = $objAdmins->RecreateApi($rid);
		$mode = 'edit';		
	}else if($action=='cancel_add'){		
		$mode = 'view';		
	}else if($action=='cancel_edit'){				
		$mode = 'view';
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>'',_ADMINS_MANAGEMENT=>'',FLATS_INSTEAD_OF_HOTELS ? _FLAT_OWNERS : _HOTEL_OWNERS=>'',ucfirst($action)=>'')));
    	
	//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == 'view'){		
		$objAdmins->DrawViewMode();	
	}else if($mode == 'add'){		
		$objAdmins->DrawAddMode();		
	}else if($mode == 'edit'){		
		$objAdmins->DrawEditMode($rid);		
	}else if($mode == 'details'){		
		$objAdmins->DrawDetailsMode($rid);		
	}
	draw_content_end();	

}else{	
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


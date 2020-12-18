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
	
if($objLogin->IsLoggedInAs('owner','mainadmin','hotelowner') && Modules::IsModuleInstalled('customers')){
	
	$action 	= MicroGrid::GetParameter('action');
	$rid    	= MicroGrid::GetParameter('rid');
	$email		= MicroGrid::GetParameter('email', false);
	$mode   	= 'view';
	$msg 		= '';
	
	$objCustomers = new Customers();

	// Check hotel owner has permissions to edit this hotel's info
	if($objLogin->IsLoggedInAs('hotelowner')){
		$hotel_id = null;
		///'create', 
		if(in_array($action, array('update', 'edit', 'details', 'delete'))){
			$info = $objCustomers->GetInfoByID($rid);
			$account_id = isset($info['created_by_admin_id']) ? $info['created_by_admin_id'] : 0;
			if($account_id != $objLogin->GetLoggedID()){
				$msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);
				$action = '';
			}
		}
		
//		if(!empty($hotel_id) && !in_array($hotel_id, $objLogin->AssignedToHotels())){
//            $msg = draw_important_message(_WRONG_PARAMETER_PASSED, false);
//            $action = '';
//			$mode = 'view';
//		}
	}

	if($action=='add'){		
		$mode = 'add';
	}else if($action=='create'){
		if($objCustomers->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objCustomers->error, false);
			$mode = 'add';
		}
	}else if($action=='edit'){
		$mode = 'edit';
	}else if($action=='update'){
		if($objCustomers->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objCustomers->error, false);
			$mode = 'edit';
		}		
	}else if($action=='delete'){
		if($objCustomers->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objCustomers->error, false);
		}
		$mode = 'view';
	}else if($action=='details'){		
		$mode = 'details';		
	}else if($action=='cancel_add'){		
		$mode = 'view';		
	}else if($action=='cancel_edit'){				
		$mode = 'view';
	}else if($action=='reactivate'){
		if(Customers::Reactivate($email)){	
			$msg = draw_success_message(_EMAIL_SUCCESSFULLY_SENT, false);
		}else{
			$msg = draw_important_message(Customers::GetStaticError(), false);
		}		
		$mode = 'view';
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>'',_CUSTOMERS_MANAGEMENT=>'',_CUSTOMERS=>'',ucfirst($action)=>'')));
	
	//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == 'view'){
		if(!$objLogin->IsLoggedInAs('hotelowner')){
			$objCustomers->DrawOperationLinks(prepare_permanent_link('index.php?admin=mod_customers_groups', '[ '._CUSTOMER_GROUPS.' ]'));
		}
		$objCustomers->DrawViewMode();	
	}else if($mode == 'add'){		
		$objCustomers->DrawAddMode();		
	}else if($mode == 'edit'){		
		$objCustomers->DrawEditMode($rid);		
	}else if($mode == 'details'){		
		$objCustomers->DrawDetailsMode($rid);		
	}
	
	draw_content_end();

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


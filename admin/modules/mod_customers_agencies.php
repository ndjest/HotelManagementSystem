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
	
if($objLogin->IsLoggedInAs('owner','mainadmin', 'regionalmanager') && Modules::IsModuleInstalled('customers') && ModulesSettings::Get('customers', 'allow_agencies') == 'yes'){
	
	$action 	= MicroGrid::GetParameter('action');
	$rid    	= MicroGrid::GetParameter('rid');
	$email		= MicroGrid::GetParameter('email', false);
	$mode   	= 'view';
	$msg 		= '';
	
	$objCustomers = new Customers('agencies');

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
	}else if($action=='set_status'){
		$mode = 'view';
		$msg = Customers::ChangeStatus($rid, 1);
	}else if($action=='reset_status'){
		$mode = 'view';
		$msg = Customers::ChangeStatus($rid, 0);
	}else if($action=='reactivate'){
		if(Customers::Reactivate($email)){	
			$msg = draw_success_message(_EMAIL_SUCCESSFULLY_SENT, false);
		}else{
			$msg = draw_important_message(Customers::GetStaticError(), false);
		}		
		$mode = 'view';
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>'',_CUSTOMERS_MANAGEMENT=>'',_TRAVEL_AGENCIES=>'',ucfirst($action)=>'')));
	
	//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
	echo $msg;

	draw_content_start();	
	if($mode == 'view'){
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

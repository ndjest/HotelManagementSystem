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

if($objLogin->IsLoggedInAs('owner','mainadmin') && Modules::IsModuleInstalled('booking')){

	$action 	= MicroGrid::GetParameter('action');
	$rid    	= MicroGrid::GetParameter('rid');
	$mode   	= 'view';
	$msg 		= '';
	
	$objCurrencies = new Currencies();
	
	if($action=='add'){		
		$mode = 'add';
	}else if($action=='create'){
		if($objCurrencies->AddRecord()){
			$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objCurrencies->error, false);
			$mode = 'add';
		}
	}else if($action=='edit'){
		$mode = 'edit';
	}else if($action=='update'){
		if($objCurrencies->UpdateRecord($rid)){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
			$mode = 'view';
		}else{
			$msg = draw_important_message($objCurrencies->error, false);
			$mode = 'edit';
		}		
	}else if($action=='delete'){
		if($objCurrencies->DeleteRecord($rid)){
			$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
		}else{
			$msg = draw_important_message($objCurrencies->error, false);
		}
		$mode = 'view';
	}else if($action=='details'){		
		$mode = 'details';		
	}else if($action=='cancel_add'){		
		$mode = 'view';		
	}else if($action=='cancel_edit'){				
		$mode = 'view';
	}else if($action=='update_rates'){
		if($objCurrencies->UpdateCurrencyRates()){
			$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED.$objCurrencies->alert, false);
		}else{
			$msg = draw_important_message($objCurrencies->error, false);
		}
		
		$objSession->SetMessage('notice', $msg);
		redirect_to('index.php?admin=mod_booking_currencies');
	}
	
	// Start main content
	draw_title_bar(prepare_breadcrumbs(array(_BOOKINGS=>'',_SETTINGS=>'',_CURRENCIES_MANAGEMENT=>'',ucfirst($action)=>'')));
    	
	if($objSession->IsMessage('notice')) $msg = $objSession->GetMessage('notice');
	if($mode == 'view' && $msg == ''){
		$msg = draw_message(_CURRENCIES_DEFAULT_ALERT, false);		
	}
	echo $msg;

	draw_content_start();	
	if($mode == 'view'){		
		$objCurrencies->DrawOperationLinks(prepare_permanent_link('index.php?admin=mod_booking_currencies&mg_action=update_rates', '[ '._UPDATE_CURRENCY_RATE.' ]'));
		$objCurrencies->DrawViewMode();	
	}else if($mode == 'add'){		
		$objCurrencies->DrawAddMode();		
	}else if($mode == 'edit'){		
		$objCurrencies->DrawEditMode($rid);		
	}else if($mode == 'details'){		
		$objCurrencies->DrawDetailsMode($rid);		
	}
	draw_content_end();	

}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


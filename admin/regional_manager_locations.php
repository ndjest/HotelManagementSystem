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

	$action = MicroGrid::GetParameter('action');
	$rid = MicroGrid::GetParameter('rid');
	$account_id = MicroGrid::GetParameter('aid', false);
	$mode   = 'view';
	$msg    = '';

	if(!empty($account_id)){
		$objAccount = new Accounts($account_id);
		$full_name = $objAccount->GetParameter('first_name').' '.$objAccount->GetParameter('last_name');

		$objAccountLocations = new AccountLocations($account_id);
		
		if($action=='add'){		
			$mode = 'add';
		}else if($action=='create'){
			if($objAccountLocations->AddRecord()){		
				$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
				$mode = 'view';
			}else{
				$msg = draw_important_message($objAccountLocations->error, false);
				$mode = 'add';
			}
		}else if($action=='edit'){
			$mode = 'edit';
		}else if($action=='update'){
			if($objAccountLocations->UpdateRecord($rid)){
				$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
				$mode = 'view';
			}else{
				$msg = draw_important_message($objAccountLocations->error, false);
				$mode = 'edit';
			}		
		}else if($action=='delete'){
			if($objAccountLocations->DeleteRecord($rid)){
				$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
			}else{
				$msg = draw_important_message($objAccountLocations->error, false);
			}
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
		draw_title_bar(
			prepare_breadcrumbs(array(_ACTIONS=>'',_ADMINS_MANAGEMENT=>'', $full_name=>'', _LOCATIONS=>'',ucfirst($action)=>'')),
			prepare_permanent_link('index.php?admin=regional_managers', _BUTTON_BACK)
		);
	
		//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
		echo $msg;
	
		draw_content_start();
		if($mode == 'view'){		
			$objAccountLocations->DrawViewMode();	
		}else if($mode == 'add'){		
			$objAccountLocations->DrawAddMode();		
		}else if($mode == 'edit'){		
			$objAccountLocations->DrawEditMode($rid);		
		}else if($mode == 'details'){		
			$objAccountLocations->DrawDetailsMode($rid);
		}
		draw_content_end();		
	}else{
		draw_title_bar(
			prepare_breadcrumbs(array(_ACCOUNTS=>'',_ADMINS_MANAGEMENT=>'',_LOCATIONS=>'')),
			prepare_permanent_link('index.php?admin=regional_managers', _BUTTON_BACK)
		);
		draw_important_message(FLATS_INSTEAD_OF_HOTELS ? _OWNER_NOT_ASSIGNED_TO_FLAT : _OWNER_NOT_ASSIGNED_TO_HOTEL);
	}
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}

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
    $country_id = MicroGrid::GetParameter('cid', false);
	$mode   = 'view';
	$msg 	= '';
	
	$country_info = Countries::GetCountryInfo($country_id);
	
	if($country_id > 0 && count($country_info) > 0){

        $objStates = new States($country_id);
    
        if($action=='add'){		
            $mode = 'add';
        }else if($action=='create'){
            if($objStates->AddRecord()){
                $msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
                $mode = 'view';
            }else{
                $msg = draw_important_message($objStates->error, false);
                $mode = 'add';
            }
        }else if($action=='edit'){
            $mode = 'edit';
        }else if($action=='update'){
            if($objStates->UpdateRecord($rid)){
                $msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
                $mode = 'view';
            }else{
                $msg = draw_important_message($objStates->error, false);
                $mode = 'edit';
            }		
        }else if($action=='delete'){
            if($objStates->DeleteRecord($rid)){
                $msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
            }else{
                $msg = draw_important_message($objStates->error, false);
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
		draw_title_bar(
			prepare_breadcrumbs(array(_GENERAL=>'',_COUNTRIES=>'',$country_info['name']=>'',_STATES=>'',ucfirst($action)=>'')),
			prepare_permanent_link('index.php?admin=countries_management', _BUTTON_BACK)
		);
        
        //if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
        echo $msg;
    
        draw_content_start();	
        if($mode == 'view'){		
            $objStates->DrawViewMode();	
        }else if($mode == 'add'){		
            $objStates->DrawAddMode();		
        }else if($mode == 'edit'){		
            $objStates->DrawEditMode($rid);		
        }else if($mode == 'details'){		
            $objStates->DrawDetailsMode($rid);		
        }
        draw_content_end();
	}else{
		draw_title_bar(
			prepare_breadcrumbs(array(_GENERAL=>'',_COUNTRIES=>'',_STATES=>'')),
			prepare_permanent_link('index.php?admin=countries_management', _BUTTON_BACK)
		);
		draw_important_message(_WRONG_PARAMETER_PASSED);
	}
}else{
	draw_title_bar(_ADMIN);
	draw_important_message(_NOT_AUTHORIZED);
}


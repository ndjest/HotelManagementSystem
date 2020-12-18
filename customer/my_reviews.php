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

if($objLogin->IsLoggedInAsCustomer()){

	if(Modules::IsModuleInstalled('reviews')){
		
		$action 		= MicroGrid::GetParameter('action');
		$rid    		= MicroGrid::GetParameter('rid');
		$mode   		= 'view';
		$msg 			= '';
		
		$objReviews = new Reviews($objLogin->GetLoggedID());
		
		if($action != 'add' || !$objReviews->error){
			if($action=='add'){		
				$mode = 'add';
			}else if($action=='create'){
				if($objReviews->AddRecord()){
					$msg = draw_success_message(_ADDING_OPERATION_COMPLETED, false);
					$mode = 'view';
				}else{
					$msg = draw_important_message($objReviews->error, false);
					$mode = 'add';
				}
			}else if($action=='edit'){
				$mode = 'edit';
			}else if($action=='update'){
				if($objReviews->UpdateRecord($rid)){
					$msg = draw_success_message(_UPDATING_OPERATION_COMPLETED, false);
					$mode = 'view';
				}else{
					$msg = draw_important_message($objReviews->error, false);
					$mode = 'edit';
				}		
			}else if($action=='delete'){
				if($objReviews->DeleteRecord($rid)){
					$msg = draw_success_message(_DELETING_OPERATION_COMPLETED, false);
				}else{
					$msg = draw_important_message($objReviews->error, false);
				}
				$mode = 'view';
			}else if($action=='details'){		
				$mode = 'details';		
			}else if($action=='cancel_add'){		
				$mode = 'view';		
			}else if($action=='cancel_edit'){				
				$mode = 'view';
			}
		}else{
			$msg = draw_important_message($objReviews->error, false);
		}
			
		// Start main content
		draw_title_bar(
			prepare_breadcrumbs(array(_MY_ACCOUNT=>'',_REVIEWS=>'',ucfirst($action)=>''))
		);
			
		//if($objSession->IsMessage('notice')) echo $objSession->GetMessage('notice');
		echo $msg;
		
		//draw_content_start();
		echo '<div id="divMyReviews">';
		if($mode == 'view'){			
			$objReviews->DrawViewMode();	
		}else if($mode == 'add'){		
			$objReviews->DrawAddMode();		
		}else if($mode == 'edit'){		
			$objReviews->DrawEditMode($rid);		
		}else if($mode == 'details'){		
			$objReviews->DrawDetailsMode($rid);		
		}	
		//draw_content_end();		
		echo '</div><br><br>';
	}else{
		draw_title_bar(prepare_breadcrumbs(array(_CUSTOMERS=>'')));
		draw_important_message(_NOT_AUTHORIZED);
	}
}else if($objLogin->IsLoggedIn()){
	draw_title_bar(prepare_breadcrumbs(array(_CUSTOMERS=>'')));
	draw_important_message(_NOT_AUTHORIZED);
}else{
	draw_title_bar(prepare_breadcrumbs(array(_CUSTOMERS=>'')));
	draw_important_message(_MUST_BE_LOGGED);
}

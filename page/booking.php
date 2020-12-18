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

if(Modules::IsModuleInstalled('booking')){
	if(ModulesSettings::Get('booking', 'is_active') == 'global' ||
	   ModulesSettings::Get('booking', 'is_active') == 'front-end' ||
	  (ModulesSettings::Get('booking', 'is_active') == 'back-end' && $objLogin->IsLoggedInAsAdmin())	
	){		
		$act         = !empty($_POST['act'])      ? $_POST['act'] : (isset($_GET['act']) ? $_GET['act'] : '');
		$room_id     = isset($_POST['room_id'])   ? (int)$_POST['room_id'] : '0';
        $rid         = isset($_GET['rid'])        ? (int)$_GET['rid'] : '';
		$from_date   = isset($_POST['from_date']) ? prepare_input($_POST['from_date']) : '';
		$to_date     = isset($_POST['to_date'])   ? prepare_input($_POST['to_date']) : '';
		$nights_post = isset($_POST['nights'])    ? (int)$_POST['nights'] : '';
		$adults      = isset($_POST['adults']) && (int)$_POST['adults'] > 0 ? (int)$_POST['adults'] : '1';
		$children    = isset($_POST['children'])  ? (int)$_POST['children'] : '0';
        $operation_allowed = true;
        $is_update   = $act == 'update' ? true : false;

        draw_content_start();
        draw_reservation_bar('selected_rooms');		

		// [#001 - 08.12.2013] added to verify post data
		// -----------------------------------------------------------------
		$objReservation = new Reservation();

        // Update from_date and to_date
        if($is_update){
            $american_format = $objSettings->GetParameter('date_format') == 'mm/dd/yyyy' ? true : false;

            // Splitting a date in the components
            $checkinParts = explode('/', $from_date);
            $checkin_month = isset($checkinParts[$american_format ? 0 : 1]) ? $checkinParts[$american_format ? 0 : 1] : '';
            $checkin_day = isset($checkinParts[$american_format ? 1 : 0]) ? $checkinParts[$american_format ? 1 : 0] : '';
            $checkin_year = isset($checkinParts[2]) ? $checkinParts[2] : '';
            $checkoutParts = explode('/', $to_date);
            $checkout_month = isset($checkoutParts[$american_format ? 0 : 1]) ? $checkoutParts[$american_format ? 0 : 1] : '';
            $checkout_day = isset($checkoutParts[$american_format ? 1 : 0]) ? $checkoutParts[$american_format ? 1 : 0] : '';
            $checkout_year = isset($checkoutParts[2]) ? $checkoutParts[2] : '';
					
            $checkin_year_month 	= $checkin_year.'-'.(int)$checkin_month;
            $checkout_year_month 	= $checkout_year.'-'.(int)$checkout_month;

            $checkin_date           = $checkin_year.'-'.$checkin_month.'-'.$checkin_day;
            $checkout_date          = $checkout_year.'-'.$checkout_month.'-'.$checkout_day;

            // max date - 2 years
            $max_date_unix = mktime(0, 0, 0, date('m'), date('d'), date('Y') + 2) + (24 * 3600);

            // -----------------------------------------------------------------
            // Get info for reservation
			$info_reservation = $objReservation->GetInfoByRoomID($room_id);

            if(!empty($room_id) && !empty($info_reservation)){
                $nights     = nights_diff($checkin_date, $checkout_date);
                $hotel_id   = $info_reservation['hotel_id'];

                $search_availability_period = ModulesSettings::Get('rooms', 'search_availability_period');
                $search_availability_period_in_days = ($search_availability_period * 365) + 1;

                // Check if there is a page
                if(!checkdate($checkout_month, $checkout_day, $checkout_year)){
                    draw_important_message(_WRONG_CHECKOUT_DATE_ALERT);
                }else if($nights > $search_availability_period_in_days){
                    draw_important_message(str_replace('_DAYS_', $search_availability_period_in_days, _MAXIMUM_PERIOD_ALERT));
                }else if(empty($checkin_date) || empty($checkout_date)){
                    draw_important_message(_WRONG_PARAMETER_PASSED);
                }else if($max_date_unix < mktime(0, 0, 0, $checkout_month, $checkout_day, $checkout_year)){
                    draw_important_message(_WRONG_PARAMETER_PASSED);
                }else if((ModulesSettings::Get('booking', 'allow_booking_in_past') != 'yes' || !$objLogin->IsLoggedInAsAdmin()) && $checkin_year.$checkin_month.$checkin_day < date('Ymd')){
                    draw_important_message(_PAST_TIME_ALERT);		
                }else if($nights < 1){
                    draw_important_message(_BOOK_ONE_NIGHT_ALERT);
                }else if(Modules::IsModuleInstalled('booking')){

                    $min_nights = ModulesSettings::Get('booking', 'minimum_nights');
                    $max_nights = ModulesSettings::Get('booking', 'maximum_nights');
                    $maximal_rooms = $info_reservation['rooms'];
                    $adults = $info_reservation['adults'];
                    $children = $info_reservation['children'];
                    $extra_beds = $info_reservation['extra_beds'];
					$meal_plan_id = $info_reservation['meal_plan_id'];
                    $packages = Packages::GetAllActiveByDate($checkin_date, $checkout_date);
					$meal_plan_info = MealPlans::GetPlanInfo($meal_plan_id);
					// specify meal multiplier
					$meal_multiplier = $nights * $maximal_rooms * ($adults + $children);
					//$meal_multiplier = $nights * $maximal_rooms * $adults;

                    // check if package min/max stays is available for this hotel
                    $hotel_data = isset($packages[$hotel_id]) ? $packages[$hotel_id] : null;
                    if(!empty($hotel_data)){
                        $min_nights = $hotel_data['minimum_nights'];
                        $max_nights = $hotel_data['maximum_nights'];
                    }

                    if($nights < $min_nights){
                        echo draw_important_message(str_replace('_DAYS_', $min_nights, _MINIMUM_PERIOD_ALERT));
                    }else if($nights > $max_nights){
                        echo draw_important_message(str_replace('_DAYS_', $max_nights, _MAXIMUM_PERIOD_ALERT));
                    }else{
                        $max_booked_rooms = '0';

                        $sql = 'SELECT room_count FROM '.TABLE_ROOMS.' WHERE id='.(int)$room_id.' LIMIT 0, 1';
                        $room_count = database_query($sql, DATA_ONLY, FIRST_ROW_ONLY);
                        $room_count = !empty($room_count['room_count']) ? $room_count['room_count'] : 0;
                        
                        $sql = 'SELECT
                                    MAX('.TABLE_BOOKINGS_ROOMS.'.rooms) as max_booked_rooms
                                FROM '.TABLE_BOOKINGS.'
                                    INNER JOIN '.TABLE_BOOKINGS_ROOMS.' ON '.TABLE_BOOKINGS.'.booking_number = '.TABLE_BOOKINGS_ROOMS.'.booking_number
                                WHERE
                                    ('.TABLE_BOOKINGS.'.status = 2 OR '.TABLE_BOOKINGS.'.status = 3) AND
                                    '.TABLE_BOOKINGS_ROOMS.'.room_id = '.(int)$room_id.' AND
                                    (
                                        (\''.$checkin_date.'\' <= checkin AND \''.$checkout_date.'\' > checkin) 
                                        OR
                                        (\''.$checkin_date.'\' < checkout AND \''.$checkout_date.'\' >= checkout)
                                        OR
                                        (\''.$checkin_date.'\' >= checkin  AND \''.$checkout_date.'\' < checkout)
                                    )';
                        $rooms_booked = database_query($sql, DATA_AND_ROWS, FIRST_ROW_ONLY);
                        if($rooms_booked[1] > 0){
                            $max_booked_rooms = (int)$rooms_booked[0]['max_booked_rooms'];
                        }
                        
                        // Check on the number of available rooms
                        $available_rooms = (int)($room_count - ($maximal_rooms + $max_booked_rooms));
                        if($available_rooms >= 0){
                            $available_rooms_updated = Rooms::CheckAvailabilityForPeriod($room_id, $checkin_date, $checkout_date, $available_rooms + $room_count);
                            if($available_rooms_updated){
                                $params = array(
                                    'from_date'    => $checkin_date,
                                    'to_date'      => $checkout_date,
                                    'from_year'    => $checkin_year,
                                    'from_month'   => $checkin_month,
                                    'from_day'     => $checkin_day,
                                    'to_year'      => $checkout_year,
                                    'to_month'     => $checkout_month,
                                    'to_day'       => $checkout_day,
                                    'max_adults'   => $adults,
                                    'max_children' => $children,
                                );
                                $price = Rooms::GetRoomPrice($room_id, $hotel_id, $params) * $maximal_rooms;
                                $extra_bed_charge = Rooms::GetRoomExtraBedsPrice($room_id, $params) * $extra_beds;
                                $extra_beds_charge = number_format($extra_bed_charge * $nights * $maximal_rooms, 2, '.', '');

								$objReservation->EditToReservation($room_id, array(
									'from_date'=>$checkin_date,
									'to_date'=>$checkout_date,
									'nights'=>$nights,
									'price'=>$price,
									'meal_plan_id' => (int)$meal_plan_id,
									'meal_plan_name' => isset($meal_plan_info['name']) ? $meal_plan_info['name'] : '',
									'meal_plan_price' => isset($meal_plan_info['price']) ? number_format($meal_plan_info['price'] * $meal_multiplier, 2, '.', '') : 0,
									'extra_beds_charge'=>$extra_beds_charge
								));

                                // refresh discount info
                                $objReservation->LoadDiscountInfo();

                                echo draw_success_message(_CHANGES_SAVED);
                            }else{
								if($american_format){
									$date_from = get_month_local($checkin_month).' '.$checkin_day.', '.$checkin_year;
									$date_to   = get_month_local($checkout_month).' '.$checkout_day.', '.$checkout_year;
								}else{
									$date_from = $checkin_day.' '.get_month_local($checkin_month).' '.$checkin_year;
									$date_to   = $checkout_day.' '.get_month_local($checkout_month).' '.$checkout_year;
								}
								$message = str_replace(array('_DATE_FROM_', '_DATE_TO_'), array($date_from, $date_to), _THERE_NO_AVAILABLE_ROOMS);
                                echo draw_important_message($message);
                            }
                        }else{
							if($american_format){
								$date_from = get_month_local($checkin_month).' '.$checkin_day.', '.$checkin_year;
								$date_to   = get_month_local($checkout_month).' '.$checkout_day.', '.$checkout_year;
							}else{
								$date_from = $checkin_day.' '.get_month_local($checkin_month).' '.$checkin_year;
								$date_to   = $checkout_day.' '.get_month_local($checkout_month).' '.$checkout_year;
							}
							$message = str_replace(array('_DATE_FROM_', '_DATE_TO_'), array($date_from, $date_to), _THERE_NO_AVAILABLE_ROOMS);
							echo draw_important_message($message);
                        }
                    }
                }
            }else{
                echo draw_important_message(_WRONG_PARAMETER_PASSED);
            }
        }else if(in_array($act, array('add','remove'))){
            $meal_plan_id               = isset($_POST['meal_plans']) ? (int)$_POST['meal_plans'] : '';
            $hotel_id                   = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : '0';
            $available_rooms            = isset($_POST['available_rooms']) ? prepare_input($_POST['available_rooms']) : '';
            $available_rooms_parts      = explode('-', $available_rooms);
            $available_extra_beds       = isset($_POST['available_extra_beds']) ? prepare_input($_POST['available_extra_beds']) : '';
            $available_extra_beds_parts = explode('-', $available_extra_beds);

            $checkinParts   = explode('-', $from_date);
            $checkin_year   = isset($checkinParts[0]) ? $checkinParts[0] : '';
            $checkin_month  = isset($checkinParts[1]) ? $checkinParts[1] : '';
            $checkin_day    = isset($checkinParts[2]) ? $checkinParts[2] : '';
            $checkoutParts  = explode('-', $to_date);
            $checkout_year  = isset($checkoutParts[0]) ? $checkoutParts[0] : '';
            $checkout_month = isset($checkoutParts[1]) ? $checkoutParts[1] : '';
            $checkout_day   = isset($checkoutParts[2]) ? $checkoutParts[2] : '';

            // -----------------------------------------------------------------
            $nights = nights_diff($checkin_year.'-'.$checkin_month.'-'.$checkin_day, $checkout_year.'-'.$checkout_month.'-'.$checkout_day);

            $rooms                 = isset($available_rooms_parts[0]) ? (int)$available_rooms_parts[0] : '';
            $price_post            = isset($available_rooms_parts[1]) ? (float)$available_rooms_parts[1] : 0;

            $extra_beds            = isset($available_extra_beds_parts[0]) ? (int)$available_extra_beds_parts[0] : '';
            $extra_bed_charge_post = isset($available_extra_beds_parts[1]) ? (float)$available_extra_beds_parts[1] : '';

            $params = array(
                'from_date'    => $checkin_year.'-'.$checkin_month.'-'.$checkin_day,
                'to_date'      => $checkout_year.'-'.$checkout_month.'-'.$checkout_day,
                'from_year'    => $checkin_year,
                'from_month'   => $checkin_month,
                'from_day'     => $checkin_day,
                'to_year'      => $checkout_year,
                'to_month'     => $checkout_month,
                'to_day'       => $checkout_day,
                'max_adults'   => $adults,
                'max_children' => $children,
            );

            if($nights_post != $nights){			
                draw_important_message(_WRONG_PARAMETER_PASSED);
                $operation_allowed = false;
            }
            // -----------------------------------------------------------------
            $min_rooms_packages = Packages::GetMinimumRooms($checkin_year.'-'.$checkin_month.'-'.$checkin_day, $checkout_year.'-'.$checkout_month.'-'.$checkout_day, $hotel_id, false);
            $max_rooms_packages = Packages::GetMaximumRooms($checkin_year.'-'.$checkin_month.'-'.$checkin_day, $checkout_year.'-'.$checkout_month.'-'.$checkout_day, $hotel_id, false);
            $minimum_rooms = $min_rooms_packages['minimum_rooms'];
            $maximum_rooms = $max_rooms_packages['maximum_rooms'];
            if((!empty($maximum_rooms) && $maximum_rooms != '-1' && $rooms > $maximum_rooms) || $rooms < $minimum_rooms){
                draw_important_message(_WRONG_PARAMETER_PASSED);
                $operation_allowed = false;
            }
            // -----------------------------------------------------------------
            $price = Rooms::GetRoomPrice($room_id, $hotel_id, $params) * $rooms;
            if(abs($price_post - $price) >= 0.01){
                draw_important_message(_WRONG_PARAMETER_PASSED);
                $operation_allowed = false;
            }		
            // -----------------------------------------------------------------
            
            $extra_bed_charge = Rooms::GetRoomExtraBedsPrice($room_id, $params) * $extra_beds;
            if($extra_bed_charge_post != $extra_bed_charge){
                draw_important_message(_WRONG_PARAMETER_PASSED);
                $operation_allowed = false;
            }		
            // -----------------------------------------------------------------
            
            if($operation_allowed){
                if($act == 'remove'){
                    $objReservation->RemoveReservation($rid);
                }else if($act == 'add'){
                    $objReservation->AddToReservation($room_id, $from_date, $to_date, $nights, $rooms, $price, $adults, $children, $meal_plan_id, $hotel_id, $extra_beds, $extra_bed_charge);
                }
                // refresh discount info
                $objReservation->LoadDiscountInfo();
            }        
        }
		
		if($objLogin->IsLoggedInAsAdmin()) draw_title_bar(prepare_breadcrumbs(array(_BOOKING=>'')));
		
        //draw_title_bar(_BOOKING);
        
		// test mode alert
		if(Modules::IsModuleInstalled('booking')){
			if(ModulesSettings::Get('booking', 'mode') == 'TEST MODE'){
				draw_message(_TEST_MODE_ALERT_SHORT, true, true);
			}        
		}

		//Campaigns::DrawCampaignBanner('standard');
		//Campaigns::DrawCampaignBanner('global');

		if($objReservation->error) echo $objReservation->error;
		$objReservation->ShowReservationInfo();
		draw_content_end();
		
	}else{
		draw_title_bar(_BOOKINGS);
		draw_important_message(_NOT_AUTHORIZED);	
	}
}else{
	draw_title_bar(_BOOKINGS);
	draw_important_message(_NOT_AUTHORIZED);
}


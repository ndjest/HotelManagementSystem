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

$objNews = News::Instance();

// Draw title bar
if($objSession->IsMessage('notice')){
	draw_title_bar(_NEWS);
	echo $objSession->GetMessage('notice');
}else{
	$objNews->DrawNews(Application::Get('news_id'));
}


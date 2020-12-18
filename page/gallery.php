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

if(Modules::IsModuleInstalled('gallery')){
	$objGalleryAlbum = new GalleryAlbums();
	$objGalleryAlbum->DrawAlbum(Application::Get('album_code'));	
}else{		
	draw_important_message(_PAGE_UNKNOWN);		
}
	

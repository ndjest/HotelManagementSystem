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

    // 'Featured Offers'
	$featured_offers = Hotels::DrawHotelsByGroup(6, false, false);
	if(!empty($featured_offers)){
?>
<div class="row anim3">
	<?php echo $featured_offers; ?>
</div>
<?php } ?>

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

    // 'today's top deals'
	$top_deals = Hotels::DrawHotelsByGroup(5, false, false);
	if(!empty($top_deals)){
?>
<div class="row anim2">
	<?php echo $top_deals; ?>
</div>
<?php } ?>

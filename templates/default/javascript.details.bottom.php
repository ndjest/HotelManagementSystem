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

?>

<!-- Javascript -->	
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/js-details.js"></script>
<!-- Custom Select -->
<script type='text/javascript' src='<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/jquery.customSelect.min.js'></script>
<!-- Review icon -->
<script type='text/javascript' src='<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/js/lightbox.js'></script>	
<!-- Custom functions -->
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/functions.js"></script>
<!-- Nicescroll  -->	
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/jquery.nicescroll.min.js"></script>
<!-- jQuery KenBurn Slider  -->
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/rs-plugin/js/jquery.themepunch.revolution.min.js" type="text/javascript"></script>
<?php if(GALLERY_TYPE == 'carousel'){ ?>
    <script src="<?php echo APPHP_BASE; ?>modules/bxslider/jquery.bxslider.min.js"></script>
<?php }else{ ?>
    <!-- CarouFredSel -->
    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/jquery.carouFredSel-6.2.1-packed.js"></script>
    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/helper-plugins/jquery.touchSwipe.min.js"></script>

    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/helper-plugins/jquery.mousewheel.min.js" type="text/javascript"></script>
    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/helper-plugins/jquery.transit.min.js" type="text/javascript"></script>
    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/helper-plugins/jquery.ba-throttle-debounce.min.js" type="text/javascript"></script>
    <!-- Carousel-->	
    <script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/initialize-carousel-detailspage.js"></script>		
<?php } ?>
<!-- Js Easing-->	
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/assets/js/jquery.easing.min.js"></script>
<!-- Bootstrap-->	
<script src="<?php echo APPHP_BASE; ?>templates/<?php echo Application::Get('template');?>/dist/js/bootstrap.min.js"></script>
<!-- Picker -->	
<script src="<?php echo APPHP_BASE; ?>js/jquery/jquery-ui.min.js"></script>

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

$mg_language_id = isset($_REQUEST['mg_language_id']) ? prepare_input($_REQUEST['mg_language_id']) : Application::Get('lang');
if(Application::Get('system_page') != ''){
    $objPage = new Pages(((Application::Get('system_page') != '') ? Application::Get('system_page') : Application::Get('page_id')), true, $mg_language_id);
    $page_title = ucwords(str_replace('_', ' ', Application::Get('system_page')));
}else if(Application::Get('news_id') != ''){
    $page_title = '<li><a href="index.php?page=news">'._NEWS.'</a></li>';
    //$page_title .= '<li>/</li>';
    //$page_title .= '<li><a href="index.php?page=news&nid='.(int)Application::Get('news_id').'">'._NEWS.'</a></li>';
}else if(Application::Get('page') != 'pages'){
	$page_title = ucwords(str_replace('_', ' ', Application::Get('page')));
}else{
	$objPage = new Pages(Application::Get('page_id'), false, $mg_language_id);
    $page_title = $objPage->GetParameter('page_title');
}

?>
	<div class="container breadcrub">
	    <div>
			<a class="homebtn left"></a>
			<div class="left">
				<ul class="bcrumbs">
                    <li><a href="index.php"><?php echo _HOME; ?></a></li>
					<li>/</li>
					<li><?php echo strip_tags($page_title); ?></li>
				</ul>				
			</div>

            <div class="right">
			<?php echo Search::DrawQuickSearch(); ?>
            </div>
			<!--<a class="backbtn right" href="#"></a>-->            
            
		</div>
		<div class="clearfix"></div>
		<div class="brlines"></div>
	</div>

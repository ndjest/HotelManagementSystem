<?php
/**
* @project uHotelBooking
* @copyright (c) 2018 ApPHP
* @author ApPHP <info@apphp.com>
* @site http://www.hotel-booking-script.com
* @license http://hotel-booking-script.com/license.php
*/

define('CSRF_VALIDATION', false);

$host = isset($_GET['host']) ? urldecode(base64_decode($_GET['host'])) : '';
$key = isset($_GET['key']) ? base64_decode($_GET['key']) : '';
$hids = isset($_GET['hids']) ? str_ireplace('-', ',', base64_decode($_GET['hids'])) : '';

$basedir = '../../../';

require_once($basedir.'include/base.inc.php');
if($key != INSTALLATION_KEY) exit(0);

require_once($basedir.'include/shared.inc.php');
require_once($basedir.'include/settings.inc.php');
require_once($basedir.'include/functions.database.'.(DB_TYPE == 'PDO' ? 'pdo.' : 'mysqli.').'inc.php');
require_once($basedir.'include/functions.common.inc.php');
require_once($basedir.'include/functions.html.inc.php');
require_once($basedir.'include/functions.validation.inc.php');

// autoloading classes
//------------------------------------------------------------------------------
function __autoload($class_name){
    global $basedir;
	
    $core_classes = array(
        /* core classes ALL - no differences */
        'Backup',
        'BanList',
        'Banners',
        'Email',
        'GalleryAlbums',
        'GalleryAlbumItems',
		'MailLogs',
        'MicroGrid',
        'Modules',
        'ModulesSettings',
        'Roles',
        'RolePrivileges',
        'Session',
        'Settings',
        'SocialNetworks',
        'States',
        /* core classes ALL - have differences */
        'Cron',
        /* core classes excepting MicroBlog - no differences */
        'Accounts',
        'Admins',
        'ContactUs',
        'FaqCategories',
        'FaqCategoryItems',
        'News',
        'NewsSubscribed',
        'PagesGrid',
        'RSSFeed',
        'SiteDescription',
        'Vocabulary',
        /* core classes excepting MicroBlog - have differences */
        'AdminsAccounts',
        'Application',
        'Comments',
        'EmailTemplates',
        'Languages',
        'Pages',
        /* core classes excepting MicroBlog, MicroCMS - have differences */
        'Currencies',
    );
	
    if(in_array($class_name, $core_classes)){
		require_once($basedir.'include/classes/core/'.$class_name.'.class.php');
	}else{
		require_once($basedir.'include/classes/'.$class_name.'.class.php');
	}
}

define('APPHP_BASE', get_base_url());
@date_default_timezone_set(TIME_ZONE);

$objSession 		= new Session();
$objLogin 			= new Login();
$objSettings 		= new Settings();
$objSiteDescription = new SiteDescription();
Modules::Init();
ModulesSettings::Init();
Application::Init();
Languages::Init();

require_once($basedir.'include/messages.en.inc.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>Reservation Form</title>
    <script type="text/javascript" src="<?php echo $host; ?>js/main.js"></script>
    <script type="text/javascript" src="<?php echo $host; ?>js/jquery-1.8.3.min.js"></script>

    <link href="<?php echo $host; ?>templates/default/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="<?php echo $host; ?>templates/default/assets/css/custom.css" type="text/css" rel="stylesheet" />

	<?php if(CALENDAR_HOTEL == 'new'){ ?>
		<!-- Picker UI-->	
		<script src="<?php echo $host; ?>js/jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="<?php echo $host; ?>js/jquery/jquery-ui.css" />
	<?php } ?>
</head>
<body>
    <?php
        echo '<h2>'._RESERVATION.'</h2>';				
        Rooms::DrawSearchAvailabilityBlock(false, '', $hids, 8, 3, 'room-inline', $host, '_parent', true);
        Rooms::DrawSearchAvailabilityFooter('', $host);
    ?>
</body>
</html>
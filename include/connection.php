<?php
/**
* @project uHotelBooking
* @copyright (c) 2018 ApPHP
* @author ApPHP <info@apphp.com>
* @site http://www.hotel-booking-script.com
* @license http://hotel-booking-script.com/license.php
*/

@session_start();

//------------------------------------------------------------------------------
require_once('shared.inc.php');
require_once('settings.inc.php');
require_once('functions.validation.inc.php');
require_once('functions.common.inc.php');
require_once('functions.html.inc.php');
require_once('functions.database.'.(DB_TYPE == 'PDO' ? 'pdo.' : 'mysqli.').'inc.php');
define('APPHP_BASE', get_base_url());

// autoloading classes
//------------------------------------------------------------------------------
function __autoload($class_name){

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

    $api_classes = array(
		'BookingsApi',
		'BookingsRoomsApi',
		'CustomersApi',
		'HotelsApi',
		'HotelsDescriptionApi',
		'RoomsApi',
		'RoomsDescriptionApi',
    );


    if($class_name == 'PHPMailer'){
		require_once('modules/phpmailer/class.phpmailer.php');
	}else if($class_name == 'tFPDF'){
		require_once('modules/tfpdf/tfpdf.php');
    }else if(in_array($class_name, $core_classes)){
        require_once('classes/core/'.$class_name.'.class.php');	
    }else if(in_array($class_name, $api_classes)){
        require_once('classes/api/'.$class_name.'.class.php');	
	}else{
		if(is_file(__DIR__.'/classes/'.$class_name.'.class.php')){
			require_once('classes/'.$class_name.'.class.php');	
		}
	}	
}

if(defined('APPHP_CONNECT') && APPHP_CONNECT == 'direct'){	
	// Set time zone
	//------------------------------------------------------------------------------
	@date_default_timezone_set(TIME_ZONE);
	
	Modules::Init();
	ModulesSettings::Init();

	// create main objects
	//------------------------------------------------------------------------------
	$objSession  = new Session();
	$objLogin    = new Login();
	$objSettings = new Settings();
	
    $lang_file = $objSession->GetSessionVariable('lang');
    if(empty($lang_file)){
        // use messages file according to preferences
        $lang_file = $objLogin->GetPreferredLang();
        if(empty($lang_file)){
            $lang_file = Languages::GetDefaultLang();
        }
    }
    include_once('messages'.($lang_file != '' ? '.'.$lang_file : '').'.inc.php');
	
}else{
	// set timezone
	//------------------------------------------------------------------------------
	Settings::SetTimeZone();	
	Modules::Init();
	ModulesSettings::Init();

	// create main objects
	//------------------------------------------------------------------------------
	$objSession 		= new Session();
	$objLogin 			= new Login();
	$objSettings 		= new Settings();
	$objSiteDescription = new SiteDescription();
	Application::Init();
	Languages::Init();
	
	// force SSL mode if defined
	//------------------------------------------------------------------------------
	$ssl_mode = $objSettings->GetParameter('ssl_mode');
	$ssl_enabled = false; 
	if($ssl_mode == '1'){
		$ssl_enabled = true; 
	}else if($ssl_mode == '2' && $objLogin->IsLoggedInAsAdmin()){
		$ssl_enabled = true; 
	}else if($ssl_mode == '3' && $objLogin->IsLoggedInAsCustomer()){
		$ssl_enabled = true; 
	}
	if($ssl_enabled && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') && isset($_SERVER['HTTP_HOST'])){ 
		redirect_to('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); 
	}
	
	// include files for administrator use only
	//------------------------------------------------------------------------------
	if($objLogin->IsLoggedInAsAdmin()){
		include_once('functions.admin.inc.php');
	}
	
	// include files for custom template
	//------------------------------------------------------------------------------
	if(file_exists('templates/'.Application::Get('template').'/lib/functions.template.php')){
		include_once('templates/'.Application::Get('template').'/lib/functions.template.php');
	}	
	
	// include language file
	//------------------------------------------------------------------------------
	if(!defined('APPHP_LANG_INCLUDED')){
		if(get_os_name() == 'windows'){
			$lang_file_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'include/messages.'.Application::Get('lang').'.inc.php';
		}else{
			$lang_file_path = 'include/messages.'.Application::Get('lang').'.inc.php';
		}
		if(file_exists($lang_file_path)){
			include_once($lang_file_path);
		}else if(file_exists('include/messages.inc.php')){
			include_once('include/messages.inc.php');
		}
	}	
		
    // *** run cron jobs file
    // -----------------------------------------------------------------------------
    if($objSettings->GetParameter('cron_type') == 'non-batch'){
        if(file_exists('cron.php')) include_once('cron.php');		
    }
}

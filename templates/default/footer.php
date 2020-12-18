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

if(Application::Get('page') == 'home' && Application::Get('customer') == ''){
	$ftitle = 'ftitle';
	$footerbg = 'footerbg';
	$footerbg3 = 'footerbg3';
}else{
	$ftitle = 'ftitleblack';
	$footerbg = 'footerbgblack';
	$footerbg3 = 'footerbg3black';
}

?>
<!-- FOOTER -->
<div class="<?php echo $footerbg; ?>">
    <div class="container">		
        
        <div class="col-md-3">
            <span class="<?php echo $ftitle; ?>"><?php echo _LETS_SOCIALIZE; ?></span>
            <div class="scont">
                <a href="#" class="social1b"><img src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>icon-facebook.png" alt=""/></a>
                <a href="#" class="social2b"><img src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>icon-twitter.png" alt=""/></a>
                <a href="#" class="social3b"><img src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>icon-gplus.png" alt=""/></a>
                <a href="#" class="social4b"><img src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>icon-youtube.png" alt=""/></a>
                <br/><br/>
            </div>
            
            <?php
                if(Modules::IsModuleInstalled('booking') && (in_array(ModulesSettings::Get('booking', 'is_active'), array('global', 'front-end')))){
                    if(ModulesSettings::Get('booking', 'payment_type_paypal') != 'no' || ModulesSettings::Get('booking', 'payment_type_2co') != 'yes' || ModulesSettings::Get('booking', 'payment_type_authorize') != 'yes'){
                        echo '<span class="'.$ftitle.'">'._PAYMENT_METHODS.'</span>';
                        echo '<div class="scont"><img src="images/ppc_icons/logo_paypal.gif" title="PayPal" alt="PayPal" />
                              <img src="images/ppc_icons/logo_ccVisa.gif" title="Visa" alt="Visa" />
                              <img src="images/ppc_icons/logo_ccMC.gif" title="MasterCard" alt="MasterCard" />
                              <img src="images/ppc_icons/logo_ccAmex.gif" title="Amex" alt="Amex" /></div>';
                    }
                }
            ?>            
            <br/>
            
            <a href="index.php"><img class="footer-logosmal" src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>logosmal2.png" alt="small logo" /></a><br/>
            <span class="grey2"><?php echo _COPYRIGHT; ?> &copy; <?php echo date('Y'); ?><br> <?php echo _ALL_RIGHTS_RESERVED; ?></span><br/>
			<?php if(SHOW_COPYRIGHT){ ?>
			<span class="powered">Powered by <a href="http://apphp.com">ScepterHolders</a></span>
			<?php } ?>
        </div>
        <!-- End of column 1-->
        
        <div class="col-md-3">
            <span class="<?php echo $ftitle; ?>"><?php echo _GENERAL; ?></span>
            <br/><br/>
            <ul class="footerlistblack">
            <?php 
                // Draw footer menu
                Menu::DrawFooterMenu('ul', 'general');	
            ?>
            </ul>
        </div>
        <!-- End of column 2-->		
        
        <div class="col-md-3">
            <span class="<?php echo $ftitle; ?>"><?php echo _INFORMATION; ?></span>
            <br/><br/>
            <ul class="footerlistblack">
                <?php 
                    // Draw footer menu
                    Menu::DrawFooterMenu('ul', 'system');	
                ?>
            </ul>				
        </div>
        <!-- End of column 3-->		
        
        <div class="col-md-3 grey">
            <?php
                if(Modules::IsModuleInstalled('news')){
                    $objNews = News::Instance();
                    echo $objNews->DrawSubscribeBlockFooter(false);	
                }
            ?>            
            <br/><br/>
            <?php
				$support_info = Hotels::GetSupportInfo();			
				if(!empty($support_info)){
					echo '<span class="ftitle">'._CUSTOMER_SUPPORT.'</span><br/>';
					echo !empty($support_info['phone']) ? '<span class="pnr">'.$support_info['phone'].'</span><br/>' : '';
					//echo !empty($support_info['fax']) ? '<span class="pnr">'.$support_info['fax'].'</span><br/>' : '';
					echo !empty($support_info['email']) ? '<span class="grey2">'.$support_info['email'].'</span>' : '';
				}				
			?>

			<br><br>
			<a href="feeds/rss.xml" title="RSS Feed"><img src="templates/default/images/rss.png" alt="RSS Feed"></a>
        </div>			
        <!-- End of column 4-->			            
    </div>	
</div>

<div class="<?php echo $footerbg3; ?>">
    <div class="container center grey">
        
        <form name="frmLogout" id="frmLogout" action="<?php echo APPHP_BASE; ?>index.php" method="post">
        <?php if($objLogin->IsLoggedIn()){ ?>
            <?php draw_hidden_field('submit_logout', 'logout'); ?>	
			<?php echo prepare_permanent_link('index.php?customer=home', _DASHBOARD, '', 'main_link'); ?> &nbsp;|&nbsp;	
			<?php echo prepare_permanent_link('index.php?customer=my_account', _MY_ACCOUNT, '', 'main_link'); ?> &nbsp;|&nbsp;	
            <a class="main_link" href="javascript:appFormSubmit('frmLogout');"><?php echo _BUTTON_LOGOUT; ?></a>
        <?php }else{ ?>
            <?php
                if(Modules::IsModuleInstalled('customers')){
                    if(ModulesSettings::Get('customers', 'allow_login') == 'yes'){
						echo prepare_permanent_link('index.php?customer=login', _CUSTOMER_LOGIN, '', 'main_link');						
						if(ModulesSettings::Get('customers', 'allow_agencies') == 'yes' && SHOW_TRAVEL_AGENCY_LOGIN){
							echo '&nbsp;&nbsp;'.draw_divider(false).'&nbsp;&nbsp;';
							echo prepare_permanent_link('index.php?customer='.TRAVEL_AGENCY_LOGIN, _AGENCY_LOGIN, '', 'main_link');							
						}
                    }
                }
				if(SHOW_ADMIN_LOGIN){
					echo '&nbsp;&nbsp;'.draw_divider(false).'&nbsp;&nbsp;';
					echo prepare_permanent_link('index.php?admin='.ADMIN_LOGIN, _ADMIN_LOGIN, '', 'main_link');		
				}
            ?>
        <?php } ?>
        </form>
        <br>    
        <a href="#top" class="gotop scroll"><img src="<?php echo 'templates/'.Application::Get('template').'/images/'; ?>spacer.png" alt=""/></a>
    </div>
</div>

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

// Draw title bar
//draw_title_bar(prepare_breadcrumbs(array(_ACCOUNTS=>'',_ADMIN_LOGIN=>'')));

// Check if admin is logged in
if(!$objLogin->IsLoggedIn()){
	if($objLogin->IsWrongLogin()) draw_important_message(_WRONG_LOGIN);
?>
	<!--<div class="pages_contents">-->
		
	<form class="form-horizontal m-t-20" action="index.php?admin=<?php echo ADMIN_LOGIN; ?>" method="post">
		<?php draw_hidden_field('submit_login', 'login'); ?>
		<?php draw_hidden_field('type', 'admin'); ?>
		<?php draw_token_field(); ?>

		<div class="form-group ">
			<div class="col-xs-12">
				<input id="txt_user_name" name="user_name" class="form-control" type="text" required="" placeholder="Username" autocomplete="off" />
			</div>
		</div>
	
		<div class="form-group">
			<div class="col-xs-12">
				<input name="password" class="form-control" type="password" required="" placeholder="Password" autocomplete="off" />
			</div>
		</div>
	
		<?php
			//<div class="form-group ">
			//	<div class="col-xs-12">
			//		<div class="checkbox checkbox-custom">
			//			<input id="checkbox-signup" type="checkbox">
			//			<label for="checkbox-signup">
			//				Remember me
			//			</label>
			//		</div>	
			//	</div>
			//</div>
		?>

		<div class="form-group text-center m-t-30">
			<div class="col-xs-12">
				<button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit"><?php echo _BUTTON_LOGIN;?></button>
			</div>
		</div>
	
		<div class="form-group m-t-30 m-b-0">
			<div class="col-sm-12">
				<?php echo prepare_permanent_link('index.php?admin=password_forgotten', '<i class="fa fa-lock m-r-5"></i> '._FORGOT_PASSWORD); ?>
			</div>
		</div>
	</form>

	<script type="text/javascript">appSetFocus('txt_user_name');</script>	
<?php
}else if($objLogin->IsLoggedInAsAdmin()){
	draw_important_message(_ALREADY_LOGGED);
	draw_content_start();
?>
	<form action="index.php?page=logout" method="post">
		<?php draw_hidden_field('submit_logout', 'logout'); ?>
		<?php draw_token_field(); ?>
		<input class="form_button" type="submit" name="submit" value="<?php echo _BUTTON_LOGOUT;?>">
	</form>
<?php
	draw_content_end();	
}else{
	$objSession->SetMessage('notice','');
	draw_important_message(_NOT_AUTHORIZED);
}
?>

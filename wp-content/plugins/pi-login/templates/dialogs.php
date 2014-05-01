<div class="pi-login-popups" style="display: none">

<div id="sign-in-dialog" title="Sign In:" class="pi-login-popup">
	<div style="clear: both"></div>
<?php if(isset($_GET['login_error'])){?>
    <span id="lPiLogin_Status" class="ui-message-error"><?= apply_filters('get_login_error_message_by_error_code', 'Unknown Error.', $_GET['login_error']); ?></span>
<?php } else{ ?>
	<span id="lPiLogin_Status">&nbsp;</span>
<?php } ?>
	<form name="PiLogin_Form" id="PiLogin_Form" action="#"  method="post" style="height: 100px;">
        <div style="display: block; height: 48px; width: 412px; vertical-align:bottom">
		<div class="restore2">
			<label><?php _e( 'Username' ) ?></label>
			<input type="text" name="PiLoginName" id="lPiLoginName" class="input" />
        </div>
		<div class="restore2">
			<label><?php _e( 'Password' ) ?></label>
			<input type="password" name="PiLoginPass" id="lPiLoginPass" class="input" value=""  />
        </div>
		<div class="">
		   <input type="hidden" name="redirect_to" id="redirect_to"  value="<?= $redirect_url ?>" />
		</div>
		
		
		</div>
		
		<div class="form-input-links">
			<div class="remember-my-checkbox">
				<input type="checkbox" name="PiLoginRemember" id="lPiLoginRemember" checked="checked" value="1"/>
			</div>			
			<div class="links-right">
				<div style="float: left;">Remember me &nbsp;Not a member?</div>
				<div style="float: left; padding-left: 5px;"><a class = "sign-up-link" href="#">Sign Up</a> / <a class = "forgot-link" href="#">Forgot your password?</a></div>
			</div>
			<div class="links-left"><?php do_action('render_fb_login_button');?></div>
			
			<div style="clear:both;float:none;"></div>
		</div>
	</form>
</div>

<div id="sign-up-dialog" title="Sign Up:" class="pi-login-popup">
	<div style="clear: both"></div>
	<span id="rPiLogin_Status"></span>
	<form name="PiSignon_Form" id="PiSignon_Form" action="#" method="post" style="padding-top: 16px; height: 263px;" >
				
				<div class="register-form">
					<label style="float: none; padding: 3px 0 0;"><span><?php _e( 'E-mail' ) ?></span>
					<input type="text" name="email" id="a_email" class="input" style="width: 265px !important;"/>
                    </label>
					
					<label style="float: none; padding: 3px 0 0;"><span><?php _e( 'Username' ) ?></span>
					<input type="text" name="user_login" id="a_user_login" class="input" style="width: 265px !important;"/>
                    </label>
				</div>
              				
                <div class="register-form"> 
					<label style="float: none; padding: 3px 0 0;"><span><?php _e( 'Password' ) ?></span>
					<input type="password" name="pass1" id="a_pass1" class="input" value="" style="width: 265px !important;"/>
                    </label>
					
					<label style="float: none; padding: 3px 0 0;"><span><?php _e( 'Confirm password' ) ?></span>
					<input type="password" name="pass2" id="a_pass2" class="input" value="" style="width: 265px !important;"/>
					</label>
					
                </div>				
				
                    <div class="sign-up-fb-2" ><?php do_action('render_fb_login_button');?> </div>
					<input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>" />
					<div class="form-input-links form-sign">Already member? <a class = "sign-in-link" href="#">Sign In</a></div>
	</form>
</div>

<div id="forgot-dialog" title="Restore password:" class="pi-login-popup">
	<div style="clear: both"></div>
	<span id="fPiLogin_Status">&nbsp;</span>
	<form name="PiForgot_Form" id="PiForgot_Form" action="#" method="post"  style="height: 80px;">

	            <div class="restore"> 
					<label><?php _e( 'An email will be sent to the address of the account' ) ?></label>
					<br />
					<input type="text" name="PiLoginEmail" id="fPiLoginEmail" class="input" />
					
				</div>	

		<input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>" />
		<div class="form-input-links rest"><a class = "sign-in-link" href="#">Cancel</a></div>
	</form>
</div>
</div>


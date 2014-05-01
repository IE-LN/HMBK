<?php
global $ice_profile;
$args = array( 'label_log_in' => __( 'Sign In' ) );
$redirect = empty($_REQUEST['redirect_to']) ? false : $_REQUEST['redirect_to'];
if (empty($redirect)) {
    $ref = false;
    if (!$ref && is_user_logged_in()) $ref = true;
    if (!$ref && defined('ICE_LOGIN_SLUG') && $ice_profile->current_component == ICE_LOGIN_SLUG) $ref = true;
    if (!$ref && defined('ICE_AGE_INELIGIBLE_SLUG') && $ice_profile->current_component == ICE_AGE_INELIGIBLE_SLUG) $ref = true;
    if($ref)
    {
        $url = $_SERVER['HTTP_REFERER'];
        $url_parsed = parse_url($url);
        $site_parsed = parse_url(get_bloginfo('siteurl'));
        if ($url_parsed['host'] == $site_parsed['host']) $redirect = $url;
    }else{
        $redirect = $_SERVER['REQUEST_URI'];
    }
}
if($redirect)$args['redirect'] = $redirect;
?>
<div id="login-page">
	<div class="fb-divider">OR</div>
	<h2 class="sign-in-title">Sign in to <span class="sign-in-its-name"><?=get_bloginfo('name');?></span> on Celebuzz</h2>
	<table><tbody>
	<tr><td class="sign-in-form-left">
        <?php if(isset($_REQUEST['fb_login_error'])){ ?>
            <span id="ice-login-status" class="invalid">
                <?=apply_filters('ice_fb_get_message_by_error_code', '', $_REQUEST['fb_login_error']);?>
            </span>
        <?php }else { ?>
            <span id="ice-login-status"></span>
        <?php } ?>
        <?= wp_login_form( $args ); ?>
	</td><td class="sign-in-form-right">
	<p>Sign in with Facebook</p>
    <?php do_shortcode("[fb_login login_text='Login with Facebook' logout_text='Logout']"); ?>
	</td></tr>
	</tbody></table>
	<div id="login-links">
	Not a member? <a class="join" href="<?=site_url(ICE_REGISTER_SLUG);?>">Join Now</a> / <a href="<?=site_url('/wp-login.php?action=lostpassword');?>">Forgot your password?</a>
	</div>

</div>


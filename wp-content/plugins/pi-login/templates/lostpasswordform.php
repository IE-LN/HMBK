<?php
    get_header();
    $msg = '';
    if(!empty($errors))$msg = $errors->get_error_message();
    if(!empty($msg)) $msg = '<p class="message reset-pass">'.$msg.'</p>';
?>
<div id="page">
<div id="content" role="main">
<div class="reset-password">
<?=$msg?>
<form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( site_url( 'reset-password/?action=lostpassword', 'login_post' ) ); ?>" method="post">
    <p>
        <label for="user_login" ><?php _e('Username or E-mail:') ?><br />
        <input type="text" name="PiLoginEmail" id="PiLoginEmail" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
    </p>
<?php do_action('lostpassword_form'); ?>
    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
    <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Get New Password'); ?>" tabindex="100" /></p>
</form>
</div>
</div>
</div>
<?php get_sidebar(); ?>
<?php
    get_footer();
?>

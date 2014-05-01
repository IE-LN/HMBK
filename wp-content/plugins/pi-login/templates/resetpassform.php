<?php
    get_header();
    $msg = 'Enter your new password below.';
    if(!empty($errors))$msg = $errors->get_error_message();
?>
<div id="page">
<div id="content" role="main">
<div class="reset-password">
<p class="message reset-pass"><?=$msg?></p>
<form name="resetpassform" id="resetpassform" action="<?php echo esc_url( site_url( 'reset-password/?action=resetpass&key=' . urlencode( $key ) . '&login=' . urlencode($login), 'login_post' ) ); ?>" method="post">
    <input type="hidden" id="user_login" value="<?php echo esc_attr( $login ); ?>" autocomplete="off" />

    <p>
        <label for="pass1"><?php _e('New password') ?><br />
        <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" /></label>
    </p>
    <p>
        <label for="pass2"><?php _e('Confirm new password') ?><br />
        <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" /></label>
    </p>

    <br class="clear" />
    <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Reset Password'); ?>" tabindex="100" /></p>
</form>
</div>
</div>
</div>
<?php get_sidebar(); ?>
<?php
    get_footer();
?>

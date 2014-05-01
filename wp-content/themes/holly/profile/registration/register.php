<?php
global $use_sidebar;
$use_sidebar = 'none';
get_header();
?>
<?php global $hide_pagenavi; ?>
<?php $hide_pagenavi = true;?>
<div id="containerMain">
<div id="register-page">
    <div class="register-header">
        <h1>JOIN <span class="its_green"><?=get_bloginfo('name');?></span> on CELEBUZZ </h1>

        <?php if ( 'request-details' == icep_get_current_signup_step() ) { ?>
        <div class="already-member">Already a member? <a class="sign-in" href="/sign-in/" title="Sign-in to your account."><?php _e( 'Sign in', 'buddypress' ) ?></a></div>
        <div class="facebook-connect">
            <div class="signup-or">sign up below, or</div>
            <?php do_shortcode("[fb_login login_text='Login with Facebook' logout_text='Logout']"); ?>
        </div>
        <?php } ?>
    </div>
    <div id="edit-profile-container-wrap">
        <div id="container">
            <div class="container-inner">
            <?php do_action( 'icep_before_register_page' ) ?>
            <form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">
            <?php if ( 'request-details' == icep_get_current_signup_step() ) : ?>
                <?php do_action( 'template_notices' ) ?>
                <?php do_action( 'icep_before_account_details_fields' ) ?>
                <div id="basic-details-section">
                    <div class="editfield">
                        <label for="signup_username"><?php _e( 'Choose a Username', 'buddypress' ) ?></label>
                        <?php do_action( 'icep_signup_username_errors' ) ?>
                        <input type="text" name="signup_username" id="signup_username" value="<?php icep_signup_username_value() ?>" />
                        <div class="helper-text">This will be your display name on the site.</div>
                    </div>

                    <div class="editfield">
                        <label for="signup_email"><?php _e( 'Your Email', 'buddypress' ) ?></label>
                        <?php do_action( 'icep_signup_email_errors' ) ?>
                        <input type="text" name="signup_email" id="signup_email" value="<?php icep_signup_email_value() ?>" />
                        <div class="helper-text">We will never spam you or sell your email address to others.</div>
                    </div>

                    <div class="editfield">
                        <label for="signup_password"><?php _e( 'Password', 'buddypress' ) ?><span class="helper-text password-helper">Enter it twice, please.</span></label>
                        <div style="clear:both;"></div>
                        <?php do_action( 'icep_signup_password_errors' ) ?>
                        <input type="password" name="signup_password" id="signup_password" value="" />
                        <?php do_action( 'icep_signup_password_confirm_errors' ) ?>
                        <input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />
                    </div>
                </div>

                <?php do_action( 'icep_after_account_details_fields' ) ?>
                <?php /***** Extra Profile Details ******/ ?>
                <?php if ( true ) : ?>
                    <div class="register-section" id="profile-details-section">
                        <?php do_action( 'icep_before_signup_profile_fields' ) ?>
                        <?php if ( function_exists( 'icep_has_profile' ) ) : if ( icep_has_profile( 'profile_group_id=1' ) ) : while ( icep_profile_groups() ) : icep_the_profile_group(); ?>
                            <?php while ( icep_profile_fields() ) : icep_the_profile_field(); ?>

                                <div class="editfield"></h4>

                                    <?php if ( 'textbox' == icep_get_the_profile_field_type() ) : ?>

                                        <label for="<?php icep_the_profile_field_input_name() ?>"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php //_e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
                                        <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                        <input type="text" name="<?php icep_the_profile_field_input_name() ?>" id="<?php icep_the_profile_field_input_name() ?>" value="<?php icep_the_profile_field_edit_value() ?>" />

                                    <?php endif; ?>

                                    <?php if ( 'textarea' == icep_get_the_profile_field_type() ) : ?>

                                        <label for="<?php icep_the_profile_field_input_name() ?>"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php //_e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
                                        <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                        <textarea rows="5" cols="40" name="<?php icep_the_profile_field_input_name() ?>" id="<?php icep_the_profile_field_input_name() ?>"><?php icep_the_profile_field_edit_value() ?></textarea>

                                    <?php endif; ?>

                                    <?php if ( 'selectbox' == icep_get_the_profile_field_type() ) : ?>

                                        <label for="<?php icep_the_profile_field_input_name() ?>"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php //_e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
                                        <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                        <select name="<?php icep_the_profile_field_input_name() ?>" id="<?php icep_the_profile_field_input_name() ?>">
                                            <?php icep_the_profile_field_options() ?>
                                        </select>

                                    <?php endif; ?>

                                    <?php if ( 'multiselectbox' == icep_get_the_profile_field_type() ) : ?>

                                        <label for="<?php icep_the_profile_field_input_name() ?>"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php //_e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
                                        <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                        <select name="<?php icep_the_profile_field_input_name() ?>" id="<?php icep_the_profile_field_input_name() ?>" multiple="multiple">
                                            <?php icep_the_profile_field_options() ?>
                                        </select>

                                    <?php endif; ?>

                                    <?php if ( 'radio' == icep_get_the_profile_field_type() ) : ?>

                                        <div class="register-radio">
                                            <div class="register-label"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php //_e( '(required)', 'buddypress' ) ?>
                                                <?php endif; ?></div>

                                            <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                            <?php icep_the_profile_field_options() ?>

                                            <?php if ( !icep_get_the_profile_field_is_required() ) : ?>
                                                <!--<a class="clear-value" href="javascript:clear( '<?php //icep_the_profile_field_input_name() ?>' );"><?php _e//( 'Clear', 'buddypress' ) ?></a>-->
                                            <?php endif; ?>
                                        </div>

                                    <?php endif; ?>

                                    <?php if ( 'checkbox' == icep_get_the_profile_field_type() ) : ?>

                                        <div class="register-checkbox">
                                            <span class="label"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?><?php endif; ?></span>

                                            <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>
                                            <?php icep_the_profile_field_options() ?>
                                        </div>

                                    <?php endif; ?>

                                    <?php if ( 'datebox' == icep_get_the_profile_field_type() ) : ?>

                                        <div class="datebox">
                                            <label for="<?php icep_the_profile_field_input_name() ?>_day"><?php icep_the_profile_field_name() ?> <?php if ( icep_get_the_profile_field_is_required() ) : ?>
                                                    <span class="helper-text"><?php _e( '(Required)', 'buddypress' ) ?></span><?php endif; ?></label>
                                            <?php do_action( 'icep_' . icep_get_the_profile_field_input_name() . '_errors' ) ?>

                                            <select name="<?php icep_the_profile_field_input_name() ?>_month" id="<?php icep_the_profile_field_input_name() ?>_month">
                                                <?php icep_the_profile_field_options( 'type=month' ) ?>
                                            </select>

                                            <select name="<?php icep_the_profile_field_input_name() ?>_day" id="<?php icep_the_profile_field_input_name() ?>_day">
                                                <?php icep_the_profile_field_options( 'type=day' ) ?>
                                            </select>

                                            <select name="<?php icep_the_profile_field_input_name() ?>_year" id="<?php icep_the_profile_field_input_name() ?>_year">
                                                <?php icep_the_profile_field_options( 'type=year' ) ?>
                                            </select>
                                        </div>

                                    <?php endif; ?>

                                    <?php do_action( 'icep_custom_profile_edit_fields' ) ?>

                                    <p class="description"><?php icep_the_profile_field_description() ?></p>

                                </div>

                            <?php endwhile; ?>


                            <input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php icep_the_profile_group_field_ids() ?>" />

                        <?php endwhile; endif; endif; ?>

                        <?php
                        $accept_tos_checked = isset($_POST['accept_tos']) ? 'checked="checked"' : '';
                        $send_newsletter_checked  = isset($_POST['send_newsletter']) ? 'checked="checked"' : '';
                        if(!isset($_POST['_wpnonce'])) $send_newsletter_checked = 'checked="checked"'; // set default state
                        ?>
                        <?php do_action( 'icep_accept_tos_errors' ) ?>

                        <div class="checkbox">
                            <span class="label"> <input type="checkbox" name="accept_tos" id="accept_tos" value="agreed" <?= $accept_tos_checked ?> />I agree to the Celebuzz <a href="<?=apply_filters('get-celebuzz-url', '/', '/');?>terms-of-service" target="_blank">Terms Of Service and Privacy Policy</a></span>
                            <span class="label"><input type="checkbox" name="send_newsletter" id="send_newsletter" value="Y" <?= $send_newsletter_checked ?> />Include me on the Celebuzz mailing list</span>
                        </div>

                    </div><!-- #profile-details-section -->
                <?php endif; ?>

                <?php do_action( 'icep_before_registration_submit_buttons' ) ?>
                <div class="register-submit-wrap">
                    <div class="submit">
                        <script type="text/javascript">
                            var user_registering = false;
                            jQuery(document).ready(function() {
                                jQuery('#signup_submit').click(function(e){
                                    if (!user_registering){
                                        user_registering = true;
                                        jQuery('#signup_submit').attr('value','Registering...');
                                    }
                                    else{
                                        e.preventDefault();
                                        return false;
                                    }
                                });
                            });

                        </script>
                        <input type="submit"name="signup_submit" id="signup_submit" value="<?php _e( 'JOIN CELEBUZZ', 'buddypress' ) ?>" />
                    </div>
                </div>

                <?php do_action( 'icep_after_registration_submit_buttons' ) ?>

                <?php wp_nonce_field( 'icep_new_signup' ) ?>

            <?php endif; // request-details signup step ?>

            <?php if ( 'completed-confirmation' == icep_get_current_signup_step() ) : ?>

                <h2><?php _e( 'Sign Up Complete.', 'buddypress' ) ?></h2>

                <?php do_action( 'template_notices' ) ?>

                <?php if ( icep_registration_needs_activation() ) : ?>
                    <p class="registration-complete"><?php _e( 'You have successfully created your account!<br/>
                                To begin using your account you will need to click the activation link in the email we just sent you. Please allow up to 5 minutes for this email to arrive.
                                ', 'buddypress' ) ?></p>
                <?php else : ?>
                    <p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
                <?php endif; ?>
                    <span class="its_green"><a href="<?=htmlspecialchars(site_url());?>">Return to <?=get_bloginfo('name');?></a><span>


            <?php endif; // completed-confirmation signup step ?>

            <?php do_action( 'icep_custom_signup_steps' ) ?>
            <div style="clear:both;"></div>
            </form>
            </div>

            <?php do_action( 'icep_after_register_page' ) ?>
            <?php do_action( 'icep_after_directory_activity_content' ) ?>

            <script type="text/javascript">
                jQuery(document).ready( function() {
                    if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
                        jQuery('div#blog-details').toggle();

                    jQuery( 'input#signup_with_blog' ).click( function() {
                        jQuery('div#blog-details').fadeOut().toggle();
                    });
                });
            </script>
        </div>
    </div>
</div>
</div>
<?php get_footer() ?>

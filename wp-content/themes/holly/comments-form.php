<?php get_currentuserinfo(); ?>
<div id="respond" class="comments-form">
	<div class="comments-form-inner">
		<h3 id="reply" class="comments-form-header"><?php comment_form_title('LEAVE A COMMENT', 'Reply to %s', true); ?></h3>
		<?php if (get_option('comment_registration') && (!is_object($current_user) || !isset($current_user->ID) || empty($current_user->ID))): ?>
			<p class="alert"><?php sprintf('You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', wp_login_url(get_permalink())); ?></p>
		<?php else: ?>
			<form action="<?php home_url('/wp-comments-post.php') ?>" method="POST" id="commentform" class="standard-form">
				<div class="current-user-info blue">
					<?php if (!is_user_logged_in()): ?>
						<div class="alternative-login">
							<?php do_action('alternative-login'); ?>
							<div class="facebook-connect">Use Facebook:
								<div class="facebook-connect-box"><?php do_shortcode("[bmfbc_login login_text='Login' logout_text='Logout']"); ?></div>
							</div>
						</div>
						<script type="text/javascript">
							function check_author(val) {if(val.toLowerCase().indexOf('celebuzz')!=-1){alert('That username is not allowed.'); jQuery('#author').val(''); return false;}}
						</script>
					<?php endif; ?>

					<div class="avatar-box-30">
						<?php global $current_user, $fb_uid; ?>
						<?php $generic_avatar = apply_filters('ice-comments-avatar-30-url', get_bloginfo('stylesheet_directory').'/images/guest-user-avatar-30.png'); ?>
						<?php if (is_user_logged_in()): ?>
							<a href="<? apply_filters('user-profile-link', '', $current_user->ID) ?>"><?php get_avatar($current_user->ID, 30, $generic_avatar); ?></a>
						<?php else: ?>
							<?php get_avatar('', 30, $generic_avatar); ?>
						<?php endif; ?>
					</div>

					<?php if (is_user_logged_in()): ?>
						<div class="sign-in-status log-in-out">
							<?php
								$ud = get_userdata($current_user->ID);
								$display_name = (!empty($ud->display_name) ? $ud->display_name : $ud->user_login);
								//$signout_link = apply_filters('logout_url', home_url('/sign-out/'));
								$signout_link = wp_logout_url();
							?>
							Signed in as <a href="<? apply_filters('user-profile-link', '', $current_user->ID) ?>"><?php $display_name ?></a>.
							<a class="sign-out log-in-out-link" href="<?php $signout_link ?>">Log out</a>
							<?php cancel_comment_reply_link('Cancel Reply') ?>
						</div>
					<?php else: ?>
						<div class="sign-in-status log-in-out-guest log-in-out">
							Signed in as Guest.
							<a rel="nofollow" class="sign-in" href="javascript:void(0);" title="Sign-in to your account.">Sign in</a>
							or <?//= //preg_replace('#>\s*Register\s*<#i', '>Join<', wp_register('', '', false)) ?>
							<?php
							global $blog_id;
							$from = 'http'.(empty($_SERVER['HTTPS']) ? '' : 's').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
							$arr = array( 'blog_id' => "$blog_id",
							'referer' => "$from");

							//$val = urlencode(serialize($arr));
							$val = base64_encode(serialize($arr));
							//echo base64_decode($val);
							$parm = "_its_signup_blog=$val";
							//echo $parm;

							/*
							// set cookie in the event user decides to signup to comment
							global $blog_id;
							//$_COOKIE['_its_signup_blog'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ":$blog_id";
							$val = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ":$blog_id";
							setcookie('_its_signup_blog', "$val", time()+30*86400, '/', $_SERVER['HTTP_HOST']);
							*/
							?>
							<a href="<?phpbloginfo('siteurl')?>/../../register/?<?php $parm?>">Join</a>
							.
						</div>
					<?php endif; ?>

					<div class="clear"></div>
				</div>

				<div class="textarea-wrapper">
					<textarea name="comment" id="comment" cols="40" rows="10" tabindex="4"></textarea>
				</div>
				<?php do_action('comment_form', $post->ID); ?>

				<div class="actions-row">
					<?php if (is_user_logged_in()): ?>
						<div class="form-submit"><input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="Submit" /></div>
						<div class="upload-actions">
							<div class="form-upload photo"><a href="javascript:void(0);" class="toggle-comment-image-upload-box its-icon its-photo"><span></span>Add a Photo</a></div>
							<div class="form-upload video"><a href="javascript:void(0);" class="toggle-comment-url-box its-icon its-embed"><span></span>Embed a Video</a></div>
						</div>
					<?php else: ?>
						<table>
							<thead>
								<tr>
									<th><label><?php _e('Name (Visible)', 'buddypress'); ?></label></th>
									<th><label><?php _e('Email (Required, Not Visible)', 'buddypress'); ?></label></th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<input type="text" class="text-input" name="author" id="author" value="<?php $comment_author; ?>" size="40" tabindex="1"
											onblur="javascript:check_author(this.value);"/>
									</td>
									<td>
										<input type="text" class="text-input" name="email" id="email" value="<?php $comment_author_email; ?>" size="40" tabindex="2" />
									</td>
									<td>
										<input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'buddypress'); ?>" />
									</td>
								</tr>
							</tbody>
						</table>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				<?php comment_id_fields(); ?>
				<?php if (is_user_logged_in() && function_exists('ecu_upload_form')): ?>
					<script type="text/javascript" language="javascript">
						if (typeof(jQuery) == 'function') {
							(function($){
								$('.toggle-comment-image-upload-box').die('click.togimgup').live('click.togimgup', function() {
									$(this).closest('.comments-form').find('#comment-upload-form').slideToggle(300);
								});
							})(jQuery);
						}
					</script>
					<div id="comment-upload-form"><? ecu_upload_form('','','Select File')?></div>
				<?php endif; ?>
			</form>
		<?php endif; ?>
	</div>
</div>

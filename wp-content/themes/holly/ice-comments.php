<?php
    $avas = apply_filters('ice-comments-get-setting', false, 'ice_comment_avatar');
    $photo_comments = apply_filters('ice-comments-get-setting', false, 'ice_comment_pull_child');
    $ava50 = apply_filters('ice-comments-avatar-50-url', get_bloginfo('stylesheet_directory').'/images/default-user-avatar-50.png');
    //echo "This comments template is: " . __FILE__ . "<br />\n";
    //echo "to customize ice comments, cp " . __FILE__ . " to your theme directory/comments.php<br />\n";
        if ( post_password_required() ) :
            echo '<h3 class="comments-header">' . __('Password Protected', 'buddypress') . '</h3>';
            echo '<p class="alert password-protected">' . __('Enter the password to view comments.', 'buddypress') . '</p>';
            return;
        endif;

        if ( is_page() && !have_comments() && !comments_open() && !pings_open() )
            return;
    
    if ($photo_comments) {
        $all_comments = get_multipost_comments(array(
            'post_id' => cb_post_id_with_main_embed_id_and_children_ids($post->ID),
            'order' => 'ASC',
            //'after' => $post->post_date_gmt
        ));
    }
    if (!$photo_comments || count($all_comments)):
    ?>
        
        <div id="comments">
                <div class="commentHead">
                    <?php comments_popup_link(__(' Leave a Comment', 'buddypress'), __('1 Comment', 'buddypress' ), __( '% Comments', 'buddypress' ) );?><?php //edit_comment_link(); ?>
                    <div class="dotBorder"></div>
                    <div class="cleaner"></div>  
                </div>
                
            <?php do_action( 'bp_before_blog_comment_list' ) ?>

            <?php $cperpage = (($cperpage = get_option('comments_per_page')) ? $cperpage : 50);  ?>
            <ol class="commentlist-ice<?php if (!$avas) echo ' noavas';?>">
                <?php
                    if ($photo_comments):
                        wp_list_comments(
                            array(
                                'callback' => 'cb_dtheme_blog_comments',
                                'per_page' => $cperpage
                            ),
                            $all_comments
                        );
                    else:
                        wp_list_comments(
                            array(
                                'callback' => 'cb_dtheme_blog_comments',
                                'per_page' => $cperpage
                            )
                        );
                    endif;
                ?>
            </ol><!-- .comment-list -->

            <?php do_action( 'bp_after_blog_comment_list' ) ?>

			<div class="comments-pagenavi">
            <?php
            if ($photo_comments):
                $cnt = 0;
                foreach ($all_comments as $c) if ($c->comment_parent == 0) $cnt++;
                if($cnt > $cperpage): ?>
                    <div class="navigation">
                        <div class="wp-pagenavi"><?php 
                            paginate_comments_links(array(
                                'total' => ceil($cnt/$cperpage),
                                //'prev_text' => '<span class="nav-wrap-prev"><img src="/wp-content/themes/superficial/images/transparent.png"/></span>', 
                                //'next_text' => '<span class="nav-wrap-next"><img src="/wp-content/themes/superficial/images/transparent.png"/></span>'
                                'prev_text' => '&laquo; Back', 
                                'next_text' => 'Next &raquo;'
                            ));
                        ?></div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
				<?php paginate_comments_links(array(
					'prev_text' => '&laquo; Back', 
					'next_text' => 'Next &raquo;'
				)); ?>
            <?php endif; ?>
            </div>
        </div><!-- #comments -->

    <?php else : ?>

        <?php if ( pings_open() && !comments_open() && is_single() ) : ?>

            <p class="comments-closed pings-open">
                <?php printf( __('Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', 'buddypress'), trackback_url( '0' ) ); ?>
            </p>

        <?php elseif ( !comments_open() && is_single() ) : ?>

            <p class="comments-closed">
                <?php _e('Comments are closed.', 'buddypress'); ?>
            </p>

        <?php endif; ?>

    <?php endif; ?>

        <?php if ( comments_open() ) : ?>

        <div id="respond">
          
            <div class="comment-content">
                <div id="reply" class="comments-header"><h3>
                    <?php comment_form_title( apply_filters('comment_form_title', __( 'Leave a Comment', 'buddypress' )) , __( 'Reply to %s', 'buddypress' ), true ); ?>
                </h3></div>
                <div class="dot-box"></div>
                <div style="clear:both;height:0px;float:none;"></div>
                
                <?php
                    global $current_user, $fb_uid;
                    get_currentuserinfo();
                    $userdata = get_userdata($current_user->ID);
                   
                ?>
                <?php if ($avas): ?>
                    <div class="comment-avatar-box">
                        <div class="avb">
                            <?php if ( is_user_logged_in() ) : ?>
                                <span class="avatar-bounds-30">
                                    <?php echo get_avatar( $current_user->ID, 30, $ava50); ?>
                                </span>                                                                    
                            <?php else : ?>
                                
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( get_option( 'comment_registration' ) && !$current_user->ID ) : ?>

                    <p class="alert">
                        <?php printf( __('You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', 'buddypress'), wp_login_url( get_permalink() ) ); ?>
                    </p>

                <?php else : ?>

                    <?php do_action( 'bp_before_blog_comment_form' ) ?>
                    <form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform" class="standard-form">

                        <?php if ($current_user->ID): ?>
                            <?php $display_name=($userdata->display_name!="")?$userdata->display_name:$userdata->user_login; ?>
                            <p class="log-in-out">                                        
                                <?php 
                                if( is_front_page() ) {
                                    $redir_link= get_option('home'); 
                                } elseif(is_category() || is_tag()) {
                                    $redir_link= 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 
                                } elseif(is_single()){
                                    $redir_link = get_permalink();
                                }else {
                                    $redir_link = get_option('home');
                                }
                                $signout_link = wp_logout_url($redir_link);
                                ?>
                                Signed in as a <a href="<?php do_action('comment-author-permalink', $userdata->user_nicename); ?>/"><?php echo $display_name; ?></a>.
                                <a class="log-in-out-link" href="<?= $signout_link ?>">Sign Out</a>
                                <?php cancel_comment_reply_link( __( ' Cancel Reply.', 'buddypress' ) ); ?>
                            </p>
                                    
                        <?php else : ?>
                            <script type="text/javascript">
                            <!--
                            function check_author(val) {
                                var loval = val.toLowerCase();
                                if (loval.indexOf('celebuzz')!=-1){
                                    alert('That username is not allowed.');
                                    jQuery('#author').val('');
                                    return false;
                                }
                            }
                            -->
                            </script>
                            <p class="log-in-out-guest">
                                <?= __('Commenting as a Guest. ', 'buddypress'); ?>
																<?php
																	$join_link = '#';
																	$rlink = wp_register('', '', false);
																	$url = preg_replace('#^.*(?:src|href)="([^\"]+)".*$#s', '\1', $rlink);
																	if ($rlink != $url) $join_link = $url;
																?>
                                <?php echo apply_filters('pi-login-get-sign-in', 'Sign in') ;?> or <?php echo apply_filters('pi-login-get-sign-up', 'Join') ;?>.
                                    
                                <div class="facebook-connect"><span>Use Facebook:</span>
                                    <div class="facebook-connect-box">
										<?php do_action('render_fb_login_button') ?>
                                    </div>
                                </div>
                            </p>
                        <?php endif; ?>
                        <p class="form-textarea">
                            <label for="comment"><?php _e('', 'buddypress'); ?></label>
														<?php $cerrors = apply_filters('get-commenting-errors', array()); ?>
														<?php if (count($cerrors) > 0): ?>
															<script type="text/javascript">
																(function($) {
																	$('.comment-reply-link').live('click', function(e) {
																		$('.commenting-errors').hide();
																	});
																	$('#cancel-comment-reply-link').live('click', function(e) {
																		$('.commenting-errors').show();
																	});
																})(jQuery);
															</script>
															<div class="commenting-errors errors">
																<ul class="error-list">
																	<?php foreach ($cerrors as $cerror): ?>
																		<li class="error-item"><?= $cerror ?></li>
																	<?php endforeach; ?>
																</ul>
															</div>
														<?php endif; ?>
                            <textarea name="comment" id="comment" cols="40" rows="10" tabindex="4"><?=apply_filters('comment_form_content', '');?></textarea>
														<div class="comment-helper">
															<?php global $allowedtags; ?>
															You may use the following HTML tags inside the text of your comment: <br/>
															<code><?= htmlspecialchars('<'.implode('> <', array_keys($allowedtags)).'>') ?></code>
														</div>
                        </p>

                        <?php do_action( 'bp_blog_comment_form' ) ?>

                        <div class="comment-action">
                            <?php do_action( 'comment_form', $post->ID ); ?>
                        </div>

                        <script type="text/javascript" language="javascript">
                            if (typeof(jQuery) == 'function') {
                                (function($){
                                    //alert($.fn.jquery); //jq version
                                    $('.toggle-comment-image-upload-box').die('click.togimgup').live('click.togimgup', function() {
                                        $(this).parents('.comment-content').find('#upload_form_wrapper').slideToggle(300);
                                    });
                                })(jQuery);
                            } else {
                                alert('jquery is not a function');
                            }
                        </script>

                        <div class="comment-action-row">
                            <?php if (is_user_logged_in()): ?>
                                <div class="form-submit"><input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'buddypress'); ?>" /></div>
                                <?php do_action('ice-comments-actions-row'); ?>
                            <?php else: ?>
                                <?php /* i really really really despise table formatting, and the use of tables in general. but alas, tis the only way to 
                                       * accomplish the crazy formatting that was requested */ ?>
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
                                                <input type="text" class="text-input" name="author" id="author" value="<?= $comment_author; ?>" size="40" tabindex="1"
                                                    onblur="javascript:check_author(this.value);"/>
                                            </td>
                                            <td>
                                                <input type="text" class="text-input" name="email" id="email" value="<?= $comment_author_email; ?>" size="40" tabindex="2" />
                                            </td>
                                            <td>
                                                <input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'buddypress'); ?>" />
                                                <?php comment_id_fields(); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                            <div class="cleaner"></div>
                        </div>
                        <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['SCRIPT_URL']?>">
<?php comment_id_fields(); ?>
                    </form>
                    <?php do_action( 'bp_after_blog_comment_form' ) ?>
                <?php endif; ?>
                <?php if (function_exists('ecu_upload_form')): ?>
                    <?php if (is_user_logged_in()): ?>
                        <div id="upload_form_wrapper">
                            <? ecu_upload_form('','','Select File')?>
                            <div class="upload-help">
                            <?=_e('JPG, PNG, GIF Only. For animated GIFs, paste image link in text box', 'buddypress');?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div><!-- .comment-content -->
            
        </div><!-- #respond -->

        <?php endif; ?>

        <?php if ( $numTrackBacks ) : ?>
            <div id="trackbacks">

                <span class="title"><?php the_title() ?></span>

                <?php if ( 1 == $numTrackBacks ) : ?>
                    <h3><?php printf( __( '%d Trackback', 'buddypress' ), $numTrackBacks ) ?></h3>
                <?php else : ?>
                    <h3><?php printf( __( '%d Trackbacks', 'buddypress' ), $numTrackBacks ) ?></h3>
                <?php endif; ?>

                <ul id="trackbacklist">
                    <?php foreach ( (array)$comments as $comment ) : ?>

                        <?php if ( get_comment_type() != 'comment' ) : ?>
                            <li><h5><?php comment_author_link() ?></h5><em>on <?php comment_date() ?></em></li>
                          <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

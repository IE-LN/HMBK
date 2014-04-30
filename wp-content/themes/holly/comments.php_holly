<?php
	if ( post_password_required() ) :
		echo '<h3 class="comments-header">' . __('Password Protected', 'buddypress') . '</h3>';
		echo '<p class="alert password-protected">' . __('Enter the password to view comments.', 'buddypress') . '</p>';
		return;
	endif;

	if ( is_page() && !have_comments() && !comments_open() && !pings_open() )
		return;
		
	$all_comments = get_multipost_comments(array(
		'post_id' => cb_post_id_with_main_embed_id_and_children_ids($post->ID),
		'order' => 'ASC',
		'after' => $post->post_date_gmt
	));
?>
<?php if (count($all_comments)): ?>
	<div id="comments">
		<div class="commentHead">
			<?php comments_popup_link('Leave a Comment<span></span>', '1 Comment<span></span>', '% Comments<span></span>', 'entry-comment-link its-icon its-quote'); ?>
			<div class="commentHead-icon"></div>
			<div class="dotBorder"></div>
			<div class="cleaner"></div>  
		</div>

		<?php do_action('its-before-comment-list') ?>
		<?php do_action('before-comment-list') ?>
		<ol class="commentlist">
			<?php
				$cperpage = (($cperpage = get_option('comments_per_page')) ? $cperpage : 50);
				wp_list_comments(
					array(
						'callback' => 'cb_dtheme_blog_comments',
						'per_page' => $cperpage
					),
					$all_comments
				);
			?>
		</ol><!-- .comment-list -->
		<?php do_action('after-comment-list') ?>
		<?php do_action('its-after-comment-list') ?>

		<?php $cnt = 0; ?>
		<?php foreach ($all_comments as $c) if ($c->comment_parent == 0) $cnt++; ?>
		<?php if($cnt > $cperpage): ?>
			<div class="navigation">
				<div class="wp-pagenavi"><?php 
					paginate_comments_links(array(
						'total' => ceil($cnt/$cperpage),
						'prev_text' => '<span class="nav-wrap-prev"><img src="/wp-content/themes/celebbuzzv05/images/transparent.png"/></span>', 
						'next_text' => '<span class="nav-wrap-next"><img src="/wp-content/themes/celebbuzzv05/images/transparent.png"/></span>'
					));
				?></div>
			</div>
		<?php endif; ?>
	</div><!-- #comments -->
<?php else: ?>
	<?php if (pings_open() && !comments_open() && is_single()): ?>
		<p class="comments-closed pings-open">
			<?php printf('Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', trackback_url('0')); ?>
		</p>
	<?php elseif (!comments_open() && is_single()): ?>
		<p class="comments-closed">
			<?php _e('Comments are closed.', 'buddypress'); ?>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<?php get_template_part('comments', 'form') ?>
<?php endif; ?>

<?php if ($numTrackBacks): ?>
	<div id="trackbacks">
		<span class="title"><?php the_title() ?></span>
		<?php if (1 == $numTrackBacks): ?>
			<h3><?php sprintf('%d Trackback', $numTrackBacks) ?></h3>
		<?php else: ?>
			<h3><?php sprintf('%d Trackbacks', $numTrackBacks ) ?></h3>
		<?php endif; ?>
		<ul id="trackbacklist">
			<?php foreach ((array)$comments as $comment): ?>
				<?php if (get_comment_type() != 'comment'): ?>
					<li><h5><?php comment_author_link() ?></h5><em>on <?php comment_date() ?></em></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

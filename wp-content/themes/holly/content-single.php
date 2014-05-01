<?php do_action('its-before-single-post') ?>
<article id="post-<?= get_the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title blackmaroon">
			<a href="<?php the_permalink(); ?>" title="<?= esc_attr(sprintf('Permalink to %s', get_the_title())); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>

		<div class="entry-meta">
			<span class="post-date-label"><?= sprintf('<span class="%s">%s</span>', 'post-date', get_post_time()) ?></span>
			/ <span class="comments-link"><?=
				comments_popup_link('Leave a Comment<span></span>', '1 Comment<span></span>', '% Comments<span></span>', 'entry-comment-link its-icon its-quote');
			?></span>
			<span class="post-cat-author">
				<span class="post-categories"><?= the_category(',') ?></span>
				/ <span class="post-author">By <?= get_the_author() ?></span>
			</span>
			<?php edit_post_link(__('Edit', 'bm-its-core'), '<span class="edit-link blue">', '</span>'); ?>
			<ul class="post-share-buttons menu">
				<li class="share-button"><?= its_fb_like_button(); ?></li>
				<li class="share-button"><?= its_twitter_share_button(); ?></li>
			</ul>
		</div>
	</header>

	<div class="entry-content">
		<?php the_content('Continue reading <span class="meta-nav">&rarr;</span>'); ?>
		<?php wp_link_pages(array('before' => '<div class="page-link"><span>Pages:</span>', 'after' => '</div>' ) ); ?>
	</div>

	<?php do_action('its-article-before-footer'); ?>

	<footer class="entry-meta-footer">
		<div class="entry-meta-footer-inner">
			<div class="add-this-wrap blue"><?= do_action('add-this-buttons', 'single', false) ?></div>

			<div class="entry-meta-extra-wrap">
				<div class="entry-tags"><?= the_tags('More on: ', ', ') ?></div>
			</div>
			<div class="clear"></div>
		</div>
	</footer>
</article><!-- #post-<?php the_ID(); ?> -->
<?php do_action('its-after-single-post') ?>

<?php
/**
Template Name: Bio
*/

get_header(); ?>
	<div id="content-column">
		<div id="content" role="main">
			<?php if (have_posts()): ?>
				<?php apply_filters('bm-its-content-nav', '', 'nav-above') ?>
				
				<?php while(have_posts()): the_post(); ?>
					<?php do_action('its-before-post') ?>
					<article id="post-<?php get_the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header header">
						<div class="content">
							<h2 class="entry-title blackmaroon">
								<a href="<?php the_permalink(); ?>" title="<?php esc_attr(sprintf('Permalink to %s', get_the_title())); ?>" rel="bookmark"><?php the_title(); ?> </a>
								<ul class="post-share-buttons menu">
									<li class="share-button edit-link-wrapper"><?php edit_post_link(__('Edit', 'bm-its-core'), '<span class="edit-link blue">', '</span>'); ?></li>
									<li class="share-button"><?php its_fb_like_button(); ?></li>
									<li class="share-button"><?php its_twitter_share_button(); ?></li>
								</ul>
							</h2>
						</div>
						</header>

						<div class="entry-content">
							<?php the_content('Continue reading <span class="meta-nav">&rarr;</span>'); ?>
							<?php wp_link_pages(array('before' => '<div class="page-link"><span>Pages:</span>', 'after' => '</div>' ) ); ?>
						</div>

						<?php do_action('its-article-before-footer'); ?>

						<footer class="entry-meta-footer">
							<div class="entry-meta-footer-inner">
								<div class="add-this-wrap blue"><?php do_action('add-this-buttons', 'home') ?></div>

								<div class="entry-meta-extra-wrap">
									<div class="entry-tags"><?php the_tags('More on: ', ', ') ?></div>
								</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</footer>
					</article><!-- #post-<?php the_ID(); ?> -->
					<?php do_action('its-after-post') ?>
				<?php endwhile; ?>
				<?php apply_filters('bm-its-content-nav', '', 'nav-below') ?>
			<?php else: ?>
				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'bm-its-core' ); ?></h1>
					</header>
					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'bm-its-core' ); ?></p>
						<?php get_search_form(); ?>
					</div>
				</article>
			<?php endif; ?>
		</div>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>

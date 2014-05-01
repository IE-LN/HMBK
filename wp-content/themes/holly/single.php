<?php
/**
 * @package BM ITS
 * @subpackage BM_ITS_Base
 */

get_header(); ?>
	<div id="content-column">
		<div id="content" role="main">
			<?php if (have_posts()): ?>
				<?= apply_filters('bm-its-content-nav', '', 'nav-above') ?>
				<?php while(have_posts()): the_post(); ?>
					<?php get_template_part('content-single', get_post_type()); ?>
				<?php endwhile; ?>
				<?= apply_filters('bm-its-content-nav', '', 'nav-below') ?>
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
		<?php if(function_exists('buzzworthy')) { 
			buzzworthy(); 
			} else {
				echo "\n<!-- buzzworthy -->\n";
			}?>
	</div>
<?php get_sidebar(); ?>
	<div id="comment-column">
		<div id="comment-column-inner" class="fb-state-changeable" fb-action="get-comments" fb-params="p=<?= get_the_ID() ?>">
			<?php do_action('its-before-comments'); ?>
			<?php comments_template('', true); ?>
			<?php do_action('its-after-comments'); ?>
		</div>
	</div>
<?php get_footer(); ?>

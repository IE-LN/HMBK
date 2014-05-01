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
				<?php $post_cnt = 0; ?>
				
				<?php while(have_posts()): the_post(); ?>
					<?php get_template_part('content', get_post_format()); ?>
					<?php $post_cnt++ ?>
					<?php do_action('its-story-well-after-story-'.$post_cnt); ?>
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
		<?php $pagination_out = its_pagination(array('greyed' => true, 'type' => 'list')); ?>
		<div class="pagination-bottom"><?= $pagination_out ?></div>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>

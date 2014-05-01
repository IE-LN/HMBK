<?php
global $ice_img, $ifunc, $size, $post, $wp_query;
?>
<div class="results-section-list-wrap">
	<ul class="results-section-list">
		<?php if (have_posts()): ?>
			<?php while (have_posts()): the_post(); ?>
				<li class="results-list-item">
					<article id="post-<?= $post->ID ?>" <?php post_class('news-item'); ?>>
						<header>
							<a class="news-image-link" href="<?= get_permalink($post->ID) ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht80x60"><?=
								$ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'gallery-image' : false)
							?></span></a>
							<div class="news-result-info">
								<div class="news-categories entry-categories grey"><?= the_category(', ') ?></div>
								<h2 class="gallery-title blackmaroon"><a href="<?= get_permalink() ?>" title="View <?= esc_attr(get_the_title()) ?>"><? the_title() ?></a></h2>
								<div class="post-meta">
									<span class="blue bold comment-count"><?php
										comments_popup_link(' Leave a Comment &raquo;', '1 Comment &raquo;', '% Comments &raquo;');
									?></span>
									<span class="post-date"><?= get_the_date() ?></span>
								</div>
							</div>
							<div class="clear"></div>
						</header>
					</article><!-- end post-<?= $post->ID ?> -->
				</li>
			<?php endwhile; ?>
		<?php endif; ?>
	</ul>
</div>

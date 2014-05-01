<?php
global $ice_img, $ifunc, $size, $post, $wp_query, $raw_results, $results, $per_row;
?>
<div class="results-section-list-wrap grid three-column">
	<?php $_post = $post; ?>
	<?php foreach ($results as $ind => $post): ?>
		<?php setup_postdata($post); ?>
		<?php if ($ind % $per_row == 0): ?><div class="grid-row"><?php endif; ?>
		<div class="grid-item column gallery-item">
			<div class="grid-item-inner">
				<article id="post-<?= $post->ID ?>" <?php post_class(); ?>>
					<header>
						<a href="<?= get_permalink($post->ID) ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht180x135"><?=
							$ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'gallery-image' : false)
						?></span></a>
						<div class="gallery-categories entry-categories grey"><?= the_category(', ') ?></div>
						<h2 class="gallery-title blackmaroon"><a href="<?= get_permalink() ?>" title="View <?= esc_attr(get_the_title()) ?>"><? the_title() ?></a></h2>
						<div class="gallery-photos-count bold">
							<a href="<?= get_permalink($post->ID) ?>" title="View <?= esc_attr(get_the_title()) ?>">
							Watch it &raquo;
							<?// = sprintf('%d video &raquo;', apply_filters('ice-gallery-image-count', 0, $post->ID)) ?></a>
						</div>
					</header>
				</article><!-- end post-<?= $post->ID ?> -->
			</div>
		</div>
		<?php if ($ind % $per_row == $per_row-1): ?></div><?php endif; ?>
	<?php endforeach; ?>
	<?php if ($ind % $per_row != $per_row-1): ?>
		<?php while ($ind % $per_row != $per_row-1): ?>
			<div class="grid-item column gallery-item">
				<div class="grid-item-inner">
					<article id="post-0-<?= $ind ?>" <?php post_class(); ?>>
						<header>
							<span class="key-hole kht180x135 fake-pic"></span>
							<div class="gallery-categories entry-categories grey">&nbsp;</div>
							<h2 class="gallery-title blackmaroon">&nbsp;</h2>
							<div class="gallery-photos-count bold">&nbsp;</div>
						</header>
					</article><!-- end post-0-<?= $ind ?> -->
				</div>
			</div>
			<?php $ind++; ?>
		<?php endwhile; ?>
		</div>
	<?php endif; ?>
	<?php $post = $_post; ?>
	<?php setup_postdata($post); ?>
	<div class="clear"></div>
</div>

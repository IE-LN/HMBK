<?php
global $ice_img, $ifunc, $size, $post, $wp_query, $raw_results, $results, $per_row, $tag_term;
?>
<div class="results-section-list-wrap">
	<div class="six-column-580 grid">
		<?php $_post = $post; ?>
		<?php foreach ($results as $ind => $post): ?>
			<?php setup_postdata($post); ?>
			<?php if ($ind % $per_row == 0): ?><div class="grid-row"><?php endif; ?>
			<div class="grid-item column photo-item">
				<div class="grid-item-inner">
					<article id="post-<?= $post->ID ?>" <?php post_class(); ?>>
						<header>
							<?php $add_url = '?page='.($ind+1).'&t='.$post->term_match; ?>
							<a href="<?= get_permalink($post->ID) ?><?= $add_url ?>"  title="View <?= esc_attr(get_the_title()) ?>"><span class="key-hole kht94x94"><?=
								$ifunc(apply_filters('ice-get-thumbnail-id', 0, $post->ID), $size, $ice_img ? 'gallery-image' : false)
							?></span></a>
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
								<span class="key-hole kht94x94 fake-pic"></span>
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
	<div class="clear"></div>
</div>

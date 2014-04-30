<?php $before_widget ?>
<div class="bmsbw-container bmsbw-more-photos">
	<?php $before_title ?><h3 class="bmsbw-title">MORE PHOTOS</h3><?php $after_title ?>
	<div class="bmsbw-inside blackmaroon">
		<ul class="bmsbw-post-list">
			<?php $count = 0; ?>
			<?php foreach ($posts as $post): ?>
				<?php setup_postdata($post); ?>
				<?php if (isset($post->thumb) && !empty($post->thumb)): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb post-<?php get_the_ID() ?>">
						<a href="<?php get_permalink($post->ID) ?>" title="Read More"
								class="bmsbw-image-keyhole bmsbw-80x60-keyhole"><?php cb_get_attachment_image($post->thumb->ID, array(80, 60)) ?></a>
						<h2><a href="<?php get_permalink() ?>" title="Read More"><?php the_title() ?></a></h2>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only post-<?php get_the_ID() ?>">
						<h2><a href="<?php get_permalink() ?>" title="Read More"><?php the_title() ?></a></h2>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php $after_widget ?>


<?php $before_widget ?>
<div class="bmsbw-container bmsbw-most-recent-filtered bmsbw-most-recent-filtered-sxs <?php $instance['css_container_class'] ?>">
	<?php $before_title ?><span class="bmsbw-title"><?php $instance['title'] ?></span><?php $after_title ?>
	<div class="bmsbw-inside blackmaroon">
		<ul class="bmsbw-post-list">
			<?php $count = 0; ?>
			<?php foreach ($posts as $post): ?>
				<?php if (isset($post->thumb) && !empty($post->thumb)): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb">
						<table>
							<tbody>
								<tr>
									<td class="bmsbw-valign-fix-image"><a href="<?php get_permalink($post->ID) ?>" title="Read More"
										class="bmsbw-image-keyhole bmsbw-40x30-keyhole"><?php ice_get_attachment_image($post->thumb->ID, array(40, 30)) ?></a></td>
									<td class="bmsbw-valign-fix-title">
										<h2><a href="<?php get_permalink($post->ID) ?>" title="<?php $instance['read_more_title'] ?>"><?php
												 apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only">
						<h2><a href="<?php get_permalink($post->ID) ?>" title="<?php $instance['read_more_title'] ?>"><?php apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<div class="clear"></div>
	</div>
    <div class="bmsbw-bottom blue">
			<a class="see-more" href="<?php$more_link?>" title="<?php $instance['more_stories_title'] ?>"><?php $instance['more_stories_title'] ?> &raquo;</a>
			<div class="clear"></div>
		</div>
</div>
<?php $after_widget ?>

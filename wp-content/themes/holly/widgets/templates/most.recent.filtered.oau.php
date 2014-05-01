<?= $before_widget ?>
<div class="bmsbw-container bmsbw-most-recent-filtered bmsbw-most-recent-filtered-oau <?= $instance['css_container_class'] ?>">
	<?= $before_title ?><span class="bmsbw-title"><?= $instance['title'] ?></span><?= $after_title ?>
	<div class="bmsbw-inside blackmaroon">
		<ul class="bmsbw-post-list">
			<?php $count = 0; ?>
			<?php foreach ($posts as $post): ?>
				<?php if (isset($post->thumb) && !empty($post->thumb)): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb">
						<div class="item-inside">
							<table>
								<tbody>
									<tr>
										<td class="bmsbw-valign-fix-image"><a href="<?= get_permalink($post->ID) ?>" title="Read More" class="bmsbw-image-keyhole bmsbw-94x94-keyhole"><?= ice_get_attachment_image($post->thumb->ID, array(94, 94)) ?></a></td></tr>
										<tr><td class="bmsbw-valign-fix-title">
											<h2><a href="<?= get_permalink($post->ID) ?>" title="<?= $instance['read_more_title'] ?>"><?=
													 apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only">
						<div class="item-inside">
							<h2><a href="<?= get_permalink($post->ID) ?>" title="<?= $instance['read_more_title'] ?>"><?= apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
						</div>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<div class="clear"></div>
	</div>
    <div class="bmsbw-bottom blue">
			<div class="clear"></div>
			<a class="see-more" href="<?=$more_link?>" title="<?= $instance['more_stories_title'] ?>"><?= $instance['more_stories_title'] ?> &raquo;</a>
			<div class="clear"></div>
		</div>
</div>
<?= $after_widget ?>

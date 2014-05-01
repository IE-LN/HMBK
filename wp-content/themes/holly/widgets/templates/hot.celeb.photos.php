<?= $before_widget ?>
<div class="bmsbw-container bmsbw-hot-celeb-photos bmsbw-gutter-cover">
	<div class="bmsbw-gutter-cover-inner">
		<?= $before_title ?><h3 class="bmsbw-title">Hot Celeb Photos</h3><?= $after_title ?>
		<div class="bmsbw-inside blackmaroon">
			<?php $count = 0; ?>
			<?php $total = min(count($posts), (int)$instance['celebs_to_show_max']); ?>
			<?php $half = ceil($total/2); ?>
			<ul class="bmsbw-post-list bmsbw-column bmsbw-left">
				<?php foreach ($posts as $post): ?>
					<?php if ($count >= $total) continue; ?>
					<?php if ($count == $half): ?>
			</ul>
			<ul class="bmsbw-post-list bmsbw-column bmsbw-right">
					<?php endif; ?>
					<?php $post = (object)$post; ?>
					<?php if (isset($post->celebrity_id) && !empty($post->celebrity_id)):  ?>
						<li class="bmsbw-post-with-thumb">
							<table>
								<tbody>
									<tr>
										<td class="bmsbw-valign-fix-image"><a href="<?= trailingslashit(get_permalink($post->celebrity_id)) ?>photos/" title="Read More"
											class="bmsbw-image-keyhole bmsbw-30x30-keyhole f"><?= apply_filters('celeb-profile-pic', 'no-img', array(30, 30), $post->celebrity_id) ?></a></td>
										<td class="bmsbw-valign-fix-title">
											<h2>
												<span class="bmsbw-celeb-name"><?= apply_filters('the_title', $post->name, $post->celebrity_id) ?></span>
												<a class="bmsbw-more-link" href="<?= trailingslashit(get_permalink($post->celebrity_id)) ?>photos/" title="Find Photos"><?= number_format($post->count, 0, '.', ',') ?> Photos &raquo;</a>
											</h2>
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					<?php else: ?>
						<li class="bmsbw-post-title-only">
							<h2>
								<span class="bmsbw-celeb-name"><?= apply_filters('the_title', $post->name, $post->celebrity_id) ?></span>
								<a class="bmsbw-more-link" href="<?= trailingslashit(get_permalink($post->celebrity_id)) ?>photos/" title="Find Photos"><?= number_format($post->count, 0, '.', ',') ?> Photos &raquo;</a>
							</h2>
						</li>
					<?php endif; ?>
					<?php $count++ ?>
				<?php endforeach; ?>
			</ul>
			<div class="cleaner"></div>
		</div>
	</div>
</div>
<?= $after_widget ?>

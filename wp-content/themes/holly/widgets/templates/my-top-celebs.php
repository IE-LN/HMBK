<?php if (!$ajax): ?>
<?= $before_widget ?>
<div class="bmsbw-container bmsbw-my-top-celebs">
	<?= $before_title ?>
	<h3 class="bmsbw-title">
		<?php if (bp_is_my_profile()): ?>
			<span class="floatR"><a class="blue-link edit-top-celebs" href="#">Add Top Celebs</a></span>
		<?php endif; ?>
		<span><?= (bp_is_my_profile() ? 'My' : do_action('bp_add_displayed_user_display_name').'\'s') ?> Top Celebs</span>
	</h3>
	<?= $after_title ?>
	<div class="bmsbw-inside blackmaroon">
		<input type="hidden" name="_tn" value="<?= $ajax_key ?>" class="top-celebs-tn" />
		<div class="top-celeb-search-container hide">
			<input type="text" name="se" value="Search" class="pre-text-helper top-celeb-search" />
		</div>
		<div class="bmsbw-post-list-wrapper">
<?php endif; ?>
			<?php $count = 0; ?>
			<?php $total = min(count($posts), 10); ?>
			<?php $half = ceil($total/2); ?>
			<ul class="bmsbw-post-list bmsbw-column bmsbw-left">
				<?php foreach ($posts as $post): ?>
					<?php //echo "<pre>\n"; print_r($post); echo "</pre>\n"; ?>
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
										<td class="bmsbw-valign-fix-image"><a href="/celebrities/<?= $post->term_slug ?>" title="Read More" class="bmsbw-image-keyhole bmsbw-30x30-keyhole f"><?php
											$img = apply_filters('celeb-profile-pic', 'no-img', array(30, 30), $post->celebrity_id);
											if('no-img' == $img) {
												$img = '<img src="' . get_bloginfo('stylesheet_directory') . '/images/cb_avatar_30x30.jpg' . '">';
											}
											echo $img;
										?></a></td>
										<td class="bmsbw-valign-fix-title">
											<h2>
												<span class="bmsbw-celeb-name">
													<a href="/celebrities/<?= $post->term_slug ?>/" title="Read More" class=""><?= apply_filters('the_title', $post->name, $post->celebrity_id) ?></a>
												</span>
												<?php if (bp_is_my_profile()): ?>
													<span class="remove-celeb" cid="<?= $post->celebrity_id ?>">X</span>
												<?php endif; ?>
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
							</h2>
						</li>
					<?php endif; ?>
					<?php $count++ ?>
				<?php endforeach; ?>
			</ul>
<?php if (!$ajax): ?>
		</div>
		<div class="cleaner"></div>
	</div>
</div>
<?= $after_widget ?>
<?php endif; ?>

<?= $before_widget ?>
<div class="bmsbw-container bmsbw-hot-now bmsbw-cb-hot-now">
	<div class="hotNowHead-cb widget-title">
		<span class="hotNowHead-inner">
			<div class="cb-hot-now-logo"></div>
			<div class="cb-hot-now">ON</div>
			<div class="cb-hot-now-cb-logo"></div>
			<div class="clear"></div>
		</span>
	</div>
	<div class="bmsbw-inside">
		<ul class="bmsbw-post-list">
		<?php 
		if(is_array($posts)) :
			foreach ($posts as $ind => $post): 
				switch($ind) {
					case 0:
					$class = 'one';
					break;

					case 1:
					$class = 'two';
					break;

					case 2:
					$class = 'three';
					break;

					case 3:
					$class = 'four';
					break;

					case 4:
					$class = 'five';
					break;

					default:
					$class = '';
					break;
				}

				if (isset($post['thumb']) && !empty($post['thumb'])): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb">
						<table>
							<tbody>
								<tr>
									<td><div class="<?=$class?>"></div></td>
									<td class="bmsbw-valign-fix-image" width="1%"><a href="<?= $post->permalink ?>" title="Read More"
										class="left key-hole kht80x60"><?= $post['thumb']?></a></td>
									<td class="bmsbw-valign-fix-title">
										<h2 class="bmsbw-title-wrapper">
											<a href="<?= $post['permalink'] ?>" title="Read More"><?= $post['post_title']?></a>
										</h2>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only">
						<div class="<?=$class?>"></div>
						<h2 class="bmsbw-title-wrapper">
							<a href="<?= $post['permalink'] ?>" title="Read More"><?= $post->post_title?></a>
						</h2>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		</ul>
	</div>
		<div class="bmsbw-bottom blue hotIcon blackmaroon">
			<a class="see-more" href="<?= apply_filters('get-celebuzz-url', '', '/hot-now/'); ?>">SEE MORE &raquo;</a>
			<div class="clear"></div>
		</div>
</div>
<?= $after_widget ?>

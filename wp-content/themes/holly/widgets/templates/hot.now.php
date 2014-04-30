<?php $before_widget ?>
<div class="bmsbw-container bmsbw-hot-now">
	<div class="hotNowHead widget-title">
		<ul>
		  <li class="current"><a href="#">MOST POPULAR</a></li><li><a href="#">MOST COMMENTED</a></li></ul>
    </div>
	<div class="bmsbw-inside blackmaroon">
		<ul class="bmsbw-post-list">
			<?php $count = 0; ?>
			<?php $most = 0; ?>
			<?php 
			foreach ($posts as $ind => $post): 
				if ($ind == 0) $most = $post->visits;
				global $cbbm_hot_now;
				if (method_exists($cbbm_hot_now, 'get_graph_img'))
					$width = $cbbm_hot_now->get_graph_img($post->visits, $most, 182)+7;
				else
					$width = 189;
			?>
				<?php if (isset($post->thumbnail) && !empty($post->thumbnail)): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb">
						<table>
							<tbody>
								<tr>
									<td class="bmsbw-valign-fix-image" width="1%"><a href="<?php $post->resource ?>" title="Read More"
										class="bmsbw-image-keyhole bmsbw-80x60-keyhole"><?php cb_get_attachment_image($post->thumbnail_id, array(80, 60)) ?></a></td>
										</a></td>
									<td class="bmsbw-valign-fix-title">
										<h2 class="bmsbw-title-wrapper">
											<a href="<?php get_permalink($post->ID) ?>" title="Read More"><?php apply_filters('the_title', $post->title, $post->post_id) ?></a>
										</h2>
										<div class="hotness-bar sidebar_hot_meter" style="width:<?php $width ?>px;"></div>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only">
						<h2 class="bmsbw-title-wrapper">
							<a href="<?php get_permalink($post->post_id) ?>" title="Read More"><?php apply_filters('the_title', $post->title, $post->post_id) ?></a>
						</h2>
						<div class="hotness-bar sidebar_hot_meter" style="width:<?php $width ?>px;"></div>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
    <div class="bmsbw-bottom blue hotIcon ">
			<a href="<?php site_url('/hot-now/') ?>" class="see-more">See Top 20 &raquo;</a>
			<div class="clear"></div>
    </div>
</div>
<?php $after_widget ?>

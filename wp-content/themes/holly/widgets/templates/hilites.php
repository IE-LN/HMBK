<?php
/***********************************************************************************************
file: 		ice/templates/hilites.php
author:		toy - ripnread from most-recent
date:		08.20.11

REV HISTORY
[toy 08.20.11]		orig version 
************************************************************************************************/
?>
<?php $before_widget ?>
<div class="bmsbw-container bmsbw-hilites">
	<?php $before_title ?>
	<?php if (isset($more_link) && !empty($more_link)){ ?>
        <h3 class="bmsbw-title"><a href="<?php $more_link ?>"><?php $title ?></a></h3>
	<?php } else { ?>
        <h3 class="bmsbw-title"><?php $title ?></h3>
	<?php } ?>
	<?php $after_title ?>
	
    <div class="bmsbw-inside blackmaroon">
		<ul class="bmsbw-post-list">
			<?php $count = 0; ?>
			<?php foreach ($posts as $post){ ?>
				<?php if (isset($post->thumb) && !empty($post->thumb)): /** if we have a thumb to display */ ?>
					<li class="bmsbw-post-with-thumb">
						<table>
							<tbody>
								<tr>
									<td class="bmsbw-valign-fix-image"><h2><a href="<?php get_permalink($post->ID) ?>" title="Read More"
										class="bmsbw-image-keyhole bmsbw-40x30-keyhole"><?php cb_get_attachment_image($post->thumb, array(80,60)) ?><?php
											if (function_exists('is_partner_link') && is_partner_link($post->ID)): ?><div class="partner-link"></div><?php endif;
										?></a></h2></td>
									<td class="bmsbw-valign-fix-title">
										<?php if ((!isset($category)) || ($category == "")) { 
											$c = get_the_category($post->ID); 
											foreach($c as $c1) {
												if ($c1->slug != "highlights"){
													$cname = $c1->slug;
													break;
												}
											} ?>
											<a class="recent-posts-category" title="<?php echo $cname;  ?>" href="/category/<?php echo $cname;  ?>"><?php echo $cname;  ?></a>
										<?php } ?>
										<h2><a href="<?php get_permalink($post->ID) ?>" title="Read More"><?php apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
									</td>
								</tr>
							</tbody>
						</table>
					</li>
				<?php else: /** if we do not have a thumb to display */ ?>
					<li class="bmsbw-post-title-only">
						<?php if ((!isset($category)) || ($category == "")) { 
							$c = get_the_category($post->ID); 
							foreach($c as $c1) {
								if ($c1->slug != "highlights"){
									$cname = $c1->slug;
									break;
								}
							} ?>
							<a class="recent-posts-category" title="<?php echo $cname;  ?>" href="/category/<?php echo $cname;  ?>"><?php echo $cname;  ?></a>
						<?php } ?>
						<h2><a href="<?php get_permalink($post->ID) ?>" title="Read More"><?php apply_filters('the_title', $post->post_title, $post->ID) ?></a></h2>
					</li>
				<?php endif; ?>
			<?php } ?>
		</ul>
	</div>
	<div class="bmsbw-bottom"></div>
</div>
<?php $after_widget ?>

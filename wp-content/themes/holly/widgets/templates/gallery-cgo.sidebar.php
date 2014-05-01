<?php
$func = function_exists('ice_get_attachment_image') ? 'ice_get_attachment_image' : 'wp_get_attachment_image';
?>
<?= $before_widget ?>
<div class="bmsbw-container bmsbw-gallery-sidebar <?= $instance['css_container_class'] ?>">
	<div class="bmsbw-photoHead">
		<?php wp_nav_menu(array('theme_location' => 'hot-photos-widget-filters')); ?>
		<?= $before_title ?><span class="bmsbw-title"><?=$instance['widget_title']?></span><?= $after_title ?>
		<div class="clear"></div>
		<?php /*
		  <ul class="bmsbw-sub-nav-links menu right">
				<li><a href="/photos/hot-bodies/">HOT BODIES</a></li>
				<li><a href="/photos/sightings/">SIGHTINGS</a></li>
				<li><a href="/photos/">RECENT</a></li>
			</ul>
			<div class="clear"></div>
		*/ ?>
    </div>
	<div class="bmsbw-inside blackmaroon">
		<div class="bmsbw-inside-inner">
			<table>
				<tbody>
					<?php
						$per_row = 3;
						global $post;

						foreach ($posts as $ind => $post):
							setup_postdata($post);
							if ($ind != 0 && $ind % $per_row == 0):
								?></tr><?php
							endif;
							if ($ind != count($posts) - 1 && $ind % $per_row == 0):
								?><tr class="bmsbw-post-list"><?php
							endif;
							
							$img = $func($post->_thumb_id, array(94, 94));

							$perma = get_permalink();
					?>
						<td class="bmsbw-gallery-image-block">
							<?php if ($img): ?>
								<div class="bmsbw-valign-fix-image bmsbw-94x94-keyhole"><a href="<?= $perma ?>" title="Read More"
									class="bmsbw-image-keyhole"><?= $img ?></a></div>
							<?php endif; ?>
								<div class="bmsbw-title-fix"><h2><a href="<?= $perma ?>" title="Read More"><?= the_title() ?></a></h2></div>
						</td>
					<?php
						endforeach;
					?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="bmsbw-bottom blue">
		<a class="see-more" href="<?= home_url()?>/photos/">See All Photos &raquo;</a>
		<div class="clear"></div>
	</div>
</div>
<?= $after_widget ?>

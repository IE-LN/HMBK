<?php $buzzlist_style = ''; ?>
<div id="buzzbox" class="content_box" style="clear:both">
	<div id="buzzbox_title"><ul>
			<li id="buzzbox_comments" class="TAB_ON">MOST COMMENTED</li>
			<li id="buzzbox_views" >MOST VIEWED</li>
			<li id="buzzbox_photos">POPULAR PHOTOS</li>
			<li id="buzzbox_mp3s">MP3`s</li>
	</ul></div>
	<?php

	$postslist = array();
	$postslist['comments'] = self::get_mostt_commented_old('- 1 WEEK');
	$postslist['views'] = self::get_most_viewed_post();
	// $postslist['videos'] = self::get_most_viewed_category_post('Video');

	foreach($postslist as $list => $posts) { ?>
		<div id="buzzbox_<?php$list;?>_list" style="<?php$buzzlist_style; ?>">
		<?php $i=0;?>
		<?php foreach($posts as $post) {
			$post_url = get_permalink($post->ID);
			$post_title = _trim_string($post->post_title,70,'&nbsp;&#8230;');
			$img = '';
			$thumb_id = get_post_thumbnail_id($post->ID);
			if(!empty($thumb_id))
			{
				$img = function_exists('cb_get_attachment_image')
					? cb_get_attachment_image($thumb_id, array(50,50))
					: wp_get_attachment_image($thumb_id, array(50,50));
			}
			else {
			    $img = function_exists('cb_get_main_attachment')
					? cb_get_main_attachment($post->ID, array(50,50))
					: sg_getpost_imageURL($post->ID);
			}
			$i++;
			?>
			<div class="buzzbox_item">
			<table cellpadding="0" cellspacing="0" class="buzzbox"><tr valign="top">
			<td class="buzzbox_image"><div class="img_wrapp50x50">
				<a href="<?php$post_url;?>" title="<?phphtmlspecialchars($post->post_title);?>"><img src="<?php$img;?>"width="50" height="50" align="left" /></a>
			</div></td>
			<td class="buzzbox_rank">0<?php echo $i;?></td>
			<td class="buzzbox_title"><a href="<?php$post_url;?>" title="<?phphtmlspecialchars($post->post_title);?>" class="buzzbox_link"><?php$post_title;?></a>
			</td></tr></table></div>
			<?php } ?>
			<?php $buzzlist_style = 'display:none'; ?>
		</div>
	<?php } ?>
</div>

<?php $buzzlist_style = ''; ?>
<div id="buzzbox" class="content_box" style="clear:both">
	<div id="buzzbox_title"><ul>
    <li id="buzzbox_views" class="TAB_ON">MOST VIEWED</li>
	<li id="buzzbox_comments">MOST COMMENTED</li>
	</ul></div>
	<?php

        $category='spin_view_single_post'; 
        $createtionPeriod = '1 week'; 
        $calculationPeriod = '3 days';

        $ids = apply_filters( 'pi-ga-pageview-most-popular', $category, 'page_view', 50, $createtionPeriod, $calculationPeriod);
        
        $ga_posts = array();
        if(count($ids) > 0){
            $args = array(
              'post__in' => $ids,
              'post_status' => 'publish',
              'order' => ' ',
            );

            $tmpPosts = get_posts($args);
            foreach ($ids as $id) {
                foreach ($tmpPosts as $key => $tp) {
                    if(is_object($tp) && $tp->ID == $id && count($ga_posts)<5) {
                        $ga_posts[] = $tmpPosts[$key];
                    }
                }
            }
            unset($tmpPosts); 
        } 
 
	$postslist = array();
	$postslist['views'] = $ga_posts;
	$postslist['comments'] = self::get_mostt_commented_old('-1 WEEK');

	foreach($postslist as $list => $posts) { ?>
		<div id="buzzbox_<?=$list;?>_list" style="<?=$buzzlist_style;?>">
		<?php $i=0;?>
		<?php foreach($posts as $post) { ?>
			<?php
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
			    $img = cb_get_main_attachment($post->ID, array(50,50));
			}
			$i++;
			?>
			<div class="buzzbox_item">
			<table cellpadding="0" cellspacing="0" class="buzzbox"><tr valign="top">
			<td class="buzzbox_rank_<?=$i;?>"><?php echo $i;?></td>
			<td class="buzzbox_image"><div class="img_wrapp50x50">
				<a href="<?=$post_url;?>" title="<?=htmlspecialchars($post->post_title);?>"><?=$img;?></a>
			</div></td>
			<td class="buzzbox_title"><a href="<?=$post_url;?>" title="<?=htmlspecialchars($post->post_title);?>" class="buzzbox_link"><?=$post_title;?></a>
				<?php if ($post->comment_count == '0') { ?>
				<span class="buzzbox_comment_count"><a href="<?=$post_url;?>#comments">Leave a Comment</a></span>
				<?php } elseif ( isset( $comments_number ) && $comments_number == '1') { ?>
				<span class="buzzbox_comment_count"><a href="<?=$post_url;?>#comments"><?=$post->comment_count;?> Comment</a></span>
				<?php } else { ?>
				<span class="buzzbox_comment_count"><a href="<?=$post_url;?>#comments"><?=$post->comment_count;?> Comments</a></span>
				<?php } ?>
			</td></tr></table></div>
			<?php } ?>
			<?php $buzzlist_style = 'display:none'; ?>
		</div>
	<?php } ?>
</div>

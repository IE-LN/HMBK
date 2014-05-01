<?php
global $post, $fsenabled, $is_fullsize, $ice_img, $ifunc, $pfunc, $fspfunc, $tarsize, $carousel, $furl, $fwidth, $fheight, $stag, $allow_zoom,
	$gal_pos, $prev, $next, $prev_img_url, $next_img_url;
remove_filter('the_content', 'prepend_attachment');
function its_add_fullsize_to_permalink_for_comment_link($permalink, $post=null, $leavename=false) {
	if (is_fullsize()) {
		$parts = parse_url($permalink);
		$parts['path'] = trailingslashit(preg_replace('#/full-size/?$#', '/', $parts['path'])).'full-size/';
		$permalink = $parts['scheme'].'://'.$parts['host'].$parts['path']
			.(isset($parts['query']) ? '?'.$parts['query'] : '')
			.(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
	}
	return $permalink;
}
?>
<?php do_action('its-before-single-gallery-image', $is_fullsize) ?>
<div id="image-<?= $post->ID ?>" <?php post_class() ?>>
	<div class="gallery-inner-wrapper">
		<div class="gallery-actions">
			<div class="gallery-nav">
				<div class="gallery-nav-inner">
					<div class="next nav-item">
						<?php $xtra = isset($_GET['page'], $_GET['t']) ? '?page='.(absint($_GET['page'])+1).'&t='.$_GET['t'] : '' ?>
						<a <?= empty($next_img_url) ? 'class="disabled"' : 'href="'.$next_img_url.$xtra.'"' ?>
								title="Next Photo"><div class="arrow-wrap its-icon its-gallery-next"><span class="arrow"></span></div></a>
					</div>
					<div class="prev nav-item">
						<?php $xtra = isset($_GET['page'], $_GET['t']) ? '?page='.(absint($_GET['page'])-1).'&t='.$_GET['t'] : '' ?>
						<a <?= empty($prev_img_url) ? 'class="disabled"' : 'href="'.$prev_img_url.$xtra.'"' ?>
								title="Previous Photo"><div class="arrow-wrap its-icon its-gallery-prev"><span class="arrow"></span></div></a>
					</div>
				</div>
			</div>
			<div class="image-heading">
				<h3 class="image-title"><?= get_the_title() ?></h3>
				<div class="image-meta">
					<ul class="image-meta-list menu">
						<?php $xtra = isset($_GET['page'], $_GET['t']) ? '?page='.(absint($_GET['page'])).'&t='.$_GET['t'] : '' ?>
						<li><a class="back" href="<?= get_permalink().$xtra ?>">&laquo; Back to Photo</a></li>
						<li> / <span class="date"><?= get_the_time() ?></span> / </li>
						<li><?php
							add_action('post_link', 'its_add_fullsize_to_permalink_for_comment_link', 10, 3);
							add_action('attachment_link', 'its_add_fullsize_to_permalink_for_comment_link', 10, 2);
						?><?=
							comments_popup_link('Leave a Comment<span></span>', '1 Comment<span></span>', '% Comments<span></span>', 'entry-comment-link its-icon its-quote')
						?><?php
							remove_action('post_link', 'its_add_fullsize_to_permalink_for_comment_link');
							remove_action('attachment_link', 'its_add_fullsize_to_permalink_for_comment_link');
						?></li>
						<?php edit_post_link('Edit gallery', '<li>/</li><li class="blue bold edit-gallery-link">', '</li>', $post->post_parent) ?>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="gallery-image-frame clear">
			<div class="gallery-image-wrapper">
				<a <?= empty($next_img_url) ? 'class="disabled"' : 'href="'.$next_img_url.$xtra.'"' ?>><?=
					apply_filters('get-gallery-big-image', '', $post->ID, $tarsize, '', 'key-hole')
				?></a>
				<?php if (!empty($gal_pos)): ?><div class="gallery-position"><?= $gal_pos ?></div><?php endif; ?>
			</div>
		</div>

		<div class="clear"></div>
	</div>

	<div class="image-entry">
		<article id="post-<?= $post->ID ?>" class="image-entry-inner">
			<header class="image-header">
				<div class="right grey">
					<?= !empty($stag) ? $stag : '' ?>
					<a href="<?= $furl ?>" title="View Original Image">View Original</a>
				</div>
				<h3 class="image-title"><?= get_the_title() ?></h3>
			</header>

			<div class="image-content entry-content"><?= the_content() ?></div>

			<footer class="image-meta-footer">
				<div class="image-meta-footer-inner">
					<div class="add-this-image-wrap blue"><?= do_action('add-this-buttons', 'single-image') ?></div>
					<div class="share-buttons right">
						<ul class="share-buttons-list menu">
							<li class="share-button"><?= its_fb_like_button(); ?></li>
							<li class="share-button"><?= its_twitter_share_button(); ?></li>
						</ul>
					</div>

					<div class="image-meta-extra-wrap">
					<?php 
					$the_tags = get_the_tags();
					if(empty($the_tags)) $the_tags = get_the_tags($post->post_parent);
						if(!empty($the_tags)) : ?>
							<div class="entry-tags">
								<span class="related-to-label">Related to: </span>
								<?php 
								$i = 0; 
								$cnt = count($the_tags);
								foreach($the_tags as $tag) {
									$i++;
									echo '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a>';
									if($i < $cnt) echo ', ';
								}
								?>
							</div>
						<?php endif; ?>
					</div>
					<div class="clear"></div>
				</div>
			</footer>
		</article>
	</div>
</div>

<?php
//do_action('ice-gallery-render-related', $post->post_parent);
?>
<?php do_action('its-after-single-gallery-image', $is_fullsize) ?>

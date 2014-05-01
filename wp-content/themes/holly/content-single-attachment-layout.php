<?php
global $post, $fsenabled, $is_fullsize, $ice_img, $ifunc, $pfunc, $fspfunc, $tarsize, $carousel, $furl, $fwidth, $fheight, $stag, $allow_zoom,
	$gal_pos, $prev, $next, $prev_img_url, $next_img_url;
?>
<?php do_action('its-before-single-gallery-image', $is_fullsize) ?>
<div id="image-<?= get_the_ID() ?>" <?php post_class() ?>>
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
			<div class="features">
				<ul class="features-list menu grey">
					<li class="fullsize-feature"><a class="its-icon its-fullsize full-size-mode" href="<?= $fspfunc($post->ID) ?>"><span></span>View Full Size</a></li>
					<?php if ($allow_zoom): ?>
						<li class="zoom-in-feature">
							<a class="its-icon its-zoom-in activate-zoom zoom-in grey" ydim="<?= $fheight ?>" xdim="<?= $fwidth ?>" full-url="<?= $furl ?>"
									ztarget=".zoom-target" href="javascript:void(0);"><span></span>Zoom</a>
						</li>
						<li class="zoom-in-feature">
							<span class="helper">Hover on Photo to Zoom</span>
						</li>
					<?php endif; ?>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="gallery-image-frame clear">
			<div class="gallery-image-wrapper">
				<?php $xtra = isset($_GET['page'], $_GET['t']) ? '?page='.(absint($_GET['page'])+1).'&t='.$_GET['t'] : '' ?>
				<a <?= empty($next_img_url) ? 'class="disabled"' : 'href="'.$next_img_url.$xtra.'"' ?>><?=
					apply_filters('get-gallery-big-image', '', $post->ID, $tarsize, '', 'key-hole zoom-target')
				?></a>
				<?php if (!empty($gal_pos)): ?><div class="gallery-position"><?= $gal_pos ?></div><?php endif; ?>
			</div>
		</div>

		<?php if ($carousel): ?>
			<div id="slider-wrapper" class="gallery-slider clear">
				<?php $start_xtra = isset($_GET['page'], $_GET['t']) && !empty($_GET['page']) && !empty($_GET['t']) ? 'page="'.$_GET['page'].'" t="'.$_GET['t'].'"' : ''; ?>
				<ul id="gallery-slider-new" class="jcarousel-skin-tango-new" start="<?= $post->ID ?>" <?= $start_xtra ?>></ul>
				<div class="clear"></div>
			</div>
		<?php endif; ?>

		<div class="gallery-attribution clear">
			<div class="right grey">
				<?= !empty($stag) ? $stag : '' ?>
				<a href="<?= $furl ?>" title="View Original Image">View Original</a>
			</div>
			<div class="left blue bold">
				<?php edit_post_link('Edit gallery', '<div class="edit-gallery-link">', '</div>', $post->post_parent) ?>
			</div>
			<div class="clear"></div>
		</div>

		<div class="clear"></div>
	</div>
</div>

<?php do_action('ice-gallery-render-related', $post->post_parent); ?>

<?php do_action('its-after-single-gallery-image', $is_fullsize) ?>

<?php
global $post, $fsenabled, $is_fullsize, $ice_img, $ifunc, $pfunc, $fspfunc, $tarsize, $carousel, $furl, $fwidth, $fheight, $stag, $allow_zoom,
	$gal_pos, $prev, $next, $prev_img_url, $next_img_url, $use_sidebar, $extra_body_classes;

// add attachment page indicator to body
$extra_body_classes = is_array($extra_body_classes)
	? array_merge($extra_body_classes, array('is-attachment'))
	: (is_string($extra_body_classes) && strlen(trim($extra_body_classes)) > 0 ? array(trim($extra_body_classes), 'is-attachment') : array('is-attachment'));

// fullsize testing
$fsenabled = function_exists('is_fullsize');
$is_fullsize = $fsenabled && is_fullsize();

// set the sidebar to use as the single gallery sidebar. also account for fullsize
$use_sidebar = 'single'.($is_fullsize ? '-fullsize' : '').'-gallery-sidebar';

// image function vars
$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

// permalink functions with fullsize awareness
$pfunc = $fsenabled && $is_fullsize && function_exists('get_fullsize_permalink') ? 'get_fullsize_permalink' : 'get_permalink';
$fspfunc = $fsenabled && function_exists('get_fullsize_permalink') ? 'get_fullsize_permalink' : 'get_permalink';

// image size with fullsize awareness
$tarsize = $is_fullsize ? array(900, 675) : array(580, 435);

// get the full image url for the zoom function and for the view original link
list($furl, $fwidth, $fheight) = wp_get_attachment_image_src($post->ID, 'full');

// photo stream
if (isset($_GET['page'], $_GET['t']) && !empty($_GET['page']) && !empty($_GET['t'])):
	// depending on the page, load the appropriate prev, current, and next images
	if (absint($_GET['page']) > 1):
		$res = search_posts_with_tag($search_term, absint($_GET['page'])-2, 3, 'attachment', false, true);
		$prev = array_shift($res['results']);
		array_shift($res['results']);
		$next = array_shift($res['results']);
	else:
		$res = search_posts_with_tag($search_term, 1, 1, 'attachment', false, true);
		$next = array_shift($res['results']);
	endif;

	// setup the carousel and disable the related galleries
	$carousel = true;
	// load the carousel js
	do_action('ice-photo-stream-carousel');
// endless gallery experience
else :
	// gallery image index indicator (1 of 10)
	$gal_pos = apply_filters('get-gallery-image-pos', '', $post->ID);

	// whether to enable th carousel of thumbs or not
	$carousel = apply_filters('show-gallery-thumbs', true, $post->post_parent);
	// load the js in the footer
	if ($carousel) do_action('ice-endless-carousel');

	// get relative gallery ids
	$prev = apply_filters('get-relative-gallery-image', (object)array('ID' => $post->ID), $post->ID, -1);
	$next = apply_filters('get-relative-gallery-image', (object)array('ID' => $post->ID), $post->ID, 1);
endif;

// calc urls for next and prev btns
$next_img_url = is_object($next) && isset($next->ID) ? $pfunc($next->ID) : '';
$prev_img_url = is_object($prev) && isset($prev->ID) ? $pfunc($prev->ID) : '';

// create an image source tag if applicable
$source = apply_filters('ice-image-source', '');
$stag = '';
if (is_array($source) && !empty($source['name'])) {
	if (empty($source['url']))
		$stag = sprintf('<span class="%s">%s</span>', 'image-credit', $source['name']);
	else
		$stag = sprintf('<a href="%1$s" title="%3$s"><span class="%2$s">%3$s</span></a>', $source['url'], 'image-credit', $source['name']);
	$stag .= ' / ';
}

get_template_part('content-single-attachment-layout', $is_fullsize ? 'fullsize' : '');

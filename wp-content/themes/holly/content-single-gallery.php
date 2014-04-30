<?php
global $old_post, $old_query, $post, $wp_query;
$gallery_id = get_the_ID();
$old_query = clone $wp_query;
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

$args = array(
	'post_type' => 'attachment',
	'post_status' => array('publish', 'inherit'),
	'post_parent' => $gallery_id,
	'posts_per_page' => 1,
	'orderby' => 'menu_order',
	'order' => 'desc',
);

query_posts($args);

if (have_posts()): while(have_posts()): the_post();
	get_template_part('content-single', get_post_type());
endwhile; endif;

$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;
setup_postdata($post);

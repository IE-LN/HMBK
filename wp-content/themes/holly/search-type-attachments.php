<?php
global $wp_query, $post, $search_term, $tag_term;
//var_dump($tag, $tag_term);
if (isset($tag_term) && !isset($_GET['t']) && !empty($tag_term)) {
	$_GET['t'] = $tag_term;
	$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
} elseif (isset($search_term) && !isset($_GET['t']) && !empty($search_term) && function_exists('get_search_matched_tag_slugs')) {
	$slugs = get_search_matched_tag_slugs($search_term);
	if (is_array($slugs) && !empty($slugs)) {
		$_GET['t'] = array_shift($slugs);
		$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
	}
}

search_posts_with_tag($search_term, 0, 1, 'attachment', true, true);
if (isset($GLOBALS['wp_query'], $GLOBALS['wp_query']->post, $GLOBALS['wp_query']->post->ID))
	query_posts(array('attachment_id' => $GLOBALS['wp_query']->post->ID));
ice_get_template_part('single', 'attachment', true);

<?php
/*
Template Name: Global Search Results Page
*/
global $post, $wp_query, $total_found_posts, $search_term, $tag_term, $hide_pagenavi;

//echo "// How may I redirect your search?";
$search_type = get_query_var('search_type');

//$pat = '/[^a-z \-\_0-9]+/i';
$pat = '/[^\w \',"_-]+/';

$search_term = urldecode(get_query_var('s')); // gets $_GET val-- kinda, but better

$search_term = preg_replace($pat, '', $search_term); //sanitizes $search_term
//var_dump($search_term);

// search page can be gotten to by tag urls too, so search_term might be empty. If $search_term is empty and $tag isn't, set $searc_term = $tag
if ( empty( $search_term ) && !empty( $tag ) ) $search_term = $tag;

$tag_term = get_term_by('name', $search_term, 'post_tag');

if (is_object($tag_term) && !is_wp_error($tag_term) && isset($tag_term->slug)) {
	$tag_term = $tag_term->slug;
	
	// Check if there's a celebrity profile page that matches this term directly and redirect the user there
	$cq = get_posts(array(
		'name' => $tag_term,
		'post_type' => 'celebrity',
		'posts_per_page' => 1,
	));
	if(count($cq) == 1) die(header('Location: '.get_permalink(array_shift($cq))));
	
	//die(header("Location: " . home_url() . "/tag/$tag_term")); //endless redirect
} else {
	$tag_term = '';
}

// this switch statement is going to include on of templ-photos.php, search-type-video-post.php, search-type-all.php
switch ($search_type) {
	case 'gallery': ice_get_template_part('templ-photos', '', true); break;
	case 'video-post': ice_get_template_part('search-type', 'video-post', true); break;
	case 'news': ice_get_template_part('search-type', 'news', true); break;
	case 'photo_stream': ice_get_template_part('search-type', 'attachments', true); break;
	case 'all':
	default: ice_get_template_part('search-type', apply_filters('ice-search-sub-template', 'all', $search_type), true); break;
}

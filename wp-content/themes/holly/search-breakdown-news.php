<?php
global $ice_img, $ifunc, $size, $total_found_posts, $search_term, $tag_term, $post, $wp_query;

$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

// image function vars
$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

$per_page = 5;

// run the search
if(!empty($tag_term)) {

	// get items if page is called via post_tag
	$results = search_posts_with_tag($tag_term, 0, 6, 'post');
	$found_posts = $results['found_posts'];
} else {
	// get items if page is called via search
	$args = array(
		'post_type' => 'post',
		's' => $search_term,
		'posts_per_page' => $per_page,
		'orderby' => 'date',
		'order' => 'desc',
	);
	$results = query_posts($args);

	global $wp_query;
	$found_posts = $wp_query->found_posts;
}
$total_found_posts += $found_posts;

$size = array(80, 60);
?>
<?php if ($found_posts > 0): ?>
	<div class="search-results-section results-section news-results-section">
		<h3 class="results-section-heading"><?= sprintf('%s (%d)', __('News'), $found_posts) ?></h3>
		<?php ice_get_template_part('search-results', 'news', true) ?>
		<div class="search-results-section-bottom">
			<div class="results-more right more">
				<a href="<?= home_url() . '/search/news/' . $search_term ?>">see all news &raquo;</a>
			</div>
		</div>
		<div class="cleaner"></div>
	</div>
<?php endif; ?>
<?php
// restore the original query and post
$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;

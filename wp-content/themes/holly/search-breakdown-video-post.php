<?php
global $ice_img, $ifunc, $size, $total_found_posts, $search_term, $tag_term, $post, $wp_query, $raw_results, $results, $per_row;

$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

// image function vars
$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

$raw_results = search_posts_with_tag($search_term, 0, 6, 'video-post',  $set_the_loop = false, $enable_cache = true);

$found_posts = $raw_results['found_posts'];
$results = $raw_results['results'];

$per_row = 3;
$total_found_posts += $found_posts;
$size = array(180, 135);
?>
<?php if ($found_posts > 0): ?>
	<div class="search-results-section results-section galleries-results-section">
		<h3 class="results-section-heading"><?php sprintf('%s (%d)', __('Videos'), $found_posts) ?></h3>
		<?php ice_get_template_part('search-results', 'video-post', true) ?>
		<div class="search-results-section-bottom">
			<div class="results-more right more">
				<a href="<?php home_url() . '/search/videos/' . urlencode($search_term) ?>">see all videos &raquo;</a>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php endif; ?>
<?php
// restore the original query and post
$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;

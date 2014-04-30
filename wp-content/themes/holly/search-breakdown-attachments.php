<?php
global $ice_img, $ifunc, $size, $total_found_posts, $search_term, $tag_term, $post, $wp_query, $raw_results, $results, $per_row;
$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

// image function vars
$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

// run the search
$raw_results = search_posts_with_tag($search_term, 0, 12, 'attachment',  $set_the_loop = false, $enable_cache = true);

$found_posts = $raw_results['found_posts'];
$results = $raw_results['results'];

$per_row = 6;
$total_found_posts += $found_posts;
$size = array(94, 94);
?>
<?php if ($found_posts > 0): ?>
	<div class="search-results-section results-section photo-results-section">
		<h3 class="results-section-heading"><?php sprintf('%s (%d)', __('Photos'), $found_posts) ?></h3>
		<?php ice_get_template_part('search-results', 'attachments', true) ?>
		<div class="search-results-section-bottom">
			<div class="results-more right more">
				<a href="<?php home_url() . '/search/photos/' . $search_term ?>">see all photos &raquo;</a>
			</div>
		</div>
		<div class="cleaner"></div>
	</div>
<?php endif; ?>
<?php
// restore the original query and post
$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;

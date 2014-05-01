<?php
global $ice_img, $ifunc, $size, $total_found_posts, $search_term, $tag_term, $post, $wp_query;

// store orig query and post for later restoration, so that we can manipulate it for the content of the page
$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

get_header();

// image function vars
$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

$page = absint(get_query_var('paged'));
$per_page = 20;

// run the search
$args = array(
	'post_type' => 'post',
	's' => $search_term,
	'posts_per_page' => $per_page,
	'orderby' => 'date',
	'order' => 'desc',
);
if (!empty($page)) $args['paged'] = $page;
$results = query_posts($args);

global $wp_query;
$found_posts = $wp_query->found_posts;
$total_found_posts += $found_posts;
$size = array(80, 60);
?>
<div id="content-column">
	<div id="content" role="main">
		<?php do_action( 'bp_before_blog_single_post' ) ?>
		<h1 class="search-results-header">Search results for: <span class="search-results-term"><?= $search_term ?></span></h1>
		<div class="search-search-results-breakdown search-results-breakdown">
			<?php if ($found_posts > 0): ?>
				<div class="search-results-section results-section news-results-section">
					<h3 class="results-section-heading"><?= sprintf('%s (%d)', __('News'), $found_posts) ?>
							<a class="right blue back-link" href="<?= home_url().'/search/'.$search_term ?>">Back to All Results &raquo;</h3>
					<?php ice_get_template_part('search-results', 'news', true) ?>
					<div class="search-results-section-bottom"></div>
					<div class="cleaner"></div>
				</div>
			<?php endif; ?>
		</div>
		<?php if ($total_found_posts == 0 ) { ?>
			<div class="no-results">
				<p class="bold">Your search did not find any results.</p>
				<p>Please try again.</p>
			</div>
		<? } ?>
		<?php do_action( 'bp_after_blog_single_post' ) ?>
	</div><!-- #content -->
</div>
<?php
// before starting any sidebar or footer, we need to restore the original query just in case a widget relies on it being the page
$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;

// call the sidebar with the restored query and post
get_sidebar();
get_footer();

<?php
/*
Template Name: Category
*/

global $post, $wp_query, $cat, $page;
$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;
if (!empty($post) && $post->post_type == 'page') {
	// store orig query and post for later restoration, so that we can manipulate it for the content of the page

	// figure out the cateogry if applicable
	//$cat = apply_filters('its-get-page-category', is_object($cat) && isset($cat->slug) ? $cat : null, $post->ID);
	$post_name = $post->post_name;
	$cat = get_category_by_slug($post_name);
	$term_link = rtrim(get_bloginfo('url','/')) . '/' . $cat->slug . '/';
	if($post_name === 'health') {
		$post_name = 'wellness';
		$cat = get_category_by_slug($post_name);
		$term_link = rtrim(get_bloginfo('url','/')) . '/health/';
	}


	global $current_ad_zone;
	$current_ad_zone = is_object($cat) && isset($cat->slug) ? $cat->slug : null;
	$page = isset($wp_query->query_vars['paged']) && intval($wp_query->query_vars['paged']) > 1 ? intval($wp_query->query_vars['paged']) : 0;
	$per_page = get_option('posts_per_page');

	// image function vars
	$ice_img = function_exists('ice_get_attachment_image');
	$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';


	$args = array(
		'post_status' => 'publish',
		'post_type' => 'post',
		'posts_per_page' => $per_page,
		'paged' => $page,
		'category_name' => '',
	);
	if (is_object($cat) && !is_wp_error($cat) && isset($cat->slug)) {
		$args['category_name'] = $cat->slug;
	}


	query_posts($args);
} else {
	$args['category_name'] = $wp_query->query_vars['category_name'];
	$term_link = get_term_link($cat, 'category');
}
get_header();
?>
	<div id="content-column">
		<div id="content" role="main">
			<?php if (have_posts() && !empty($args['category_name'])): ?>
				<?php apply_filters('bm-its-content-nav-category-page', '', 'nav-above') ?>
				<div class="category-page-heading">
					<?php /*
					<div class="more-cats-container">
						<ul class="menu more-cats-menu">
							<li>
								<a class="its-icon its-dropdown more-cats-label" href="">More Categories<span></span></a>
								<div class="sub-menu more-cats-list-container">
									<ul class="more-cats-list blackgreen">
										<?php foreach (get_terms('category') as $term): ?>
											<?php $url = get_term_link($term) ?>
											<li><a href="<?php $url ?>"><?php $term->name ?></a></li>
										<?php endforeach; ?>
									</ul>
								</div>
							</li>
						</ul>
					</div>
					*/ ?>
					<h1 class="category-name"><?php $cat->name ?></h1>
				</div>
				<?php $post_cnt = 0; ?>
				<?php while(have_posts()): the_post(); ?>
					<?php get_template_part('content', get_post_format()); ?>
					<?php $post_cnt++ ?>
					<?php do_action('its-story-well-after-story-'.$post_cnt); ?>
				<?php endwhile; ?>
				<?php apply_filters('bm-its-content-nav-caetgory-page', '', 'nav-below') ?>
			<?php else: ?>
				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'bm-its-core' ); ?></h1>
					</header>
					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'bm-its-core' ); ?></p>
						<?php get_search_form(); ?>
					</div>
				</article>
			<?php endif; ?>
		</div>
		<?php
			$pagination_out = '';
			if (!empty($term_link) && !is_wp_error($term_link) && is_string($term_link))
				$pagination_out = its_pagination(array('base' => $term_link.'%_%', 'greyed' => true, 'type' => 'list'));
		?>
		<div class="pagination-bottom"><?php $pagination_out ?></div>

	</div>

<?php
// before starting any sidebar or footer, we need to restore the original query just in case a widget relies on it being the page
$GLOBALS['wp_query'] = $old_query;
$GLOBALS['post'] = $old_post;

// call the sidebar with the restored query and post
get_sidebar();
?>
<?php get_footer(); ?>

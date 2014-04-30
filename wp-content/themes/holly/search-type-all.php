<?php
/*
Template Name: Search All Types
*/
global $post, $wp_query, $total_found_posts, $search_term, $tag_term, $hide_pagenavi;

// store orig query and post for later restoration, so that we can manipulate it for the content of the page
$old_query = clone $GLOBALS['wp_query'];
$old_post = is_object($GLOBALS['post']) ? clone $GLOBALS['post'] : null;

get_header();

$total_found_posts = 0;
?>
<div id="content-column">
	<div id="content" role="main">
		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<?php //if(strpos($_SERVER['REQUEST_URI'], 'post_tag')) { ?>
		<?php if(isset($tag) && !empty($tag)) { ?>
		<h1 class="search-results-header">Items tagged: <span class="search-results-term"><?php $search_term ?></span></h1>
		<?php } else { ?>
		<h1 class="search-results-header">Search results for: <span class="search-results-term"><?php $search_term ?></span></h1>
		<?php } ?>
		<div class="search-search-results-breakdown search-results-breakdown">
			<?php 
			// no topics or videos for ITS. boo.
			//ice_get_template_part('search-breakdown', 'topics', true); 
			//ice_get_template_part('search-breakdown', 'video-post', true) 
			?>

			<?php 
			// function returns concatenation of first part, second part, so something like: search-breakdown-attamchents.php
			ice_get_template_part('search-breakdown', 'attachments', true) ?>
			<?php ice_get_template_part('search-breakdown', 'galleries', true) ?>
			<?php ice_get_template_part('search-breakdown', 'news', true) ?>
		</div>
		<?php if ($total_found_posts == 0 ) {
			// if no posts, lets find all posts 
			include 'search-get-all-news.php';
		} ?>
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

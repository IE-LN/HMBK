<?php
global $ice_img, $ifunc, $post, $wp_query, $total_found_posts, $search_term, $tag_term, $hide_pagenavi;

$ice_img = function_exists('ice_get_attachment_image');
$ifunc = $ice_img ? 'ice_get_attachment_image' : 'wp_get_attachment_image';

$old_query = is_object($GLOBALS['wp_query']) ? clone $GLOBALS['wp_query'] : null;
$old_post = is_object($GLOBALS['post']) ?  clone $GLOBALS['post'] : null;

$search_is_main = apply_filters('ice-search-is-main', false);//is main search page
$search_other = apply_filters('ice-search-get-more', array());//other serch blocks
$search_first = apply_filters('ice-search-get-first', array());//current serch block
$hide_pagenavi = $search_is_main ;
if ($search_first['is_tag']) {
    $tag_title = single_tag_title("",false);
}

if (!have_posts()):
    get_template_part('templates/ice-search-error', apply_filters('ice-search-get-current-page', ''), true);
else:
    get_header();
    ?>
<div id="content-column">
    <div id="content" role="main">
        <?php do_action( 'bp_before_blog_single_post' ) ?>
        <h1 class="search-results-header"><?php sprintf(
            '%s <span class="search-results-term">%s</span>',
            $search_first['is_tag'] ? 'Items tagged' : 'Search results for:',
            $search_first['is_tag'] ? $tag_title : apply_filters('ice-search-fix-search-request', $search_first['value'])
        ); ?></h1>
        <div class="search-search-results-breakdown search-results-breakdown">
            <?php
            do_action('ice-search-render-results', $search_first);
            if (!empty($search_other)) {
                $cachekey = $search_first['is_tag'] ? 'tagpage:'.$search_first['value'] : 'searchpage:'.md5($search_first['value']);
                html_cache($cachekey, function () {
                    $search_other = apply_filters('ice-search-get-more', array());
                    foreach ($search_other as $block) {
                        do_action('ice-search-get-results', $block);
                        do_action('ice-search-render-results', $block);
                    }
                }, 15*60);
            }
            ?>
        </div>
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
endif;


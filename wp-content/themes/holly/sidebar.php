<?php
global $use_sidebar;
// preserve post and query for after sidbar is run
if (is_object($GLOBALS['wp_query']))
	$old_query = clone $GLOBALS['wp_query'];
if (is_object($GLOBALS['post']))
	$old_post = clone $GLOBALS['post'];

if (empty($use_sidebar)) {
	switch (true) {
		case is_singular(array('post', 'video-post')):
			$use_sidebar = 'single-post-page-sidebar';
		break;

		case is_tag():
		case is_search():
			$use_sidebar = 'tag-search-pages-sidebar';
		break;


		case is_home():
		case is_archive():
		case is_category():
		case isset($cat):
			$use_sidebar = 'home-archive-category-sidebar';
		break;

		default:
			$use_sidebar = apply_filters('its-determine-page-sidebar', 'generic-sidebar');
		break;
	}
}
?>
<?php if($use_sidebar!='none') { ?>
<div id="sidebar-column" role="complementary">
	<div id="sidebar" class="widget-area">
		<?php if (!dynamic_sidebar($use_sidebar)): ?>
			<?php if (!dynamic_sidebar('generic-sidebar')): ?>
				<aside id="archives" class="widget">
					<h3 class="widget-title">Archives</h3>
					<ul>
						<?php wp_get_archives(array('type' => 'monthly')); ?>
					</ul>
				</aside>

				<aside id="meta" class="widget">
					<h3 class="widget-title">Meta</h3>
					<ul>
						<?php wp_register(); ?>
						<li><?php wp_loginout(); ?></li>
						<?php wp_meta(); ?>
					</ul>
				</aside>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<?php } ?>
<?php
// restore post and query from before sidebar
if (is_object($old_query))
	$GLOBALS['wp_query'] = $old_query;
if (is_object($old_post))
	$GLOBALS['post'] = $old_post;
?>

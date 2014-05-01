<?php
global $wp_query, $is_video_search;
get_header();

$search_info = apply_filters('ice-search-get-first', array());//current serch block
$args = $search_info['request'];
if (!have_posts()) $search_info['error'] .= ' No results were found.';
?>
<?php global $hide_pagenavi; ?>
<?php $hide_pagenavi = true; ?>
<div id="content-column">
	<div id="content" role="main">
		<h1 class="search-results-header">Search results for: <span class="search-results-term"><?=htmlspecialchars($search_info['value']);?></span></h1>
		<div class="search-search-results-breakdown search-results-breakdown">
			<div class="search-error"><?=$search_info['error'];?></div>
		</div>
	</div><!-- #content -->
</div>
<script>
function ice_er_search_submit()
{
	<?php
	$o = apply_filters('ice-search-get-settings', false);
	$args = $search_info['request'];
	$args['search_value'] = '%s';
	?>
	<?php if($o){ ?>
	var rule='<?= apply_filters('ice-search-link', 'search/%s', $args);?>';
	var ws='<?=$o['word_separator'];?>';
	<?php }else{ ?>
	var rule='<?php bloginfo('url'); ?>/search/%s';
	var ws='%20';
	<?php } ?>
	var v = document.getElementById('se').value.replace(/ /g, ws);
	var url = rule.replace('%s', v);
	window.location = url;
	return false;
}
</script>

<?php
global $need_sidebar;
$need_sidebar = '404-page';
?>
<?php
get_sidebar();
get_footer();

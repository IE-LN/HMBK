<?php
/**
 * @package Buzzmedia_Ads
 * @version 0.0.1 Celebuzz
 * @author John Terenzio
 */

// global vars that we want to keep across ad calls
$ad_tile = 1;
if (!isset($zone)) {
	$ad_zone = FALSE; // caching mechanism
}
if (!isset($ad_category)) {
	$ad_category = FALSE;
}

function buzzmediaads_admin_init() {
	global $wp_settings_fields;

	$wp_settings_fields['general']['default'] = is_array($wp_settings_fields['general']['default']) ? $wp_settings_fields['general']['default'] : array();

	$wp_settings_fields['general']['default'][] = array(
		'title' => 'Site Token (topscript token)',
		'args' => array(
			'label_for' => 'ads-site-token',
			'option_name' => 'ads-site-token',
			'option_type' => 'text-box',
		),
		'callback' => 'buzzmediaads_draw_option',
	);

	$wp_settings_fields['general']['default'][] = array(
		'title' => 'Site ID (bm-analytics site_id)',
		'args' => array(
			'label_for' => 'ads-site-id',
			'option_name' => 'ads-site-id',
			'option_type' => 'text-box',
		),
		'callback' => 'buzzmediaads_draw_option',
	);

	$wp_settings_fields['general']['default'][] = array(
		'title' => 'GA Acct Number (like UA-297416-7)',
		'args' => array(
			'label_for' => 'ads-ga-acct',
			'option_name' => 'ads-ga-acct',
			'option_type' => 'text-box',
		),
		'callback' => 'buzzmediaads_draw_option',
	);
}

function buzzmediaads_whitelist_options($current) {
	if (is_array($current) && isset($current['general']) && is_array($current['general'])) {
		$current['general'][] = 'ads-site-token';
		$current['general'][] = 'ads-site-id';
		$current['general'][] = 'ads-ga-acct';
		$current['general'] = array_unique($current['general']);
	}

	return $current;
}
add_action('admin_init', 'buzzmediaads_admin_init');
add_action('whitelist_options', 'buzzmediaads_whitelist_options');

function buzzmediaads_draw_option($args) {
	$args = wp_parse_args($args, array('option_type' => false, 'label_for' => false, 'default'));
	if (!empty($args['option_type']) && !empty($args['label_for'])) {
		switch ($args['option_type']) {
			case 'text-box':
				?><input type="text" name="<?= $args['label_for'] ?>" id="<?= $args['label_for'] ?>" value="<?= form_option($args['label_for']) ?>" class="widefat"/><?php
			break;
		}
	}
}

function buzzmedia_top_script() {
	global $ads_site_token, $ga_account_id;

	if (empty($ads_site_token)) {
		$ads_site_token = get_option('ads-site-token', 'TEST');
		$ads_site_token = empty($ads_site_token) ? 'TEST' : $ads_site_token;
	}
 
	// CB GA account
	if (empty($ga_account_id)) {
		$ga_account_id = get_option('ads-ga-acct', 'UA-297416-7');
		$ga_account_id = empty($ga_account_id) ? 'UA-297416-7' : $ga_account_id;
	}

	$domain = 'cdn.extensions.buzznet.com';
	if ((isset($_SERVER['BM_ENV']) && in_array($_SERVER['BM_ENV'], array('STG', 'DEV'))) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'buzznetdev') !== false))
		$domain = 'extensions-staging.jcugnodev1.buzznetdev.com';

	if (!defined('SHOW_ADS') || SHOW_ADS == true): ?>
		<script type="text/javascript">var _gaq=_gaq||[];_gaq.push(['_setAccount','<?=$ga_account_id?>']);_gaq.push(['_trackPageview']);<?php do_action('pi-ga-pageview-track'); ?>(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s);})();</script>
		<script type="text/javascript" src="http://<?= $domain ?>/topscript.js.php?siteToken=<?php echo $ads_site_token; ?>"></script><?php
	endif;
}

// echos ad tag
function buzzmedia_ad_dart($sizes, $position, $opts = '')
{

	// globalize vars
	global $ad_zone;
	global $ads_site_id;
	global $ads_default_opts;
	global $ad_category;
	global $ad_tile;
	global $photogallerypage6;

	if (empty($ads_site_id)) {
		$ads_site_id = get_option('ads-site-id', 'Test_Site');
		$ads_site_id = empty($ads_site_id) ? 'Test_Site' : $ads_site_id;
	}

	if (empty($opts) && !empty($ads_default_opts)) {
		$opts = $ads_default_opts;
	}

	// caching mechanism, get the zone first time only
	if ($ad_zone === FALSE) {
		$ad_zone = get_ad_zone();
	}

	// can take strings or arrays for sizes
	if (!is_array($sizes)) {
		$sizes = array($sizes);
	}

	// add key/value pair cat=<category slug>
	if ($ad_zone == 'category') {
		// caching mechanism, get the category first time only
		if ($ad_category === FALSE) {
			$ad_category = preg_replace('/[^a-z0-9]/i', '', get_category(get_query_var('cat'))->slug);
		}
		if ($opts) {
			$opts .= ';';
		}
		$opts .= 'cat=' . $ad_category;
	}

	// this was for some godforsaken ad camapign JT had to do right before he left ;)
	if (isset($photogallerypage6) && $photogallerypage6 === TRUE) {
		if ($opts) {
			$opts .= ';';
		}
		$opts .= 'page=photointerstitial';
	}

// echo
?>
<!-- Start Ad Tag <?php echo $position; ?> -->
<?php if (!defined('SHOW_ADS') || SHOW_ADS == true): ?>
<script type="text/javascript">if (typeof bmQuery == 'function') bmQuery.bmLib.iab.write({siteIdentifier: '<?php echo $ads_site_id; ?>', zone: '<?php echo $ad_zone; ?>', position: '<?php echo $position ?>', sizes: [<?php 
foreach ($sizes as $size) {
	echo "'" . $size . "'";
	if (end($sizes) != $size) {
		echo ",";
	}
}
?>]<?php if ($opts) { echo ', options: ' . '\'' . $opts . '\''; } ?>});</script>
<noscript><?php endif; ?><a href="http://ad.doubleclick.net/jump/<?php echo $ads_site_id; ?>/<?php echo $ad_zone; ?>;pos=<?php echo $position ?>;sz=<?php echo implode(',', $sizes); ?>;<?php if ($opts) {echo $opts.';';} ?>tile=<?php echo $ad_tile; ?>;ord=123456789?" target="_blank" ><img src="http://ad.doubleclick.net/ad/<?php echo $ads_site_id; ?>/<?php echo $ad_zone; ?>;pos=<?php echo $position ?>;sz=<?php echo implode(',', $sizes); ?>;<?php if ($opts) {echo $opts.';';} ?>tile=<?php echo $ad_tile; ?>;ord=123456789?" border="0" alt="" /></a><?php if (!defined('SHOW_ADS') || SHOW_ADS == true): ?></noscript><?php endif; ?>
<!-- End Ad Tag <?php echo $position; ?> -->
<?php

	// increment tile for next call
	$ad_tile++;
}

// this site-specific function contains rules to return the zone for the current page
function get_ad_zone() 
{
	global $current_ad_zone;

	// default/catch-all zone
	if (!empty($current_ad_zone)) {
		$zone = $current_ad_zone;
	} else {
		$zone = 'misc';

		$purl = parse_url(trailingslashit(home_url()));
		$path = isset($purl['path']) ? $purl['path'] : '/';
		$prurl = parse_url($_SERVER['REQUEST_URI']);
		$rpath = $prurl['path'];
		$test_uri = preg_replace('#^'.$path.'#', '/', $rpath);

		// set of rules
		if ($test_uri == '/') {
			$zone = 'homepage';
		} elseif (substr($test_uri, 0, 8) == '/page/2/') {
			$zone = 'page2';
		} elseif (substr($test_uri, 0, 8) == '/page/3/') {
			$zone = 'page3';
		} elseif (substr($test_uri, 0, 8) == '/page/4/') {
			$zone = 'page4';
		} elseif (substr($test_uri, 0, 8) == '/page/5/') {
			$zone = 'page5';
		} elseif (substr($test_uri, 0, 8) == '/page/6/') {
			$zone = 'page6';
		} elseif (substr($test_uri, 0, 8) == '/photos/' && strlen($test_uri) > 8) {
			$zone = 'photogallery';
		} elseif (substr($test_uri, 0, 8) == '/photos/') {
			$zone = 'photos';
		} elseif (substr($test_uri, 0, 8) == '/videos/') {
			$zone = 'videos';
		} elseif (substr($test_uri, 0, 7) == '/video/') {
			$zone = 'videos';
		} elseif (substr($test_uri, 0, 16) == '/celeb-bloggers/') {
			$zone = 'celebbloggers';
		} elseif (is_singular( array('gallery', 'attachment') )) {
			$zone = 'photos';
		} elseif (is_singular( array("video-post") )) {
			$zone = 'videos';
		} elseif (is_single()) {
			$zone = 'permalink';
		} elseif (is_category()){
			$zone = 'category';
		}
	}

	return $zone;

}

?>

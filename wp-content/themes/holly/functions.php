<?php
function get_public_version() {
	return md5(__FILE__);
}
//var_dump($_SERVER);
$ads_default_opts = 'cat=its';

function its_add_js() {
       wp_enqueue_script('its_functions', get_template_directory_uri().'/js/functions.js', array('jquery'), '1.0-lou', true);
}
add_action('wp', 'its_add_js', 1000);

if (!function_exists('k_disableAdminBar')) {

	function k_disableAdminBar(){
		remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 ); // for the admin page
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 ); // for the front end
		add_filter('admin_head','k_remove_admin_bar_style_backend');
		add_filter('wp_head','k_remove_admin_bar_style_frontend', 99);

	}
	function k_remove_admin_bar_style_backend() {  // css override for the admin page
		?>
		<style>
			body.admin-bar #wpcontent, body.admin-bar #adminmenu { 
				padding-top: 0px !important; 
			}
		</style>
		<?php 
	}
	function k_remove_admin_bar_style_frontend() { // css override for the frontend
		?> 
			<style type="text/css" media="screen"> 
				html { margin-top: 0px !important; } 
				* html body { margin-top: 0px !important; } 
			</style>
		<?php
	}
}
add_action('init','k_disableAdminBar'); // New version


add_theme_support('post-thumbnails');

function its_register_theme_sidebars() {
	$defaults = array(
		'name' => '',
		'id' => '',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	);

	$sidebars = array(
		'story-well-ad' => 'Story Well Ad',
		'category-story-well-ad' => 'Category Story Well Ad',
		'home-archive-category-sidebar' => 'Home + Archive + Category (shared)',
		'single-post-page-sidebar' => 'Post Pages',
		'tag-search-pages-sidebar' => 'Tag + Search Pages (shared)',
		'photos-hub-sidebar' => 'Photos Hub',
		'single-gallery-sidebar' => 'Gallery Pages',
		'single-fullsize-gallery-sidebar' => 'Full Size Gallery Pages',
		'header-carousel-topbar' => 'Header Carousel',
		'generic-sidebar' => array(
			'name' => 'Generic',
			'description' => 'The fallback sidebar that is used when there is no specific sidebar set for a given page.',
		),
	);
	$sidebars = apply_filters('its-register-sidebars', $sidebars, $defaults);

	foreach ($sidebars as $slug => $args) {
		if (is_string($args)) $args = array('name' => $args);
		$args['id'] = $slug;
		$args = wp_parse_args($args, $defaults);
		if (empty($args['id']) || empty($args['name'])) continue;
		register_sidebar($args);
	}
}
its_register_theme_sidebars();

//require_once(__DIR__.'/admin/admin-pages.php');
require_once(dirname(__FILE__).'/admin/admin-pages.php');

function its_nav_menus() {
	register_nav_menus(array(
		'main-menu' => 'Main Site Navigation',
		'photos-sub-menu' => 'Photos Sub Nav',
		'videos-sub-menu' => 'Videos Sub Nav',
		'hot-photos-widget-filters' => 'Hot Photos Widget Filters',
		'cb-shared-nav' => 'Shared Nav Items',
	));
}
add_action('init', 'its_nav_menus');

function its_zoom_on_attachments() {
	if (!is_admin() && is_singular(array('attachment', 'gallery')) && !is_fullsize()) {
		global $post, $allow_zoom;
		$allow_zoom = true;
		$test_id = $post->post_type == 'attachment' ? $post->post_parent : $post->ID;
		if (get_post_meta($test_id, '_disable_zoom', true)) $allow_zoom = false;
		if ($allow_zoom) wp_enqueue_script('zoom-script', get_template_directory_uri().'/js/zoom-fullsize.js', array('jquery'), '1.0-lou', true);
	}
}
add_action('wp', 'its_zoom_on_attachments', 1000);

function its_no_default_menu($args) {
	return '';
}

function relative_time($time, $d='', $gmt='') {
	global $use_relative_time;
	$use_relative_time = !isset($use_relative_time) ? true : $use_relative_time;

	$otime = $time;
	$post = get_the_ID();
	$post = get_post($post);
	$time = empty($time) ? $post->post_date : $time;
	$gmt = empty($gmt) ? $post->post_date_gmt : $gmt;
	$post_date = mysql2date('U', $gmt, false);

	$delta = time() - $post_date;
	if ( $use_relative_time && $delta < 60 ) {
		$entrydate = '<span class="entry-date-relative recent-time"><abbr class="published" title="';
		$entrydate .= 'Less than a minute ago">';
		$entrydate .= 'Less than a minute ago';
		$entrydate .= '</abbr></span>';
	} elseif ( $use_relative_time && $delta > 60 && $delta < 120){
		$entrydate = '<span class="entry-date-relative recent-time"><abbr class="published" title="';
		$entrydate .= 'A minute ago">';
		$entrydate .= 'A minute ago';
		$entrydate .= '</abbr></span>';
	} elseif ($use_relative_time && $delta > 120 && $delta < (60*60)){
		$entrydate = '<span class="entry-date-relative recent-time"><abbr class="published" title="';
		$entrydate .= strval(round(($delta/60),0)).' minutes ago">';
		$entrydate .= strval(round(($delta/60),0)).' minutes ago';
		$entrydate .= '</abbr></span>';
	} elseif ($use_relative_time && $delta > (60*60) && $delta < (120*60)){
		$entrydate = '<span class="entry-date-relative"><abbr class="published" title="';
		$entrydate .= 'An hour ago">';
		$entrydate .= 'An hour ago';
		$entrydate .= '</abbr></span>';
	} elseif ($use_relative_time && $delta > (120*60) && $delta < (24*60*60)){
		$entrydate = '<span class="entry-date-relative"><abbr class="published" title="';
		$entrydate .= strval(round(($delta/3600),0)).' hours ago">';
		$entrydate .= strval(round(($delta/3600),0)).' hours ago';
		$entrydate .= '</abbr></span>';
	} else {
		if (empty($d))
			$entrydate = mysql2date(get_option('date_format'), $time);
		else
			$entrydate = mysql2date($d, $time);
	}
	
	return $entrydate;
}

function relative_time_post($time, $d, $gmt) {
	if (is_feed()) return $time;
	$post = get_post(get_the_ID());
	$time = $post->post_date;
	$gmt = $post->post_date_gmt;
	$d = '';
	return relative_time($time, $d, $gmt);
}
add_filter('get_post_time', 'relative_time_post', 10000, 3);

function relative_time_comment($time, $d, $gmt) {
	global $comment;
	$time = $comment->comment_date;
	$gmt = $comment->comment_date_gmt;
	return relative_time($time, $d, $gmt);
}
add_filter('get_comment_time', 'relative_time_comment', 10000, 3);

function its_extra_body_class($classes) {
	global $extra_body_classes;
	
	if (is_array($extra_body_classes) && count($extra_body_classes) > 0) {
		$classes = array_merge($classes, $extra_body_classes);
	} else if (is_string($extra_body_classes) && strlen(trim($extra_body_classes)) > 0) {
		$classes[] = trim($extra_body_classes);
	}
    if(function_exists('is_fullsize') && !is_fullsize()) $classes[] = 'not-fullsize';
    $classes = array_unique($classes);

	return $classes;
}
add_filter('body_class', 'its_extra_body_class', 10, 1);

function its_fb_like_button($post_id=0) {
	$post_id = empty($post_id) ? get_the_ID() : $post_id;
	$out = '<div id="fb-like-'.$post_id.'" class="fb-like-btn post-social-button"><fb:like'
			.' href="'.html_entity_decode(get_permalink($post_id)).'" layout="button_count" show_faces="false" data-send="false" font="arial"></fb:like></div>';
	return $out;
}

function its_twitter_share_button($post_id=0) {
	$via = preg_replace('#^@#', '', apply_filters('get-theme-setting', 'celebuzz', 'twitter-at-name'));
	$vtxt = trim(apply_filters('get-theme-setting', 'via', 'twitter-via-text'));
	$vtxt = empty($vtxt) || strtolower($vtxt) == 'via' ? '' : ' '.$vtxt;
	$twtbt = apply_filters('get-theme-setting', 'check out', 'twitter-text-before-title');
	$twtbt = empty($twtbt) ? '' : $twtbt.' ';
	$post_id = empty($post_id) ? get_the_ID() : $post_id;
	if (empty($vtxt)) {
		$out = '<div class="tweet-share-btn post-social-button"><a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-url="'
			.html_entity_decode(get_permalink($post_id)).'" data-text="'.$twtbt.get_the_title($post_id).'" data-via="'.$via.'"></a>'
			.'</div>';
	} else {
		$out = '<div class="tweet-share-btn post-social-button"><a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-url=" "'
			.' data-text="'.$twtbt.get_the_title($post_id).' '.html_entity_decode(get_permalink($post_id)).$vtxt.' @'.$via.'"></a>'
			.'</div>';
	}
	return apply_filters('its-twitter-api-link', $out, $post_id);;
}

//apply_filters('like-and-share-buttons', '', get_the_ID(), array('fb'));
function its_get_share_links($current, $post_id=0, $btns=false) {
	$btns = $btns === false ? array('fb', 'twitter') : $btns;
	$btns = is_array($btns) ? $btns : (array)$btns;
	$post_id = empty($post_id) ? get_the_ID() : $post_id;

	$out = array();
	foreach ($btns as $btn) {
		if ($btn == 'fb') $out[] = its_fb_like_button($post_id);
		else if ($btn == 'twitter') $out[] = its_twitter_share_button($post_id);
		else if (has_filter('its-social-btn-'.$btn)) $out[] = apply_filters('its-social-btn-'.$btn, '', $post_id);
	}

	return $current.implode('', $out);
}
add_filter('like-and-share-buttons', 'its_get_share_links', 10, 3);

function its_share_button_scripts() {
	wp_enqueue_script('twitter-widgets', 'http://platform.twitter.com/widgets.js', array(), 'twitter-1.0');

}
add_action('init', 'its_share_button_scripts');

function its_add_this($location='', $share=false) {
	$atacct = apply_filters('get-theme-setting', 'celebuzz', 'addthis-acct');
	$fburl = apply_filters('get-theme-setting', 'http://www.facebook.com/CELEBUZZ', 'facebook-social-url');
	$twurl = apply_filters('get-theme-setting', 'http://www.twitter.com/CELEBUZZ', 'twitter-social-url');
	$twat = preg_replace('#^@#', '', apply_filters('get-theme-setting', 'celebuzz', 'twitter-at-name'));
	$vtxt = trim(apply_filters('get-theme-setting', 'via', 'twitter-via-text'));
	$vtxt = empty($via) ? '' : ' '.$vtxt;
	$twtbt = trim(apply_filters('get-theme-setting', 'check out', 'twitter-text-before-title'));
	$twtbt = empty($twtbt) ? '' : $twtbt.' ';
	?><script type="text/javascript">var addthis_config = { ui_click:true };</script><?php
	switch ($location):
		case 'single':
			?>
			<ul class="addthis_toolbox addthis_default_style menu" addthis:url="<?php esc_attr(get_permalink()) ?>" addthis:title="<?php esc_attr(get_the_title()) ?>" >
				<?php if ($share): ?>
					<li><span class="add-this-label">SHARE:</span></li>
					</li></li>
				<?php endif; ?>
				<li><a class="addthis_button_facebook its-icon its-facebook"><span></span></a></li>
				<li><a class="addthis_button_twitter its-icon its-twitter"><span></span></a></li>
				<li><a class="addthis_button_email its-icon its-email"><span></span></a></li>
				<li><a href="http://addthis.com/bookmark.php?v=250&amp;pub=<?php $atacct ?>" class="addthis_button_compact its-icon its-more-share"><span></span></a></li>
			</ul>
			<script type="text/javascript">var addthis_share = { templates: { twitter: '<?php $twtbt ?>{{title}} {{url}}<?php $vtxt ?> @<?php $twat ?>' } }</script>
			<?php /*
			<div class="add-this-follow-us">Follow on: <a href="<?php esc_attr($fburl) ?>">Facebook</a> / <a href="<?php esc_attr($twurl) ?>">Twitter</a></div>
			<div class="clear"></div>
			*/ ?>
			<?php
		break;

		case 'single-image':
			?>
			<ul class="addthis_toolbox addthis_default_style menu" addthis:url="<?php esc_attr(get_permalink()) ?>" addthis:title="<?php esc_attr(get_the_title()) ?>" >
				<?php if ($share): ?>
					<li><span class="add-this-label">SHARE:</span></li>
				<?php endif; ?>
				<li><a class="addthis_button_facebook its-icon its-facebook"><span></span></a></li>
				<li><a class="addthis_button_twitter its-icon its-twitter"><span></span></a></li>
				<li><a class="addthis_button_email its-icon its-email"><span></span></a></li>
				<li><a href="http://addthis.com/bookmark.php?v=250&amp;pub=<?php $atacct ?>" class="addthis_button_compact its-icon its-more-share"><span></span></a></li>
			</ul>
			<script type="text/javascript">var addthis_share = { templates: { twitter: '<?php $twtbt ?>{{title}} {{url}}<?php $vtxt ?> @<?php $twat ?>' } }</script>
			<?php
		break;

		case 'home':
		default:
			?>
			<ul class="addthis_toolbox addthis_default_style menu" addthis:url="<?php esc_attr(get_permalink()) ?>" addthis:title="<?php esc_attr(get_the_title()) ?>" >
				<?php if ($share): ?>
					<li><span class="add-this-label">SHARE:</span></li>
					</li></li>
				<?php endif; ?>
				<li><a class="addthis_button_facebook its-icon its-facebook"><span></span></a></li>
				<li><a class="addthis_button_twitter its-icon its-twitter"><span></span></a></li>
				<li><a class="addthis_button_email its-icon its-email"><span></span></a></li>
				<li><a href="http://addthis.com/bookmark.php?v=250&amp;pub=<?php $atacct ?>" class="addthis_button_compact its-icon its-more-share"><span></span></a></li>
			</ul>
			<script type="text/javascript">var addthis_share = { templates: { twitter: '<?php $twtbt ?>{{title}} {{url}}<?php $vtxt ?> @<?php $twat ?>' } }</script>
			<?php
		break;
	endswitch;

	wp_enqueue_script('its-addthis-javascript');
	global $wp_scripts;
	if (is_a($wp_scripts, 'WP_Scripts'))
		$wp_scripts->in_footer = array_merge($wp_scripts->in_footer, array('its-addthis-javascript'));
}
wp_register_script('its-addthis-javascript', 'http://s7.addthis.com/js/250/addthis_widget.js#pubid='.apply_filters('get-theme-setting', 'celebuzz', 'addthis-acct'), array(), '', true);
add_action('add-this-buttons', 'its_add_this', 10, 2);

function f_its_get_user_profile_link($current, $user_id=0) {
	if (empty($user_id)) {
		global $current_user;
		$user_id = is_object($current_user) && isset($current_user->ID) ? $current_user->ID : 0;
	}
	if (empty($user_id)) return '';
	return home_url('/wp-admin/profile.php?user_id='.$user_id);
}
add_filter('user-profile-link', 'f_its_get_user_profile_link', 10, 2);

function f_its_logout_url($url, $redirect_to) {
	$signout_page_id = get_option('_its_signout_page_id', 0);
	if (empty($signout_page_id)) {
		global $wpdb;
		$q = $wpdb->prepare(
			'select post_id from '.$wpdb->postmeta.' join '.$wpdb->posts.' on id = post_id where meta_key = %s and meta_value = %s and post_status = %s',
			'_wp_page_template',
			'templ-signout.php',
			'publish'
		);
		$signout_page_id = $wpdb->get_var($q);
		$signout_page_id = !is_numeric($signout_page_id) ? 0 : (int)$signout_page_id;
	}
	if (!empty($signout_page_id)) {
		$args = array();
		if (!empty($redirect_to)) $args['redirect_to'] = $redirect_to;
		$url = add_query_arg($args, get_permalink($signout_page_id));
	}
	return $url;
}
add_action('logout_url', 'f_its_logout_url', 10, 2);

function f_this_query($query) {
	var_dump($query);
	return $query;
}
if (isset($_GET['qdebug']) && $_GET['qdebug'] == 1) add_filter('request', 'f_this_query');

if (!class_exists('its_ajax_login_hooks')):
	class its_ajax_login_hooks {
		public static function pre_init() {
			wp_enqueue_script('ajax-login-its', get_template_directory_uri().'/js/ajax-login.js', array('jquery', 'bmfbc-starter'), '0.2-lou');
			add_filter('bmfbc-state-change-get-comments', array(__CLASS__, 'aj_get_comments'), 10, 2);
		}
	
		public static function aj_get_comments($current, $params) {
			parse_str($params, $ps);
			$ps = wp_parse_args($ps, array('p' => false));
			$params = array('p' => is_numeric($ps['p']) ? (int)$ps['p'] : 0);
			if (empty($params['p'])) return $current;

			ob_start();
			query_posts($params);
			if (have_posts()): while(have_posts()):
				the_post();
				comments_template('', true);
			endwhile; endif;
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
	}

	its_ajax_login_hooks::pre_init();
endif;

function its_registration_link($link) {
	$url = preg_replace('#^.*(?:src|href)="([^\"]+)".*$#s', '\1', $link);
	if ($url != $link) {
		if (preg_match('#wp-login#', $url)) {
			$url = apply_filters('get-celebuzz-url', '', '/register/'); //'http'.(is_ssl() ? 's' : '').'://'.$_SERVER['SERVER_NAME'].'/register/';

			global $blog_id;
			$arr = array(
				'referer' => site_url(),
				'referer_url' => 'http'.(is_ssl() ? 's' : '').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
				'blog_id' => $blog_id,
				'sitename' => get_bloginfo('sitename')
			);
			
			$url = add_query_arg(array('_its_signup_blog' => base64_encode(serialize($arr))), $url);
		}
		$link = preg_replace('#(^.*(?:src|href)=")([^\"]+)(".*$)#s', '\1'.esc_attr($url).'\3', $link);
	}
	return $link;
}
add_action('register', 'its_registration_link');

function is_descendant_of($slug, $post_type, $inclusive=false) {
	$k = md5(serialize(array('slug' => $slug, 'post-type' => $post_type)));
	$cur = wp_cache_get($k, 'descendant-cache');
	if (empty($cur)) {
		$args = array(
			'post_type' => $post_type,
			'name' => $slug,
			'post_status' => 'publish',
			'posts_per_page' => 1,
		);
		$posts = get_posts($args);
		if (is_array($posts) && count($posts) > 0) {
			$post = array_shift($posts);
			$cur = $post->ID;
			wp_cache_set($k, $cur, 'descendant-cache', 3600*24*7);
		}
	}
	if (!empty($cur)) {
		global $post;
		if ($inclusive && $cur == $post->ID) return true;
		$anc = get_ancestors(array('object_id' => $post->ID, 'object_type' => $post_type));
		if (in_array($cur, $anc)) return true;
	}
	return false;
}

function its_post_signature_block() {
	$img_id = apply_filters('ice-get-post-signature-img-id', 0);
	if (!empty($img_id)) {
		global $post;
		$more = (bool)preg_match('#<!--more.*?-->#', $post->post_content);
		if (is_single() || !$more) {
			?>
				<div class="post-signature">
					<div class="clear"></div>
					<?php ice_get_attachment_image($img_id, 'orig-with-brand', 'post-signature-img') ?>
					<div class="clear"></div>
				</div>
			<?php
		}
	}
}
add_action('its-article-before-footer', 'its_post_signature_block');

function its_theme_gallery_link_settings($settings) {
	$settings = wp_parse_args(array(
		'size' => array(180, 135),
		'title_words_count' => 8,
		'class' => implode(' ', get_post_class()),
		'counts' => '%d Photos &raquo;',
		'tags' => '<div class="gallery-categories entry-categories grey">{%tag_list%}</div>',
		'count' => '%d Photo &raquo;',
		'img' => '{%link_in%}<span class="key-hole kht180x135">{%img_tag%}</span>{%link_out%}',
		'title' => '<h2 class="gallery-title blackmaroon">{%link_in%}{%post_title%}{%link_out%}</h2>',
		'more' => '<div class="more">{%link_in%}{%count%}{%link_out%}</div>',
		'template' => '<article id="post-{%post_id%}" class="{%class%}"><header>{%img%}<div class="thirdDesc">{%tags%}{%title%}{%more%}</div></header></article><!-- end post-{%post_id%} -->'
	), $settings);
	return $settings;
}
add_filter('ice-gallery-render-link-pre-settings', 'its_theme_gallery_link_settings', 10);

function its_theme_gallery_link_search($current, $settings) {
	$current[] = '{%class%}';
	$current[] = '{%tag_list%}';
	return $current;
}
add_filter('ice-gallery-render-link-search', 'its_theme_gallery_link_search', 10, 2);

function its_theme_gallery_link_replace($current, $settings) {
	$current[] = implode(' ', get_post_class());
	$current[] = get_the_category_list(',');
	return $current;
}
add_filter('ice-gallery-render-link-replace', 'its_theme_gallery_link_replace', 10, 2);

function its_add_story_page_position($classes, $class, $post_id) {
	static $position = 1;
	$post_type = get_post_type($post_id);
	$new_class = sprintf('%s-%d', $post_type, $position++);
	$classes[] = esc_attr($new_class);
	return $classes;
}
add_filter('post_class', 'its_add_story_page_position', 10, 3);

// adding story-well ad to ITS
function its_story_well_ad() {
	?><div class="story-well-widgets"><?php
	if (!dynamic_sidebar('story-well-ad')):
	endif;
	?><div class="clear"></div></div><?php
}
add_action('its-story-well-after-story-2', 'its_story_well_ad');

function its_category_story_well_ad() {
	?><div class="story-well-widgets category-story-well-widgets"><?php
	if (!dynamic_sidebar('category-story-well-ad')):
	endif;
	?><div class="clear"></div></div><?php
}
//add_action('its-category-story-well-after-story-3', 'nc_category_story_well_ad');

function its_pagination($pargs='') {
	$pargs = wp_parse_args($pargs, array(
		'base' => site_url('/%_%'),
		'greyed' => true,
		'first_last' => false,
		'first_text' => '&lt;&lt;',
		'last_text' => '&gt;&gt;',
		'per_page' => false,
		'prev_next' => true,
		'prev_text' => '<span class="prev-button prev-next"></span>',
		'next_text' => '<span class="next-button prev-next"></span>',
		'end_size' => 1,
		'mid_size' => 2,
		'type' => 'plain',
	));

	global $wp_query, $paged;
	$per_page = empty($pargs['per_page']) ? (int)get_option('posts_per_page') : absint($pargs['per_page']);
	$per_page = $per_page <= 0 ? 5 : $per_page;
	$total_pages = ceil($wp_query->found_posts / $per_page);
	$current_page = get_query_var('paged');
	$current_page = empty($current_page) ? 1 : $current_page;
	$args = array(
		'base' => $pargs['base'],
		'format' => 'page/%#%/',
		'current' => $current_page,
		'total' => $total_pages,
		'end_size' => $pargs['end_size'],
		'mid_size' => $pargs['mid_size'],
		'prev_next' => $pargs['prev_next'],
		'prev_text' => $pargs['prev_text'],
		'next_text' => $pargs['next_text'],
		'type' => 'array',
	);
	$link = str_replace('%_%', $args['format'], $args['base']);

	$links = paginate_links($args);

	if (is_array($links)) {
		if ($pargs['prev_next'] && $pargs['greyed']) {
			if ($current_page == 1) array_unshift($links, sprintf('<span class="%s">%s</span>', 'page-numbers prev greyed', $pargs['prev_text']));
			if ($current_page == $total_pages) array_push($links, sprintf('<span class="%s">%s</span>', 'page-numbers next greyed', $pargs['next_text']));
		}
		if ($pargs['first_last']) {
			if ($current_page == 1 && $pargs['greyed']) array_unshift($links, sprintf('<span class="%s">%s</span>', 'page-numbers first greyed', $pargs['first_text']));
			else array_unshift($links, sprintf('<a class="%s" href="%s">%s</a>', 'page-numbers first', str_replace('%#%', 1, $link), $pargs['first_text']));
			if ($current_page == $total_pages && $pargs['greyed']) array_unshift($links, sprintf('<span class="%s">%s</span>', 'page-numbers last greyed', $pargs['last_text']));
			else array_unshift($links, sprintf('<a class="%s" href="%s">%s</a>', 'page-numbers last', str_replace('%#%', $total_pages, $link), $pargs['last_text']));
		}

		//foreach ($links as &$l) $l = preg_replace('#>\.\.\.<#', '>&nbsp;<', $l);

		$type = strtolower($pargs['type']);
		if ($type == 'array') return $links;
		elseif ($type == 'list') return "<ul class=\"page-numbers\">\n\t<li>".implode("</li>\n\t<li>",$links)."</li>\n</ul>";
	} else $links = array();
	
	return implode("\n", $links);
}

function its_comment_reply_link_add_class($link, $args, $comment, $post) {
	if (!is_user_logged_in() && get_option('comment_registration')) {
		$link = preg_replace('#(<a[^>]+class=)("|\')#', '\1\2sign-in ', $link);
		$link = preg_replace('#(<a[^>]+href=)("|\')[^\2]*\2#', '\1\2#\2', $link);
	}
	return $link;
}
add_filter('comment_reply_link', 'its_comment_reply_link_add_class', 10, 4);

function its_header_meta() {
	$meta = array();
	if (is_home() || is_front_page()) {
		$meta['description'] = get_option('ice-site-description', get_bloginfo('name').'\'s Official Site');
	} else if (is_single() || is_page()) {
		if (is_singular('gallery')) {
			$image_id = get_post_thumbnail_id();
			if (!empty($image_id)) {
				$post = get_post(get_the_ID());
			} else {
				$args = array(
					'post_type' => 'attachment',
					'post_status' => array('publish', 'inherit'),
					'post_parent' => get_the_ID(),
					'posts_per_page' => 1,
					'orderby' => 'menu_order',
					'order' => 'desc',
				);

				$posts =& get_posts($args);
				if (is_array($posts) && !empty($posts)) $post = array_shift($posts);
				else $post = get_post(get_the_ID());
			}
		} else {
			$post = get_post(get_the_ID());
		}
		$meta['description'] = trim(htmlspecialchars(strip_tags(htmlspecialchars_decode($post->post_excerpt))));
		if (empty($meta['description'])) $meta['description'] = trim(htmlspecialchars(strip_tags(htmlspecialchars_decode($post->post_content))));
	}

	$meta = apply_filters('its-header-meta', $meta);

	if (is_array($meta) && !empty($meta)) {
		echo "\n\n";

		foreach ($meta as $k => $v) {
			if (!empty($k)) {
				?>
				<meta name="<?php esc_attr($k) ?>" content="<?php esc_attr($v) ?>" />
				<?php
			}
		}
	}
}
add_action('wp_head', 'its_header_meta', 10000);

function its_home_rel_canonical() {
	if (is_home()) {
		?><link href="<?php esc_attr(trailingslashit(site_url())) ?>" rel="canonical"/><?php
	}
}
add_action('wp_head', 'its_home_rel_canonical', 10000);

function its_remove_prev_next($current) {
	return '';
}
add_filter('previous_post_rel_link', 'its_remove_prev_next');
add_filter('next_post_rel_link', 'its_remove_prev_next');

function its_make_robots_txt_404() {
	$template = get_404_template();
	if ( $template = apply_filters( 'template_include', $template ) )
		include( $template );
}
remove_action('do_robots', 'do_robots');
add_action('do_robots', 'its_make_robots_txt_404');

function its_second_make_robots_txt_404($current, $public) {
	its_make_robots_txt_404();
	return '';
}
add_action('robots_txt', 'its_second_make_robots_txt_404');

include 'includes/ads.php';

function html_cache($key,$fn,$timeout) {
    //ini_set('display_errors', 1);
    echo "<!-- HC:$key:start -->";
    $time_start = microtime(true);

    if (isset($_GET['clear_cache'])) {
        wp_cache_delete($key, 'html_cache');
    }

    if (!isset($_GET['no_cache'])) {
        $html = wp_cache_get($key, 'html_cache');

        if ($html !== FALSE) {
            echo $html.'<!-- HC:cached:'.$key.':'.($time = (($tim_end = microtime(true)) - $time_start)).' ms -->';
            return;
        }
    }

    // no cache? generate and store
    ob_start();
    $fn();
    $html = ob_get_clean();
    // for debug - don't include the time to set the transient, because thats not fair
    echo $html.'<!-- HC:cached:'.$key.':'.($time = (($tim_end = microtime(true)) - $time_start)).' ms -->';

    if (!isset($_GET['no_cache'])) {
        wp_cache_set($key, $html, 'html_cache');
    }
}

add_filter('ice-mainembed-settings', 'its_fix_overlay_text_if_array');
add_filter('main-embed-sets', 'its_fix_overlay_text_if_array');
function  its_fix_overlay_text_if_array($sets)
{
    if(isset($sets['overlay_text']) && is_array($sets['overlay_text']))
    {
        $text = empty($sets['overlay_text']['text']) ? '' : $sets['overlay_text']['text'];
        $sets['overlay_text'] = $text;
    }
    return $sets;
}

include 'widgets/pi-login.widget.php';
function pi_register_theme_widgets() {
    register_widget( 'PiLogin_Sidebar_Widget' );
}   
add_action( 'widgets_init', 'pi_register_theme_widgets' );

wp_dequeue_style('top-celeb-style');
function sg_savethumbnail($img_path, $size, $save_path)
{
	$imagetype = exif_imagetype($img_path);
	switch( $imagetype )
	{
		case IMAGETYPE_GIF:  $img = imagecreatefromgif($img_path);  break;
		case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($img_path); break;
		case IMAGETYPE_PNG:  $img = imagecreatefrompng($img_path);  break;
		default: $img = false;
	}

	if( !$img ) return false;

	$thumb = imagecreatetruecolor($size, $size);
	$result = false;		

	if ( ($imagetype == IMAGETYPE_GIF) || ($imagetype == IMAGETYPE_PNG) )
	{
		$trnprt_indx = imagecolortransparent($img);
		if($trnprt_indx >= 0 and $trnprt_indx<imagecolorstotal($img))
		{
			$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
			$trnprt_indx = imagecolorallocate($thumb, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			imagefill($thumb, 0, 0, $trnprt_indx);
			imagecolortransparent($thumb, $trnprt_indx);
		}
		elseif ( $imagetype == IMAGETYPE_PNG )
		{
			imagealphablending($thumb, false); 
			$color = imagecolorallocatealpha($thumb, 0, 0, 0, 127); 
			imagefill($thumb, 0, 0, $color); 
			imagesavealpha($thumb, true); 
		}
	}

	$img_size = getimagesize($img_path);
	$w_diff = ($img_size[0] - $img_size[1]);
	$w_diff = ($w_diff<0) ? 0 : $w_diff;
	$h_diff = ($img_size[1] - $img_size[0]);
	$h_diff = ($h_diff<0) ? 0 : $h_diff;

	imagecopyresampled($thumb, $img, 0, 0, $w_diff/2, $h_diff/2, $size, $size, $img_size[0]-$w_diff, $img_size[1]-$h_diff);

	switch($imagetype)
	{
		case IMAGETYPE_GIF:  $result = imagegif( $thumb, $save_path); break;
		case IMAGETYPE_JPEG: $result = imagejpeg($thumb, $save_path, 90); break;
		case IMAGETYPE_PNG:  $result = imagepng( $thumb, $save_path); break;
	}
	imagedestroy($thumb);

	return $result;
}

add_filter('get_avatar', 'wp_get_avatar', 10, 4);
// Avatar "filter" function
function wp_get_avatar($avatar, $userdata, $size, $default)
{
	if (is_numeric($userdata)) {
		$userdata = get_userdata((int) $userdata);
		if (!empty($userdata)) {
			$userdata->user_id = $userdata->ID;
		}
	}
	if ( !is_object($userdata) )
		return $avatar;

	if( !($userdata->user_id) )
		return $avatar;

	$wp_avatar = get_usermeta($userdata->user_id,'wp_avatar');
	// check user_meta for WP avatar
	if($wp_avatar)
	{
		// display local avatar
		if($size == 125)
			$wp_avatar = str_replace('thumb','avatar',$wp_avatar);

		if(function_exists('get_content_server'))
		{
			$surl = get_option('siteurl');
			$cdnurl = get_content_server($wp_avatar);
			$wp_avatar = str_replace($surl,  "http://".$cdnurl, $wp_avatar);
		}

		return '<img src="' . $wp_avatar . '" width="' . $size . '" height="' . $size . '">';
	} else {
		return $avatar;
	}
}

if ( ! function_exists( 'holly_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 */
function holly_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'twentytwelve' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'twentytwelve' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;
/*
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}
*/

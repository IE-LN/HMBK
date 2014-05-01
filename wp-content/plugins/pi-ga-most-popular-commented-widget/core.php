<?php
/**
	Plugin Name: PI GA Most Popular Commented Widget
	Version: 0.1
	Author: PostIndustria
 */

class PI_GA_Most_Popular_Commented_Widget extends WP_Widget
{
	protected static $key = 'pi-ga-widget-most-';
	protected static $limit = 5;
	protected static $cache_time = 900;


	public function __construct()
	{
		parent::__construct(false, $name = 'Pi GA Most Vieved/Most Commented');
	}

	public static function init()
	{
		wp_enqueue_style("pi-ga-most-popular-commented-widget-style", plugins_url('css/style.css', __FILE__));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'print_scripts'));
	}

	public static function print_scripts()
	{
		wp_enqueue_script('pi-ga-most-popular-commented-widget-script', plugins_url('js/script.js', __FILE__), array('jquery'));
	}

	public static function w_init()
	{
		return register_widget(__CLASS__);
	}

	public static function get_most_viewed_post($limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days')
	{

		$posts = array();

		$postIds = apply_filters( 'pi-ga-pageview-single-post',array(),$limit,$createtionPeriod,$calculationPeriod);

		if(!empty($postIds) && is_array($postIds)) 
		{
			$args = array(
			  'post__in' => $postIds,
			  'post_status' => 'publish',
			  'order' => ' ',
			);

			$tmpPosts = get_posts($args);
			foreach ($postIds as $id) {
				foreach ($tmpPosts as $key => $tp) {
					if(is_object($tp) && $tp->ID == $id) {
						$posts[] = $tmpPosts[$key];
					}
				}
			}
			unset($tmpPosts);
		} else {
			$posts = array();
		}

		return $posts;
	}

	public static function get_most_viewed_category($category, $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days')
	{
		$posts = array();

		$postIds = apply_filters( 'pi-ga-pageview-category-list',array(),$category,$limit,$createtionPeriod,$calculationPeriod);

		if(!empty($postIds) && is_array($postIds)) 
		{
			$args = array(
			  'post__in' => $postIds,
			  'post_status' => 'publish',
			  'order' => ' ',
			);

			$tmpPosts = get_posts($args);
			foreach ($postIds as $id) {
				foreach ($tmpPosts as $key => $tp) {
					if(is_object($tp) && $tp->ID == $id) {
						$posts[] = $tmpPosts[$key];
					}
				}
			}
			unset($tmpPosts);
		} else {
			$posts = array();
		}

		return $posts;
	}

	public static function get_mostt_commented_old($interval = '-24 HOUR')
	{
		$limit = self::$limit;
		$commented_posts = wp_cache_get(self::$key . 'mco');
		if ($commented_posts === false) {
			global $wpdb;
			$sql = "SELECT ID, post_name, post_title, comment_count FROM {$wpdb->prefix}posts 
					WHERE post_type='post' AND post_parent=0 AND post_status='publish' AND post_date > DATE_ADD(NOW(), INTERVAL $interval)
					ORDER BY comment_count DESC, post_date DESC LIMIT $limit";
			$commented_posts = $wpdb->get_results($sql);
			wp_cache_set(self::$key . 'mco', $commented_posts, '', 3600);
		}

		return $commented_posts;
	}

	public function widget($args, $instance)
	{
		$from_cache = true;
		$classname = get_class($this);
		$cache_key = 'widget-cache-key-' . $classname . md5(serialize($instance));
		$cache = wp_cache_get($cache_key, 'sidebar-widgets');
		if (isset($_GET['clear_cache'])) {
			wp_cache_delete($cache_key, 'sidebar-widgets');
			$cache = false;
		}
		if (empty($cache)) {
			ob_start();
			$this->_widget($args, $instance);
			$html = ob_get_clean();
			$cache = array('t' => time(), 'h' => $html);
			wp_cache_set($cache_key, $cache, 'sidebar-widgets', self::$cache_time);
			$from_cache = false;
		}

		$ci = $from_cache ? 'FROM:CACHE' : 'CACHE:REDRAW';
		echo "<!-- {$ci}:start:[{$cache_key}]{{$cond}}set:{$cache['t']} -->";
		echo $cache['h'];
		echo "<!-- {$ci}:end:[{$cache_key}] -->";
	}

	public static function _widget($args, $instance)
	{
		$name = 'most_viewed.php';
		$path = locate_template('templates/pi-ga-most-popular-commented-widget' . '/' . $name);
		if (empty($path)) {
			$path = dirname(__FILE__) . '/' . 'templates' . '/' . $name;
		}
		if (!file_exists($path)) {
			return '';
		}

		extract($args);
		include $path;
	}
}

if (!function_exists('_trim_string')) {
	function _trim_string($str = '', $limit = 100, $suffix_trimmed = "&nbsp;&#8230;")
	{
		if (strlen($str) > $limit) {
			return substr($str, 0, $limit) . $suffix_trimmed;
		}

		return $str;
	}
}

add_action('widgets_init', array('PI_GA_Most_Popular_Commented_Widget', 'w_init'));
add_action('init', array('PI_GA_Most_Popular_Commented_Widget', 'init'));

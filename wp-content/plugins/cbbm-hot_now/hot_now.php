<?php 

/*
Plugin Name: Celebuzz BuzzMedia Hot Now Caching
Description: Creates lookup tables to contain data for Hot Now. Header, sidebar and Hot Now Hub all use this. Requires wp-slim-stat plugin.
Version: 1.1
Author: jcugno/mwhite/tsmith
License: GPL2
*/


/* Class to deal with hot-now hot now most popular most_popular in page hot-now-hub and on sidebar and 
	Uses the buzz external tracking API as the source for most popular by views, and the internal wordpress
	database for most popular by comments. Result sets are cached for 5 minutes, and rebuilt on expiry.
*/
(__FILE__ == $_SERVER['SCRIPT_FILENAME']) ? die(header('Location: /')) : null;
class hot_now {

	// intervals we are interested in: 15 min, 1 hour, etc
	// used to display menu and map to values understood
	// by the tracker API
	protected static $intervals = array(
		array('label' => 'Last 15 Mins', 'val' => 'last_15m'), 
		array('label' => 'Last Hour', 'val' => 'last_hour'), 
		array('label' => 'Last 24 Hours', 'val' => 'last_24h'), 
		array('label' => 'Last 48 Hours', 'val' => 'last_48h'), 
		array('label' => 'Last Week', 'val' => 'last_week'), 
		array('label' => 'Last Month', 'val' => 'last_month')
	);
	
	protected static $api_host='http://q.cms.celebuzz.com';
	

	// The interval we are currently looking at:
	protected $current_interval;

	// the corresponding option for last updat in wp_options table
	protected $option_name;

	// constructor
	function hot_now() {

		if(isset($_GET['current_interval'])) {
			$this->current_interval = intval($_GET['current_interval']);
		} else {
			$this->current_interval = 1; // 1 hour default value
		}
	}
	
	function get_hottest($limit, $interval = '', $post_type = '', $orderby = '')
	{
		return $this->get_hottest_views($limit, $interval = '', $post_type = '', $orderby = '');
	}
	
	function get_most_commented ($limit=20, $interval = '', $post_type = '', $orderby = '')
	{
		
	
		if(empty($interval)) $interval = $this->current_interval;
		$string_interval = hot_now::$intervals[$interval]['val'];
		
		// serve from cache if possible
		$cache_key='hmost_comm_'.$string_interval;
		$cached_value= get_transient($cache_key);
		if ($cached_value !== FALSE) {
			echo '<!-- served from cache -->';
			return $cached_value;
		}
		
		// No cache? fine, we'll rebuild
		$mysql_interval='-1 MONTH'; // default
		// map the interval values to something we can use in a SQL query		
		$intervalMap = array (
								'last_15m'=>'-15 MINUTE',
								'last_hour'=>'-1 HOUR',
								'last_24h'=>'-1 DAY',
								'last_48h'=>'-2 DAY',
								'last_week'=>'-1 WEEK',
								'last_month'=>'-1 MONTH',
							 );
							 
		if (isset($intervalMap[$string_interval])) {
			$mysql_interval=$intervalMap[$string_interval];
		}
		
		// hit the database, then do some work with the returned data to make sure the front end has
		// the values it is expecting
		global $wpdb;
		
		$now = gmdate('Y-m-d H:i:s'); // since wordpress keeps track of the GMT time of comments, best to use that as the basis
		if(apply_filters('ice-comments-get-setting', false, 'ice_comment_pull_child') && !is_admin()){                                                                                                                                                                       
			$sql = "SELECT p.`ID` AS `ID`, p.post_name as post_name, p.post_title as title,p.post_type, m.meta_value AS comments FROM {$wpdb->prefix}posts AS p
					LEFT JOIN {$wpdb->prefix}postmeta AS m ON p.`ID`=m.`post_id`
					WHERE p.post_type='post' AND p.post_parent=0 AND p.post_status='publish' AND p.post_date > DATE_ADD('$now', INTERVAL $mysql_interval)
					      AND m.meta_key='_ice_comment_count'
					ORDER BY m.meta_value+0 DESC, post_date DESC LIMIT $limit";  //m.meta_value+0 for ordering as int
		}else
		{
			$sql = "SELECT ID, post_name, post_title as title,p.post_type, comments FROM {$wpdb->prefix}posts 
					WHERE post_type='post' AND post_parent=0 AND post_status='publish' AND post_date > DATE_ADD('$now', INTERVAL $mysql_interval)
					ORDER BY comment_count DESC, post_date DESC LIMIT $limit";
		}
		
		$data_source = $wpdb->get_results($sql);
		
		// touchup data for UI
		for ($i = 0;$i < count($data_source);$i++) {
				$data_source[$i]->resource = get_permalink($data_source[$i]->ID);
				$thumb_id = get_post_thumbnail_id($data_source[$i]->ID);
				list($thumb) = wp_get_attachment_image_src($thumb_id, array(80, 60));
				$data_source[$i]->thumbnail = $thumb;
		}
		
		// cache for 5 minutes - @todo, maybe should lift this
		set_transient($cache_key,$data_source,60*5);
		return $data_source;
	
	}

	protected function _get_api_response($api_call, $limit=20, $thumb_size=false) {
		$thumb_size = !is_array($thumb_size) || count($thumb_size) != 2 ? array(80, 60) : $thumb_size;
		$cache_key='hot_'.md5($api_call).".$limit";
		
		if (isset($_GET['clear_cache'])) {
			delete_transient($cache_key);
		}	
		
		// return from cache if possible
		$cached_value = get_transient($cache_key);
		
		if ($cached_value != false || $cached_value == 'empty') {
			// echo "<!-- hot now served from cache -->";
			
			if ($cached_value == 'empty') {
				return array();
			}
			return $cached_value;
		}
		
		// no cache? generate and store
		$api_response=@file_get_contents($api_call);
		
		if(!$api_results = json_decode($api_response)) {
			set_transient($cache_key, 'empty', 30);
			return array(); // if api error, return null and dont cache
		} else {
			$data_source = $api_results->topItems->post_view;
			
			if (count($data_source) === 0) {
				set_transient($cache_key, 'empty', 30); 
				return array();	
			}
			
			if (count($data_source) < $limit) {$limit=count($data_source);} // get either the limit, or the max possible if that is less than the limit
			
			// wrangle api returned data to structure required by rest of system
			for ($i = 0; $i < $limit; $i++) {
				$post_arr = $this->get_post_info_for_id($data_source[$i]->gid);
				if ($post_arr['post_status'] != 'publish') {
					// If post isn't published, skip it
					continue;
				}
				$data_source[$i]->ID=$data_source[$i]->gid;
				$data_source[$i]->resource = $post_arr['resource'];
				$data_source[$i]->title = $post_arr['post_title'];
				$data_source[$i]->type = $post_arr['post_type'];
				$data_source[$i]->comments = $this->get_comment_count($data_source[$i]->gid);
				$data_source[$i]->visits = $data_source[$i]->count;
				$thumb_id = get_post_thumbnail_id($data_source[$i]->ID);
				list($thumb) = wp_get_attachment_image_src($thumb_id, $thumb_size);
				$data_source[$i]->thumbnail = $thumb;
				$data[]=$data_source[$i];
			}
			
			set_transient($cache_key,$data,60*5); // cache for 5 minutes
			return $data;	
		}
	}
	
	function api_get_hottest_views($limit, $time, $actions, $format, $thumb_size=false) {
		$thumb_size = !is_array($thumb_size) || count($thumb_size) != 2 ? array(80, 60) : $thumb_size;
		$env = apply_filters('bm-analytics-env-name', get_bloginfo('name'));
		// fetch the data from the server and clean up to just what we want
		$api_call=hot_now::$api_host.'/analytics/summary/get-top-items?site='.$env.'&actions='.$actions.'&time='.$time.'&format='.$format;
		return self::_get_api_response($api_call, $limit, $thumb_size);
	}
	
	function get_hottest_views($limit, $interval = '', $post_type = '', $orderby = '') {
	
		if(empty($interval)) $interval = $this->current_interval;

		$string_interval = hot_now::$intervals[$interval]['val'];
		$env = apply_filters('bm-analytics-env-name', get_bloginfo('name'));
		// fetch the data from the server and clean up to just what we want
		$api_call=hot_now::$api_host.'/analytics/summary/get-top-items?site='.$env.'&actions=post_view&time='.$string_interval.'&format=json';
		return self::_get_api_response($api_call, $limit);
	}
	
	// FOLLOWING ARE HELPER FUNCTIONS TO THE MAIN get_hottest method

	// gets information about the post required but not provided
	// by external tracker
	function get_post_info_for_id($id) {
		if($id==0) return false;
		global $wpdb;
				// get post id and title from our resource/name
		$q = "select ID, post_title, post_type, post_status from " . $wpdb->prefix . "posts where ID = $id";
		$post_arr = $wpdb->get_row($q, ARRAY_A);
		$post_arr['resource']=get_permalink($id);
		return $post_arr;
	}

	// We just need a numeric count of comments on post_id. 
	function get_comment_count($post_id) {
		global $wpdb;
		if($post_id > 0) {
			$q = "select count(*) from " . $wpdb->prefix . "comments where comment_post_ID = '$post_id'";
			$comment_count = $wpdb->get_var($q);
//echo "<h1>\n" . print_r($comment_count) . "</h1>\n";//DEBUG
		} else {
			$comment_count = 0;
		}
		return $comment_count;
	}

	// function to show the pink hotness bar:
	function get_graph_img($val, $max_num, $width=320) {
		if ($max_num == 0 || $width == 0) $width = 1;
		else $width = intval($val / $max_num * $width);
		if($width == 0) $width = 1;
		return $width;
	}
	

	// default time is 5 days. Probably change to 15 minutes or 24 hours for production
	function get_hot_form() {
		$current_interval = $this->current_interval;
		//$hot_form = '<span>' . self::$intervals["$current_interval"]['label'] . "</span>";//DEBUG
		$hot_form = '<form name="hot_now_form">
		<span class="in-the">in the </span><select name="current_interval" onchange="document.hot_now_form.submit();">';

		foreach(self::$intervals as $k => $v) {
			if($current_interval == $k) {
				$hot_form .= '<option value="'. $k .'" selected>' . $v['label'] . '</option>';
			} else {
				$hot_form .= '<option value="'. $k .'">' . $v['label'] . '</option>';
			}
		};

		$hot_form .= '</select></form>';
		return $hot_form;
	}

	

} //end class

function cbbm_hot_now_init() {  
	global $cbbm_hot_now;
	$cbbm_hot_now = new hot_now();
}

if(defined('ABSPATH') && defined('WPINC')) {
	add_action('init', 'cbbm_hot_now_init', 9000, 0);
}

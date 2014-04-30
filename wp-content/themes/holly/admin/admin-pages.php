<?php (__FILE__ == $_SERVER['SCRIPT_FILENAME']) ? die(header('Location: /')) : null;

if (!class_exists('bmits_theme_ice_tab')):
	class bmits_theme_ice_tab {
		protected static $op_tab_slug = 'its-theme-options';
		protected static $op_tab_title = 'ITS Theme Options';
		protected static $option_name = '_ice-its-theme-settings'; // the option name in which the options for this plugin are stored
		protected static $opt_pre = 'ice-its-theme-'; // the prefix for all options on the ICE Core Settings page for this plugin
		protected static $option_defs; // the default options for this plugin
		protected static $settings; // the current options for this plugin loaded from the db and transposed on top of the defaults

		public static function pre_init() {
			self::$option_defs = array(
				'celebuzz-url' => 'http://'.$_SERVER['SERVER_NAME'].'/',
				'addthis-acct' => 'celebuzz',
				'twitter-text-before-title' => 'check out',
				'twitter-social-url' => 'http://www.twitter.com/celebuzz',
				'twitter-at-name' => '@celebuzz',
				'facebook-social-url' => 'http://www.facebook.com/CELEBUZZ',
			);
			add_action('admin_init', array(__CLASS__, 'a_admin_init'), 8);
			add_filter('get-celebuzz-url', array(__CLASS__, 'f_get_celebuzz_url'), 10, 2);
			add_filter('get-theme-setting', array(__CLASS__, 'f_get_theme_setting'), 10, 2);
			self::_load_settings();
			add_action('init', array(__CLASS__, 'a_plugins_loaded'), 11);
		}

		public static function a_plugins_loaded() {
			if (class_exists('bmice_comment')) remove_action('comment-author-permalink', array('bmice_comment', 'get_author_permalink'));
			add_action('comment-author-permalink', array(__CLASS__, 'get_author_permalink'), 100, 1);
		}

		// overtake the author link functionality for author links if the appropriate option is set
		public static function get_author_permalink($userid_or_name){
			// if nothing was passed in, then we can do nothing, so fail
			if (empty($userid_or_name)) return;
			
			$userName = '';
			// determine the author link pre-slug. default is /author/<author-name>/ (author)
			$pSlug = 'author';
			// if the user id is passed in
			if (is_numeric($userid_or_name)) {
				// fetch the username from the user id
				$id = (int) $userid_or_name;
				$user = get_userdata($id);
				if ( $user ) $userName = $user->user_nicename;  
			// if the user name is passed in
			} else {
				$userName = $userid_or_name;     
			}
			
			// create the permalink by mashing the pre-slug and the username together with the home url
			$user_profile_link = get_bloginfo('url').'/'.$pSlug.'/'.$userName;
			echo $user_profile_link;
		}

		public static function a_admin_init() {
			do_action('add-ice-settings-tab', self::$op_tab_slug, array(
				'title' => self::$op_tab_title,
				'save-func' => array(__CLASS__, 'a_process_save_settings'),
				'head-func' => array(__CLASS__, 'a_options_tab_head'),
				'metaboxes' => array(
					// register the basic layout settings metabox on the Layout tab of the settings page
					array(
						'title' => 'Shared Nav',
						'func' => array(__CLASS__, 'mb_shared_nav'),
						'side' => 'right',
						'order' => 1
					),
					array(
						'title' => 'ITS Theme Settings',
						'func' => array(__CLASS__, 'mb_theme_settings'),
						'side' => 'left',
						'order' => 1
					),
				)
			));
		}

		public static function f_get_celebuzz_url($current, $extra='') {
			$current = self::$settings['celebuzz-url'];
			$current = str_replace(parse_url($current, PHP_URL_SCHEME), is_ssl() ? 'https' : 'http', $current);
			$current = trailingslashit($current).ltrim($extra, '/');
			return $current;
		}

		public static function f_get_theme_setting($current, $name=false) {
			$res = '';

			$lookup_name = preg_replace('#-user$#', '-social-url', $name);
			if (is_string($name) && isset(self::$settings[$name])) {
				$res = self::$settings[$name];
			} else if (is_string($lookup_name) && isset(self::$settings[$lookup_name])) {
				$url = self::$settings[$lookup_name];
				$purl = parse_url($url);
				$path = trim($purl['path'], '/');
				$path = explode('/', $path);
				$path = array_shift($path);
				if (!empty($path)) $res = $path;
			}

			return $res;
		}

		public static function a_options_tab_head() {
		}

		// draw the metabox that controls the basic settings for the layouts plugin. currently this is to just turn them on and off
		public static function mb_shared_nav($tab, $mbargs) {
			?>
				<p>
					<label for="<?php self::$opt_pre ?>celebuzz-url">Celebuzz Url:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>celebuzz-url" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>celebuzz-url" value="<?php self::$settings['celebuzz-url'] ?>" />
				</p>
			<?php
		}

		// draw the metabox that controls the basic settings for the layouts plugin. currently this is to just turn them on and off
		public static function mb_theme_settings($tab, $mbargs) {
			?>
				<p>
					<label for="<?php self::$opt_pre ?>addthis-acct">addThis Account Name:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>addthis-acct" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>addthis-acct" value="<?php self::$settings['addthis-acct'] ?>" />
				</p>
				<p>
					<label for="<?php self::$opt_pre ?>twitter-social-url">Twitter URL:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>twitter-social-url" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>twitter-social-url" value="<?php self::$settings['twitter-social-url'] ?>" />
				</p>
				<p>
					<label for="<?php self::$opt_pre ?>twitter-at-name">Twitter @ name:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>twitter-at-name" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>twitter-at-name" value="<?php self::$settings['twitter-at-name'] ?>" />
				</p>
				<p>
					<label for="<?php self::$opt_pre ?>twitter-text-before-title">Twitter Text Before Title:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>twitter-text-before-title" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>twitter-text-before-title" value="<?php self::$settings['twitter-text-before-title'] ?>" />
				</p>
				<p>
					<label for="<?php self::$opt_pre ?>facebook-social-url">Facebook URL:</label>
					<input type="hidden" name="<?php self::$opt_pre ?>facebook-social-url" value="0" />
					<input type="text" class="widefat" name="<?php self::$opt_pre ?>facebook-social-url" value="<?php self::$settings['facebook-social-url'] ?>" />
				</p>
			<?php
		}

		// load the settings from the db for use later
		protected static function _load_settings() {
			$o = wp_parse_args(get_option(self::$option_name), self::$option_defs);
			self::$settings = $o;
		}

		// actually saves the current self::$settings to the database
		protected static function _save_settings() {
			// make sure that we have at least the default setting for all the options available
			$o = wp_parse_args(self::$settings, self::$option_defs);
			// save the settings to the db
			update_option(self::$option_name, $o);
			self::$settings = $o;
		}

		// save the settings on the gallery tab of the ICE settings page
		public static function a_process_save_settings() {
			// foreach setting that this plugin conrols, record any changes that may have been made on the settings page
			foreach (self::$option_defs as $key => $val)
				if (isset($_POST[self::$opt_pre.$key])) self::$settings[$key] = $_POST[self::$opt_pre.$key];
			// allow other plugins to make their changes to the settings if need be
			self::$settings = apply_filters(self::$opt_pre.'save-settings-array', self::$settings);
			// actually save the settings
			self::_save_settings();
		}
	}

	if (defined('ABSPATH') && function_exists('add_action')) {
		bmits_theme_ice_tab::pre_init();
	}
endif;

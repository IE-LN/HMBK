<?php
/*
Plugin Name: CDN Image Rewriter
Description: A plugin that redirects images to the CDN of your choice.
Author: Pedro Canahuati, rewritten by Dave Tichy ( dtichy@buzz-media.com )
Version: 0.2
*/


class BuzzMedia_CDN_Rewriter
{
	
	var $old_url;
	var $new_url;
	var $servers;
	var $exclusion_rules;
	
	
	function BuzzMedia_CDN_Rewriter()
	{
		$this->__construct();
	}

	
	function __construct()
	{
		register_activation_hook(__FILE__, array(&$this, 'install_plugin'));
		add_action('admin_menu', array(&$this, 'cdn_menu'));
		
		add_filter('the_content', array(&$this, 'image_cdn_rewrite'), 10);
		// add_filter('the_excerpt', array(&$this, 'image_cdn_rewrite') ,10);
		add_filter('get_the_excerpt', array(&$this, 'image_cdn_rewrite') ,10);

		add_filter('wp_get_attachment_url', array(&$this, 'simple_cdn_rewrite') ,10);
		add_filter('wp_get_attachment_thumb_url', array(&$this, 'simple_cdn_rewrite'), 10);
		//add_filter('bloginfo', array(&$this, 'simple_cdn_rewrite'), 1000);

		// add_filter('the_content_rss', array(&$this, 'image_cdn_rewrite'), 10);
		// add_filter('the_excerpt_rss', array(&$this, 'image_cdn_rewrite'), 10);
		
		$this->old_url = get_option('cdnOldUrl');
		$this->new_url = get_option('cdnNewUrl');
		$this->servers = $this->get_cdn_servers();
		$this->exclusion_rules = $this->get_exclusion_rules();

		$old_urls = trim($this->old_url, "\n\r\b\0 ,");
		$doms = empty($old_urls) ? array() : preg_split('#[\s,]+#', $old_urls);
		$domains = array();
		foreach ($doms as $dom) {
			$dom = trim($dom);
			if (!empty($dom)) $domains[] = $dom;
		}
		$this->old_urls_array = $domains;
	}

	
	/**
	 * Initialize the plugin when first activated
	 * 
	 * @access public
	 * @return void
	 */
	function install_plugin()
	{
	  add_option('cdnOldUrl', '');
	  add_option('cdnNewUrl', '');
	  add_option('cdnExcludeRules', '');
	}


	//-----------------------------------------------------------------------------
	
	
	/**
	 * Get the array of active CDN servers to which we should rewrite.
	 * 
	 * @access public
	 * @param string $servers_string. (default: null)
	 * @return array
	 */
	function get_cdn_servers($servers_string=null)
	{
		if (is_null($servers_string)) {
			$servers_string = $this->new_url;
		}
		return $this->linebreaks_to_array($servers_string);
	}
	
	
	/**
	 * Get an array of exclusion rules for bypassing CDN rewrites.
	 * 
	 * @access public
	 * @param string $rules_string. (default: null)
	 * @return array
	 */
	function get_exclusion_rules($rules_string=null)
	{
		if (is_null($rules_string)) {
			$rules_string = get_option('cdnExcludeRules');
		}
		return $this->linebreaks_to_array($rules_string);
	}
	
	/**
	 * Helper function to create an array from a 1-on-each-line string.
	 * 
	 * @access public
	 * @param string $string
	 * @return array
	 */
	function linebreaks_to_array($string)
	{
		$array = array();
		
		$mess = explode("\n", $string);
		if (count($mess) == 1) {
			$mess = explode("\r", $mess[0]);
		}
		foreach ($mess as $thing) {
			if (trim($thing) != '') {
				$array[] = trim($thing);
			}
		}
		return $array;
	}
	
	
	//-----------------------------------------------------------------------------
	
	
	/**
	 * Hash the given URL and decide which CDN server to use for rewrites.  Return 
	 * the CDN url to use.
	 * 
	 * @access public
	 * @param string $uri
	 * @return string
	 */
	function get_content_server($uri)
	{
//		$num_servers = count($this->servers);
		//if(is_array($uri)) {
//			$hash_float = hexdec(md5($uri[0]));
//		}
//		else {
//			$hash_float = hexdec(md5($uri));
//		}
//		if (strstr($hash_float, '+')) {
//			$hash_float = (float) substr($hash_float, 0, strpos(strtoupper($hash_float), 'E') );
//		}  
//		$server_id = fmod($hash_float, $num_servers); // nice.
		$num_servers = count($this->servers);
		if ($num_servers > 0) {
        if(is_array($uri)) {
            $md5 = md5($uri[0]);
        }
        else {
            $md5 = md5($uri);
        }
        					
        $md5 = substr($md5, 0, 2);
        $hash_number = hexdec($md5);
        
        $server_id = ($hash_number % $num_servers);
                            		
			return $this->servers[$server_id];
		} 
		$url = parse_url(site_url());

		return $url['host'];
	}
	
	
	/**
	 * Return a rewritten HTML fragment to point to a properly balanced CDN server 
	 * for the given URI. 
	 * 
	 * @access public
	 * @param string $content
	 * @return string
	 */
	function image_cdn_rewrite($content) 
	{
		foreach ($this->exclusion_rules as $pattern) {
			//error_log("cdnexclude:".implode(",", $content));
			if (strstr($content, $pattern)) return $content;
		}

		$content = preg_replace_callback('#(src=)(["\'])([^\2\s]*?)\2#', array(__CLASS__, 'image_url_transpose'), $content);
		return $content;
	}

	public function image_url_transpose($match) {
		$url = $match[3];
		$quote = $match[2];
		$attr = $match[1];

		$url = self::_url_transpose($url);
		return $attr.$quote.$url.$quote;
	}

	protected function _url_transpose($url) {
		$new_domain = $this->get_content_server($url);
		$domains = $this->old_urls_array;
		$rewritten = $url;

		if (!empty($domains)) {
			foreach ($domains as $domain) {
				$regex = '#(https?://)'.$domain.'#';
				$replacement = '\1'.$new_domain;
				$rewritten = preg_replace($regex, $replacement, $url);
				if ($rewritten != $url) break;
			}
		}

		$url = $rewritten;

		if (is_multisite()) {
			$u = wp_upload_dir();
			$base_path = $u['basedir'];
			$base_path = str_replace(ABSPATH, '', $base_path);
			$base_path = '/'.trim($base_path, '/').'/';
			$url = str_replace('/files/', $base_path, $url);
		}

		return $url;
	}
	
	
	/**
	 * Return a rewritten URI to a properly balanced CDN server for the given URI. 
	 * 
	 * @access public
	 * @param string $content
	 * @return string
	 */
	function simple_cdn_rewrite($content)
	{
		foreach ($this->exclusion_rules as $pattern) {
			if (strstr($content, $pattern)) return $content;
		}

		$content = self::_url_transpose($content);
		return $content;
	}
	
	
	//-----------------------------------------------------------------------------
	
	
	/**
	 * Add the options page for CDN plugin.
	 * 
	 * @access public
	 * @return void
	 */
	function cdn_menu()
	{
		add_options_page('CDN Options', 'CDN Options', 8, __FILE__, array(&$this, 'cdn_options'));
	}
	
	
	/**
	 * Display the options page for the CDN plugin.
	 * 
	 * @access public
	 * @return void
	 */
	function cdn_options() 
	{
		if ( isset($_POST['action']) && ( $_POST['action'] == 'updateCdn' )){
			update_option('cdnOldUrl', $_POST['cdnOldUrl']);
			$servers = $this->linebreaks_to_array($_POST['cdnNewUrl']);
			sort($servers);
			update_option('cdnNewUrl', implode("\n", $servers));
			
			/* @todo - this might be bad. */
			$rules = $this->linebreaks_to_array($_POST['cdnExcludeRules']);
			update_option('cdnExcludeRules', implode("\n", $rules));
		}
		
		?>
		<div class="wrap">
		<h2>CDN URL Settings</h2>
		<p>Multiple options can be seperated by comma or space. Please do not leave any trailing comma or space and slash.
		<br />
		<form method="post" action="">
		<?php 
		// wp_nonce_field('update-options'); 
		?>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="cdnOldUrl">Original Site URLs</label></th>
			<td><span class="setting-description">Your blog's main top level URls or 
				any urls you want to rewrite without <b>http://</b> <br />Comma separated, 
				e.g., mydomain.com,www.mydomain.com,img.mydomain.com</span><br />
			<input type="text" name="cdnOldUrl" value="<?php echo get_option('cdnOldUrl'); ?>" size="50" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cdnNewUrl">URL Rewrite</label></th>
			<td><span class="setting-description">The NEW URLS that should be displayed (One per line)<br />
				Requests will be evenly spread across the servers you provide here.</span><br />
				<textarea name="cdnNewUrl" cols="40" rows="5"><?php echo get_option('cdnNewUrl'); ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cdnExcludeRules">Exclude Rules</label></th>
			<td><span class="setting-description">URLs that should be excluded from CDN rewrites (One per line)<br />
				Use relative paths for best results (e.g. /wp-content/plugins/some-captcha-plugin ).</span><br />
				<textarea name="cdnExcludeRules" cols="40" rows="5"><?php echo get_option('cdnExcludeRules'); ?></textarea>
			</td>
		</tr>
		</table>
		
		<input type="hidden" name="action" value="updateCdn" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		
		</form>
		</div>
		<?php
	}
	
	
}




// Doooo eeeeeet.

$bm_cdn = new BuzzMedia_CDN_Rewriter();




// Legacy: Just in case these are being used in templates

function image_cdn_rewrite($content) {
	global $bm_cdn;
	return $bm_cdn->image_cdn_rewrite($content);
}

function simple_cdn_rewrite($content) {
	global $bm_cdn;
	return $bm_cdn->simple_cdn_rewrite($content);
}

function get_content_server($uri) {
	global $bm_cdn;
	return $bm_cdn->get_content_server($uri);
}



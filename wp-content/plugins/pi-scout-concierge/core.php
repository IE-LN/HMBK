<?php 
/**
Plugin Name: PI Scout Concierge
Version: 1.2.1
Author: PostIndustria
*/



class pi_scout_concierge {

	const theme_templates_path = 'templates/scout/';

	// Class properties:
	protected static $slug = 'scout-concierge';  // slug for mb label
	protected static $op_tab_slug = 'ice-services';  // slug for tab label
	protected static $version = '1.0-pi'; // version for js and css
	protected static $opt_pre = 'scoutconcierge_';
	protected static $option_defs = array(
		'horizont_domain' => '',
		'show_only_admins' => 0,
        'show_concierge' => 0,
        'imagesize_full' => 'medium',
        'imagesize_thumb' => 'thumbnail',
	); 

	protected static $option_name = 'scoutconcierge-settings'; 
	protected static $settings; 

	public static function pre_init() {
		self::_load_settings();
		add_action('init', array(__CLASS__, 'a_init'), 2);
		add_action('plugins_loaded', array(__CLASS__, 'a_plugins_loaded'), 2);
	}

	public static function  a_init() {
		add_action('admin_init', array(__CLASS__, 'a_admin_init'), 2);
		add_action('admin_menu', array(__CLASS__,'plugin_menu'));
		add_action( 'wp_head', array( __CLASS__, 'head_tags' ),100 );//call after seo
		add_action('pi-concierge-traking', array(__CLASS__, 'a_concierge_traking'), 2);
		add_action('save_post',array(__CLASS__,'a_save_post'));

		if ( empty(self::$settings['show_only_admins']) || (!empty(self::$settings['show_only_admins']) && current_user_can( 'administrator' )) ) {
			add_action( 'wp_enqueue_scripts',   array( __CLASS__, 'a_enqueue_scripts' ) );
			add_action('wp_footer', array(__CLASS__, 'footer_scripts'), 2);
			add_action('ice-add-sailthru-scout-slots', array(__CLASS__, 'render_sailthru_scout_slots'), 2);
		}
	}
    public static function a_enqueue_scripts() {
        wp_enqueue_style( 'pi-scout-concierge-font', plugins_url( 'css/font-awesome.css', __FILE__ ), array(), filesize(dirname(__FILE__)."/css/font-awesome.css"));
		if( file_exists(get_stylesheet_directory().self::theme_templates_path.'scout.css') ){ //Child Theme (or just theme)
			wp_enqueue_style( 'pi-scout-style', get_stylesheet_directory_uri().'/'.self::theme_templates_path.'scout.css', array(), filesize(get_stylesheet_directory_uri().'/'.self::theme_templates_path.'scout.css'));
		}else if( file_exists(get_template_directory().'/'.self::theme_templates_path.'scout.css') ){ //Parent Theme (if parent exists)
			wp_enqueue_style( "pi-scout-style", get_template_directory_uri().'/'.self::theme_templates_path.'scout.css', array(), filesize(get_template_directory_uri().'/'.self::theme_templates_path.'scout.css'));
		}else{ //Default file in plugin folder
			wp_enqueue_style( 'pi-scout-style', plugins_url( 'css/scout.css', __FILE__ ), array(), filesize(dirname(__FILE__)."/css/scout.css"));
		}
    }

	public static function  plugin_menu() {
		add_options_page('Pi-Scout-Concierge','Pi-Scout-Concierge','manage_options','pi-scout-concierge',array(__CLASS__,'mb_edit_settings'));
	}

	public static function a_admin_init() {
	}
	public static function a_plugins_loaded() {
		do_action('allow-media-box-usage', self::$op_tab_slug);
	}

	protected static function _load_settings() {
		$o = wp_parse_args(get_option(self::$option_name), self::$option_defs);
		self::$settings = $o;
	}
	protected static function _save_settings() {
		// make sure that we have at least the default setting for all the options available
		$o = wp_parse_args(self::$settings, self::$option_defs);
		// save the settings to the db in wp_options, option_name = "_ice-commenting-settings"
		update_option(self::$option_name, $o);
		self::$settings = $o;
	}

	// save the settings on the gallery tab of the ICE settings page
	public static function a_process_save_settings() {
		// foreach setting that this plugin conrols, record any changes that may have been made on the settings page
		foreach (self::$option_defs as $key => $val) {
			if (isset($_POST[self::$opt_pre.$key])) self::$settings[$key] = $_POST[self::$opt_pre.$key];
		}
		// allow other plugins to make their changes to the settings if need be
		self::$settings = apply_filters(self::$opt_pre.'save-settings-array', self::$settings);
		// actually save the settings
		self::_save_settings();
	}

	public static function mb_edit_settings() {
		if (!empty($_POST[self::$opt_pre.'save_bt'])) {
			self::a_process_save_settings();
		}
		?>
		<form method="post" method="/wp-admin/options-general.php?page=pi-scout-concierge">
			<h2>Pi Scout Concierge Settings</h2>
			<div style="margin: 10px">
				Horizon Domain
				<input type="text" name="<?php self::$opt_pre ?>horizont_domain" value="<?php self::$settings['horizont_domain']?>" /><br/>
                Show concierge
                <input type="hidden" name="<?php self::$opt_pre ?>show_concierge" value="0"  />
                <input type="checkbox" name="<?php self::$opt_pre ?>show_concierge" value="1" <?php checked(1, !empty( self::$settings['show_concierge'])); ?> /><br/>
				<br/>
                Full Image Size <?php echo self::get_sizes_dropdown_html(self::$opt_pre.'imagesize_full',self::$settings['imagesize_full']);?></br>
                Thumb Image Size <?php echo self::get_sizes_dropdown_html(self::$opt_pre.'imagesize_thumb',self::$settings['imagesize_thumb']);?></br>
				<input type="submit" name="<?php self::$opt_pre ?>save_bt" value="Save" />
			</div>
		</form>
		<?php
	}

	public static function get_sizes_dropdown_html($name, $curent = ''){
		$data = self::list_thumbnail_sizes();
		return self::get_dropdown_html($name,$data,$curent);
	}
	public static function get_dropdown_html($name,$data,$curent){
		ob_start();
		?>
		<select name = "<?php echo $name;?>">
			<?php if(!empty($data) && is_array($data)): ?>
				<?php foreach($data as $value => $title): ?>
					<?php $selected = ($value == $curent) ? 'selected' : '';?>
					<option <?php echo $selected; ?> value="<?php echo $value;?>"><?php echo $title;?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select>
		<?php
		$html = ob_get_clean();
		return $html;
	}
	public static function list_thumbnail_sizes(){
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach( get_intermediate_image_sizes() as $s ){
			$sizes[ $s ] = array( 0, 0 );
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
				$sizes[ $s ] = $s;
			}else{
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
					$sizes[ $s ] = $_wp_additional_image_sizes[ $s ]['width'] . 'x' . $_wp_additional_image_sizes[ $s ]['height'];
			}
		}

		return $sizes;
	}

	protected static function get_tracking_post() {
		global $post, $attach;
		$cpost = $post;
		if ($cpost->post_type == 'gallery') {
			$attachment_post_id = apply_filters('ice-gallery-first-image', $cpost->ID);
			$attachment_post = get_post($attachment_post_id);
			$cpost = $attachment_post;
		}

		return $cpost;
	}

	protected static function get_posts_title($post) {
		if (empty($post->post_title)) {
			return '';
		}
		$title = $post->post_title;
		$title = htmlspecialchars(strip_tags( $title ));
		return $title;
	}

	protected static function get_post_tags($post) {
		$tags = array();
		$tag_objs = wp_get_object_terms( $post->ID, 'post_tag'); 
		if (!empty($tag_objs)) {
			foreach($tag_objs as $obj) {
				$tags[] = $obj->name;
			}
		}
		return $tags;
	}


	protected static function get_head_tags() {

		$cpost = self::get_tracking_post();
		$title = self::get_posts_title($cpost);
		$description = wp_trim_words(htmlspecialchars(strip_tags(strip_shortcodes($cpost->post_content))),apply_filters('excerpt_length', 55),'...');

		$attachmentId = apply_filters('ice-get-post-thumbnail-id', 0, $cpost->ID);
		if (empty($origimage)) {
			$attachmentId = self::get_image_from_post();
		}
		$origimage = wp_get_attachment_image_src( $attachmentId, self::$settings['imagesize_full'] );
		$thumb = wp_get_attachment_image_src($attachmentId, self::$settings['imagesize_thumb']);

		$tags = self::get_post_tags($cpost);

		if (!empty($cpost->post_date)) {
			$data['sailthru.date'] = $cpost->post_date;
		}
		if (!empty($title)) {
			$data['sailthru.title'] = $title;
		}
		if (!empty($tags) && is_array($tags)) {
			$data['sailthru.tags'] = implode($tags,',');
		}
		if (!empty($origimage[0])) {
			$data['sailthru.image.full'] = $origimage[0];
		}
		if (!empty($thumb[0])) {
			$data['sailthru.image.thumb'] = $thumb[0];
		}
		if (!empty($description)) {
			$data['sailthru.description'] = $description;
		}
		$meta = "\n";
		if (!empty($data)) {
			foreach($data as $name => $content) {
				$meta .= '<meta name="' . $name . '" content="' . $content . '" />'."\n";
			}
		}
		return $meta;
	}

	public static function a_save_post($post_id){
		wp_cache_delete('sailthru_head_tag_'.$post_id);
		return $post_id;
	}

	public static function head_tags() {
		if (is_single()) {
			global $post;
			$cache_key = 'sailthru_head_tag_'.$post->ID;
			$cache = wp_cache_get($cache_key);
			if($cache === false || !empty($_GET['clear_cache'])) {
				$meta = self::get_head_tags();
				wp_cache_set($cache_key,$meta);
				$cache = $meta;
			}
			echo $cache; 
		}
	}

	protected static function get_image_from_post() {
		global $post, $attach;
		$image_id = get_post_thumbnail_id($post->ID);
		if (empty($image_id)) {
			if ($post->post_type == 'attachment') {
				$image_id = $post->ID;
			} elseif($post->post_type == 'post') {
				$args = array(
					'post_status' => 'inherit',
					'numberposts' => 1, // (-1 for all)
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'post_parent' => $post->ID,
				);
				$attachments = get_posts($args);
				if (!empty($attachments[0])) {
					$image_id = $attachments[0]->ID;
				}
			}
		}
		return $image_id;
	}

    public static function a_concierge_traking() {
		if ( $_GET['concierge'] == 'true' ) { ?>
			_gaq.push(['_setCustomVar', 21, 'sailthru-concierge', 'article', 2]);
		<?php }
		if ( $_GET['sailthru'] == 'true' ) {
			if ( $_GET['sailthru_position'] == 'article_bottom' ) {
				$sailthru_position = 'article_bottom';
			} else if ( $_GET['sailthru_position'] == 'footer' ) {
				$sailthru_position = 'footer';
			} else {
				$sailthru_position = 'unknown';
			} ?>
			_gaq.push(['_setCustomVar', 20, 'sailthru', 'scout-<?php echo $sailthru_position ?>', 2]);
		<?php } 
    }

	public static function footer_scripts() {
		?>
			<script type="text/javascript">
			// custom vars set based on query string
				(function() {
					function loadHorizon() {
						var s = document.createElement('script');
						s.type = 'text/javascript';
						s.async = true;
						s.src = location.protocol + '//ak.sail-horizon.com/horizon/v1.js';
						var x = document.getElementsByTagName('script')[0];
						x.parentNode.insertBefore(s, x);
					}

					loadHorizon();
					var oldOnLoad = window.onload;
					window.onload = function() {
						if (typeof oldOnLoad === 'function') {
							oldOnLoad();
						}
						Sailthru.setup({
							domain: '<?php echo self::$settings['horizont_domain'];?>'
							<?php if( !empty(self::$settings['show_concierge']) && is_single()) {?>
								,concierge: { from: 'top', threshold: 400 } 
							<?php } ?> 
						});
					};
				})();
			</script>

			<script>
				(function($){
				var templateCache = {};
				$.fn.template = function tmpl(template, data){
					return this.html($.template(template, data)); 
				}
				$.template = function tmpl(template, data){
					try {
					var fn = !/\W/.test(template) ?
					templateCache[template] = templateCache[template] ||
						tmpl(document.getElementById(template).innerHTML) :
					new Function("obj",
						"var p=[],print=function(){p.push.apply(p,arguments);};" +
						"with(obj){p.push('" +
						template
						.replace(/[\r\t\n]/g, "")
						.split("[!").join("\t")
						.replace(/((^|!\])[^\t]*)'/g, "$1\r")
						.replace(/\t=(.*?)!\]/g, "',$1,'")
						.split("\t").join("');")
						.split("!]").join("p.push('")
						.split("\r").join("\\'")
					+ "');}return p.join('');");
					return data ? fn( data ) : fn;
					} catch (e) {
						//in case the template script is not in the output, we don't want all js execution to stop 
					}
				};
				})(jQuery);
			</script>

			<script type="text/javascript">

            (function() {

                var script = document.createElement("script");
                script.async = true;
                script.src = "//ak.sail-horizon.com/scout/v1.js";
                var s = document.getElementsByTagName("script")[0];

                script.onload = function(){
                    var articleSlots = jQuery(".sailthruScout");

                    var articleCount = 0;
                    var articleRequestCount = articleSlots.length + 5; // 5 for buffer

                    SailthruScout.setup({
                        domain: "<?php echo self::$settings['horizont_domain'];?>",
                        numVisible: articleRequestCount,

                        renderItem: function(item, pos) {

                            if ( !item.image.full ) {
                                return;
                            }

                            // Modify the url to have a special GET parameter
                            item.url = item.url + '?sailthru=true&sailthru_position=' +  jQuery(articleSlots[articleCount]).data('position');

                            if(articleSlots.length == 9){
                                item.url += '&sailthru_name=' + jQuery(articleSlots[articleCount]).data('name') + '&sailthru_value=' +  jQuery(articleSlots[articleCount]).data('value');
                            }

                            var html = '';
                            html = jQuery.template("templateSailthruScout", item); // this template is in the footer for now
                            jQuery(articleSlots[articleCount]).html(html);
                            articleCount++;
                        }
                    });
                };
                s.parentNode.insertBefore(script, s);
            })();

			</script>

		<?php
	}

	public static function render_sailthru_scout_slots() {
		//<article class="article"><div class="image_container" style="background:url(http://cdn02.cdnwp.celebuzz.com/wp-content/uploads/2013/06/18/Katy-Perry-Vogue-4-220x165.jpg) no-repeat; background-size: cover; "><a href="http://www.celebuzz.com/2013-06-18/katy-perry-russell-brand-asked-for-divorce-via-text-message?sailthru=true&amp;sailthru_position=article_right_rail" class="link"><img src="http://cdn02.cdnwp.celebuzz.com/wp-content/uploads/2013/06/18/Katy-Perry-Vogue-4-220x165.jpg" class="image article_linked " alt="Katy Perry: Russell Brand Asked For Divorce Via Text Message"></a></div><h3 class="title"><a href="http://www.celebuzz.com/2013-06-18/katy-perry-russell-brand-asked-for-divorce-via-text-message?sailthru=true&amp;sailthru_position=article_right_rail" class="link">Katy Perry: Russell Brand Asked For Divorce Via Text Message</a></h3></article>
		$template = self::get_template('scout.php');
		if (!empty($template)) {
			ob_start();
			include $template;
			$content = ob_get_clean();
			echo $content;
		}
		
	}

	public static function get_template($name,$plugin_path = 'templates') {
		$path = locate_template(self::theme_templates_path . '/' . $name);
		if (empty($path)) {
			$path = dirname(__FILE__) . '/' . $plugin_path . '/' . $name;
		}
		if (file_exists($path)) {
			return $path;
		}
		return false;
	}
}

	
if (defined('ABSPATH') && function_exists('add_action')) {
	pi_scout_concierge::pre_init();
}

<?php (__FILE__ == $_SERVER['SCRIPT_FILENAME']) ? die(header('Location: /')) : null;

/*
Plugin Name: PI Onswipe Feed
Version: 1.5.1
Author: Postindustria
*/

class pi_feed{
    protected static $pi_feed_slug = 'pi-feed';
    
    protected static $img_first = 'attach';
    protected static $img_size  = 'full';
    protected static $fields    = array();

    public static function pre_init() {
        add_action('do_feed_onswipefeed', array(__CLASS__,'feed_onswipefeed'), 10, 1);
        add_action('init', array(__CLASS__, 'a_init'));
        
        add_action('admin_menu', array(__CLASS__,'options_page'));
    }

    public static function options_page(){
        add_options_page('PI Onswipe Feed', 'PI Onswipe Feed', 8, 'onswipe-feed', array('pi_feed', 'show_options_page'));    
    }
    
    public static function show_options_page(){
        $is_ice = is_plugin_active('bm-ice/bm-ice-deps.php');
        global $_wp_additional_image_sizes;
        $wp_sizes = get_intermediate_image_sizes();
        $sizes = array('full' => 'full');
        foreach($wp_sizes as $s ){
            if(in_array($s, array('thumbnail', 'medium', 'large'))){
                $sizes[$s] = $s;
            }
            else{
                if(isset($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$s]))
                    $sizes[$_wp_additional_image_sizes[$s]['width'].'x'.$_wp_additional_image_sizes[$s]['height']] = array($_wp_additional_image_sizes[$s]['width'], $_wp_additional_image_sizes[$s]['height']);
            }              
        }
        
        $options   = get_option(self::$pi_feed_slug);
        $img_desc  = isset($options['of_desc']) && !empty($options['of_desc']) ? $options['of_desc'] : 'desc';
        $img_size  = isset($options['of_size']) && !empty($options['of_size']) ? $options['of_size'] : 'full';
        $img_first = isset($options['of_first_img']) && !empty($options['of_first_img']) ? $options['of_first_img'] : 'attach';
        $fields    = isset($options['fields']) && !empty($options['fields']) ? $options['fields'] : array();
            
        if(is_array($img_size)){
            $img_size = implode('x', $img_size);    
        } 
              
        if(isset($_POST['onswipe_feed_submit'])){
            if(isset($_POST['of_ck_field']) && !empty($_POST['of_ck_field'])){
                $fields = array();
                foreach($_POST['of_ck_field'] as $field){
                    $value = $_POST['of_'.$field];
                    $value2 = '';
                    if($value == 'post_meta')
                        $value2 = $_POST['of_'.$field.'_meta_key'];
                        
                    $fields[$field] = array(
                        'value'  => $value,
                        'value2' => $value2
                    );        
                }
            }else{
                $fields = array();    
            }
            
            $img_desc  = isset($_POST['of_desc']) && !empty($_POST['of_desc']) ? $_POST['of_desc'] : 'desc';
            $img_first = isset($_POST['of_first_img']) && !empty($_POST['of_first_img']) ? $_POST['of_first_img'] : 'attach';
            
            if(isset($_POST['of_img_size']) && isset($sizes[$_POST['of_img_size']])){
                $img_size_value = $sizes[$_POST['of_img_size']]; 
            }
            
            $img_size = $img_size_value;
            if(is_array($img_size_value)){
                $img_size = implode('x', $img_size_value);    
            }
            
            update_option(self::$pi_feed_slug, array('of_desc' => $img_desc, 'of_size' => $img_size_value, 'of_first_img' => $img_first, 'fields' => $fields));
            echo '<div id="message" class="updated fade"><p><strong>Onswipe Feed Options updated</strong></p></div>';
        }
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"><br/></div>
                <h2>Onswipe Feed Options</h2>
                <form action="options-general.php?page=onswipe-feed" method="post">
                <table>
                    <tr>
                        <td style="width: 120px;"><label>First Image:</label></td>
                        <td>
                            <select name="of_first_img" <?php if($is_ice) echo 'disabled="disabled"'?>>
                                <option value="attach" <?php if($img_first == 'attach') echo 'selected="selected"';?>>Post Attachments</option>
                                <option value="featured" <?php if($img_first == 'featured') echo 'selected="selected"';?>>Featured Image</option>
                                <option value="shortcode" <?php if($img_first == 'shortcode') echo 'selected="selected"';?>>Gallery Shortcode</option>
                                <option value="insertedimg" <?php if($img_first == 'insertedimg') echo 'selected="selected"';?>>First Inserted Image</option>
                            </select>
                            <?php if($is_ice):?>
                            <label>Only for Non-ICE sites</label>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Image Size:</label></td>
                        <td>
                            <select name="of_img_size">
                                <?php foreach($sizes as $key=>$item) : ?>
                                    <option value="<?php echo $key; ?>" <?php if($img_size == $key) echo 'selected="selected"';?>><?php echo $key; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><br/>
                        <table>
                        <?php self::render_field_options('Image Title',  'img_title', $fields);?>
                        <?php self::render_field_options('Photo Credit', 'img_pc', $fields);?>
                        <?php self::render_field_options('Image Caption','img_caption', $fields);?>
                        </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="onswipe_feed_submit" value="1" />
                            <input type="submit" class="button-primary" value="Update options">
                        </td>
                    </tr>
                </table>
                </form>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('.of_select').on('change', function(){
                            $('#'+this.id+'_pm').hide();
                            if(this.value == 'post_meta'){
                                $('#'+this.id+'_pm').show();
                            }  
                        });            
                    });            
                </script>        
        </div>
        <?php    
    }
    
    private static function render_field_options($text, $name, $fields){
        $use = false; $value = 'title'; $value2 = '';
        if(isset($fields[$name])) $use = true;
        if(!empty($fields[$name]['value']))  $value = $fields[$name]['value'];
        if(!empty($fields[$name]['value2'])) $value2 = $fields[$name]['value2'];
        
        ?>
            <tr>
                <td><input <?php if($use) echo 'checked="checked"';?> type="checkbox" name="of_ck_field[]" value="<?php$name?>"></td>
                <td><label><?php echo $text;?>:</label></td>
                <td>
                    <select name="of_<?php$name?>" id="of_<?php$name?>" class="of_select">
                        <option value="title" <?php if($value == 'title') echo 'selected="selected"';?>>Title</option>
                        <option value="desc" <?php if($value == 'desc') echo 'selected="selected"';?>>Description</option>
                        <option value="caption" <?php if($value == 'caption') echo 'selected="selected"';?>>Caption</option>
                        <option value="post_meta" <?php if($value == 'post_meta') echo 'selected="selected"';?>>Post Meta</option>
                    </select>
                </td>
                <td>
                    <div id="of_<?php$name?>_pm" style="<?php if($value != 'post_meta') echo 'display: none;';?>">
                        <input name="of_<?php$name?>_meta_key" type="text" style="width: 150px;" value="<?php$value2?>">
                    </div>
                </td>
            </tr>
        <?php  
    }
    
    public static function a_init(){
        add_feed('onswipe', array(__CLASS__,'feed_onswipefeed'));
    }

    public static function activate(){
        add_feed('onswipe', array(__CLASS__,'feed_onswipefeed'));
        flush_rewrite_rules();
    }

    public static function deactivate(){
        flush_rewrite_rules();
    }

    public static function feed_onswipefeed(){
        self::init_options();
        
        remove_all_filters('the_content_feed');
        remove_all_filters('the_excerpt_rss');
        remove_all_filters('the_content');
       
        //Wordpress hooks
        foreach ( array( 'the_content', 'the_title' ) as $filter )
            add_filter( $filter, 'capital_P_dangit', 11 );
        
        add_filter( 'the_content', 'convert_smilies'    );
        add_filter( 'the_content', 'convert_chars'      );
        add_filter( 'the_content', 'wpautop'            );
        add_filter( 'the_content', 'shortcode_unautop'  );
        add_filter( 'the_content', 'prepend_attachment' );
        
        add_filter( 'the_excerpt_rss',    'convert_chars'   );
        add_filter( 'the_excerpt_rss',    'ent2ncr',      8 );
        
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        //Plugin hooks
        if(is_plugin_active('bm-ice/bm-ice-deps.php'))
            add_filter('the_content_feed', array(__CLASS__,'embed_content_feed'), 99);  
        else
            add_filter('the_content_feed', array(__CLASS__, 'render_gallery_images'), 11);
            
        add_filter('the_content_feed', array(__CLASS__, 'remove_shortcodes'), 12);
        add_filter('the_content_feed', array(__CLASS__, 'catch_that_href'), 99);
        
        load_template( dirname(__FILE__). '/templates/feed-rss2.php' );     
    }
    
    public static function render_gallery_images($content){
        global $post, $first_img_url;
        $first_img_url = '';
        $post_id = $post->ID;
        $attachments = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
        if(count($attachments) > 0){
            //Gallery Edit option title
            $gallery_options = get_post_meta($post_id, 'gallery_options', true);
            $show_title = true;
            if(isset($gallery_options['display_title'])){
                $show_title = !empty($gallery_options['display_title']) ? true : false;            
            }
            
            $first_img = ''; $first_img_id = 0;
            if(self::$img_first == 'featured'){
                $fid = get_post_thumbnail_id($post_id);
                if(!empty($fid)){
                    $first_img_id = $fid;
                }
            }elseif(self::$img_first == 'shortcode'){
               
                $pattern = "\[(\[?)gallery(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)";
                preg_match_all("/$pattern/s", $content, $mshort);
                if(isset($mshort[2][0]) && !empty($mshort[2][0])){
                    $a_split = split('=', $mshort[2][0]); 
                    if(isset($a_split[1]) && !empty($a_split[1])){
                        $str = str_replace('"', '', $a_split[1]);
                        $ids = explode(',', $str);
                        if(!empty($ids[0]) && is_numeric($ids[0]) ){
                            $first_img_id = $ids[0];
                        }
                    }
                }
            }elseif(self::$img_first == 'attach'){
                $temp = array_shift(array_values($attachments));    
                if(!empty($temp) && isset($temp->ID))
                    $first_img_id = $temp->ID;       
            }
            
            //remove all images
            preg_match_all('/(<a[^>]+>)? *(<img[^>]+src=[\'"][^\'"]+[\'"][^>]*>) *(< *\/ *a>)?/i', $content, $matches);
            if(isset($matches[0]) && isset($matches[2]) && !empty($matches[2])){
                foreach ( $attachments as $s_id => $s_img ){
                    $search_img = $s_img->post_name;
                    $img_meta = get_post_meta($s_id, '_wp_attached_file', true);
                    if(!empty($img_meta)){
                        $search_img = $img_meta;    
                        $last_dot = strrpos($img_meta, '.');
                        if($last_dot !== false)
                            $search_img = substr($img_meta, 0, $last_dot);
                    } 
                    
                    foreach($matches[2] as $key => $p_img){
                        if(strpos($p_img, $search_img) !== false){
                            //first img
                            if($key == 0 && empty($first_img_id) && self::$img_first == 'insertedimg')
                                $first_img_id = $s_id;
                            
                            $content = str_replace($matches[0][$key], '', $content);    
                        }
                    }    
                }    
            }
            
            //add attach images    
            $attachments = apply_filters('onswipe-feed-attachments', $attachments);
            
            if(!empty($first_img_id)){
                $first_img = get_post($first_img_id);// !empty($attachments[$first_img_id]) ? $attachments[$first_img_id] : '';
                if(!empty($first_img)){
                    $first_img = wp_get_attachment_image($first_img->ID, self::$img_size); 
                }    
            }else{
                $temp = array_shift(array_values($attachments));    
                if(!empty($temp) && isset($temp->ID)){
                    $first_img_id = $temp->ID;
                    $first_img = get_post($first_img_id);// !empty($attachments[$first_img_id]) ? $attachments[$first_img_id] : '';
                    if(!empty($first_img)){
                        $first_img = wp_get_attachment_image($first_img->ID, self::$img_size); 
                    }               
                }
            }
            
            if(!empty($first_img)){
                $first_img_url = wp_get_attachment_url($first_img_id);   
            }
            
            $g_content = '';
            foreach ( $attachments as $id => $img ){
                //if($first_img_id == $id) continue;
                
                $img_src  = wp_get_attachment_image($id, self::$img_size);
                $img_info = self::render_image_fields($img, $show_title);
                
                $g_content .= $img_src.$img_info;    
            }
            
            $content = $first_img . $content . $g_content;
        }
        
        return $content;    
    }
    
    public static function embed_content_feed($content){
        global $post, $first_img_url;
        $first_img_url = '';
        $post_id = $post->ID;
        
        if($post->post_type == 'video-post'){
            $video_embed = get_post_meta($post_id, '_bm_video_embed', true);    
            return $video_embed.$content;
        }    
        $first_img_id = 0;$first_img = '';
        if($post->post_type == 'gallery'){
            $attachments = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
            if(count($attachments) > 0){
                $attachments = apply_filters('onswipe-feed-attachments', $attachments);
                $g_content = ''; 
                
                $temp = array_shift(array_values($attachments));    
                if(!empty($temp) && isset($temp->ID))
                    $first_img_id = $temp->ID;
                if(!empty($first_img_id)){
                    $first_img = get_post($first_img_id);//
                    if(!empty($first_img)){
                        $first_img = wp_get_attachment_image($first_img->ID, self::$img_size); 
                    }    
                }
                
                if(!empty($first_img)){
                    $first_img_url = wp_get_attachment_url($first_img_id);   
                }
            
                foreach ( $attachments as $id => $img ){
                    $img_src  = wp_get_attachment_image($id, self::$img_size);
                    $img_info = self::render_image_fields($img, true);
                    
                    $g_content .= $img_src.$img_info;    
                }
                
                $content = $first_img . $content . $g_content;
            }
        
            return $content; 
        }
               
        $type = get_post_meta($post_id, '_story_layout_type', true);
        if($type == 'story'){
            $emded_options = get_post_meta($post_id,'_bm_main_embed_post_settings',true);
            if($emded_options['embed_type'] == 'bmice_core_layout_mainembed_types_photo'){
                $img_id = get_post_meta($post_id, '_thumbnail_id', true);
                if(!empty($img_id)){
                    $img_src = wp_get_attachment_image($img_id, self::$img_size);
                    $content = $img_src.$content; 
                    
                    if(!empty($img_id)){
                        $first_img_url = wp_get_attachment_url($img_id);   
                    }   
                }
                            
            }elseif($emded_options['embed_type'] == 'bmice_core_gallery_embed'){
                $gid = get_post_meta($post_id, '_bm_main_embed_post_id', true);
                if(!empty($gid)){
                    $images = apply_filters('get-gallery-images', $gid);
                    $g_content = '';
                    
                    $temp = array_shift(array_values($images));    
                    if(!empty($temp) && isset($temp->ID))
                        $first_img_id = $temp->ID;
                    if(!empty($first_img_id)){
                        $first_img = get_post($first_img_id);//
                        if(!empty($first_img)){
                            $first_img = wp_get_attachment_image($first_img->ID, self::$img_size); 
                        }    
                    }
                    
                    if(!empty($first_img)){
                        $first_img_url = wp_get_attachment_url($first_img_id);   
                    }
                    
                    foreach($images as $img){
                        $img_src  = wp_get_attachment_image($img->ID, self::$img_size);
                        $img_info = self::render_image_fields($img, true);
                        
                        $g_content .= $img_src.$img_info;    
                    }
                    
                    $content = $first_img . $content. $g_content;
                }    
            }elseif($emded_options['embed_type'] == 'bmice_core_posts_videoembed'){
                $vid = get_post_meta($post_id, '_bm_main_embed_post_id', true);
                $video_embed = '';
                if(!empty($vid))
                    $video_embed = get_post_meta($vid, '_bm_video_embed', true);
                    
                $content = $video_embed.$content;    
            }              
        }elseif($type == 'medium'){
            $img_id = get_post_meta($post_id, '_thumbnail_id', true);
            if(!empty($img_id)){
                $img_src = wp_get_attachment_image($img_id, self::$img_size);
                $content = $img_src.$content;
                
                if(!empty($img_id)){
                    $first_img_url = wp_get_attachment_url($img_id);   
                }    
            }    
        }

        return $content;    
    }
    
    private static function render_image_fields($img, $show_title){
        $content = '';
       
        foreach(self::$fields as $key => $field){
            switch($field['value']){
                case 'title':{
                    $content .= $show_title && !empty($img->post_title) ? '<p>'.$img->post_title.'</p>' : '';
                    break;
                }
                case 'desc':{
                    $content .= !empty($img->post_content) ? '<p>'.$img->post_content.'</p>' : '';
                    break;
                }
                case 'caption':{
                    $content .= !empty($img->post_excerpt) ? '<p>'.$img->post_excerpt.'</p>' : '';
                    break;
                }
                case 'post_meta':{
                    $meta_value = get_post_meta($img->ID, $field['value2'], true);
                    $content .= !empty($meta_value) ? '<p>'.$meta_value.'</p>' : '';                           
                    break;
                }
            }
        }
        
        return $content;    
    }
    
    private static function init_options(){
        
        $options   = get_option(self::$pi_feed_slug);
        self::$img_size  = isset($options['of_size']) && !empty($options['of_size']) ? $options['of_size'] : 'full';
        self::$img_first = isset($options['of_first_img']) && !empty($options['of_first_img']) ? $options['of_first_img'] : 'attach';
        self::$fields    = isset($options['fields']) && !empty($options['fields']) ? $options['fields'] : array();

    }  
    
    public static function remove_shortcodes($content){
        if (!is_feed()) return $content;
        //remove shortcods from content
        global $shortcode_tags;
        if (!empty($shortcode_tags) && is_array($shortcode_tags))
        {
            $pattern = get_shortcode_regex();
            $content = preg_replace("/$pattern/s", '', $content);
        }
        return $content;    
    }
    
    public static function catch_first_img($content){
        preg_match_all('/(<a[^>]+>)? *(<img[^>]+src=[\'"][^\'"]+[\'"][^>]*>) *(< *\/ *a>)?/i', $content, $matches);
        
        $first_img = $matches[0][0];
        if(!empty($first_img)){
            $content = str_replace($first_img, '', $content);    
        }
        return $content;
    }
    
    public static function catch_that_href($content){
        preg_match_all('/(<a[^>]+>)? *(<img[^>]+src=[\'"][^\'"]+[\'"][^>]*>) *(< *\/ *a>)?/i', $content, $matches);
        $aImgs = isset($matches[2]) && !empty($matches[2]) ? $matches[2] : array();
        foreach($aImgs as $key => $item){
            if(!empty($item) && isset($matches[0][$key]))
                $content = str_replace($matches[0][$key], $item, $content);
        }

        return $content;
    }
}


register_activation_hook(__FILE__, array('pi_feed', 'activate'));
register_deactivation_hook(__FILE__, array('pi_feed', 'deactivate'));

if (defined('ABSPATH') && function_exists('add_action')) {
    pi_feed::pre_init();
}

?>
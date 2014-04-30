<?php
/*
Plugin Name: Buzzworthy
Description: Displays related articles from Buzzworthy
Author: Spinmedia
Version: 1.0
*/

 
class BuzzworthyWidget extends WP_Widget
{

  //
  // JavaScript in the <head>
  //
  public static function header_changes() 
  {
  
      ?> 
        <script type="text/javascript" src="/wp-content/plugins/buzzworthy/buzzworthy.js?v=2"></script>
        <link rel="stylesheet" href="/wp-content/plugins/buzzworthy/buzzworthy.css" type="text/css" media="screen" />
      <?php
  }

  function BuzzworthyWidget()
  {
    $widget_ops = array('classname' => 'BuzzworthyWidget', 'description' => 'Displays related stories as determined by Buzzworthy' );
    $this->WP_Widget('BuzzworthyWidget', 'Buzzworthy', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }

 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;


 
    ?>

      <script type="text/javascript">
        console.log('oohhh');
      </script>
    <?php


  
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("BuzzworthyWidget");') );

// Site Modifications
//add_action('wp_head', array('BuzzworthyWidget', 'header_changes'));
add_action('init', 'buzzworthy_init');
function buzzworthy_init(){
    wp_enqueue_script('buzzworthy', plugins_url('buzzworthy.js', __FILE__), array(), '1.0.1');
    wp_enqueue_style('buzzworthy', plugins_url( 'buzzworthy.css', __FILE__ ) );
}



function buzzworthy_getblock(){
  $options = get_option('buzzworthy_options');
  $buzzworthy_sitename = $options['sitename'];
  return "<div id='buzzworthy'>".$buzzworthy_sitename."</div>";
}



// Shortcode implementation
function buzzworthy_shortcode($attribs) {
  $buzzworthy_block = buzzworthy_getblock();
  return $buzzworthy_block;
}

function buzzworthy() {
  echo buzzworthy_getblock();
}


add_shortcode('buzzworthy', 'buzzworthy_shortcode');


if(is_admin()) {

  // add the admin options page
  add_action('admin_menu', 'buzzworthy_admin_add_page');
  function buzzworthy_admin_add_page() {
    add_options_page('BuzzWorthy Page', 'BuzzWorthy', 'manage_options', 'buzzworthy', 'buzzworthy_options_page');
  }

  // display the admin options page
  function buzzworthy_options_page() {  ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div><h2>BuzzWorthy Settings</h2>
    
    <form action="options.php" method="post">
    <?php settings_fields('buzzworthy_options'); ?>
    <?php do_settings_sections('buzzworthy'); ?>

    <br /><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
    
    
    </div>
    <?php
  }
  
  
  // add the admin settings and such
  add_action('admin_init', 'buzzworthy_admin_init');
  function buzzworthy_admin_init(){
    register_setting( 'buzzworthy_options', 'buzzworthy_options', 'buzzworthy_options_validate' );
    add_settings_section('buzzworthy_main', 'Main Settings', 'buzzworthy_section_text', 'buzzworthy');
    add_settings_field('buzzworthy_sitename', 'BuzzWorthy Sitename', 'buzzworthy_setting_string', 'buzzworthy', 'buzzworthy_main');
  }

  function buzzworthy_section_text() {
    // echo '<p>Main description of this section here.</p>';
  }
  
  function buzzworthy_setting_string() {
    $options = get_option('buzzworthy_options');
    echo "<input id='buzzworthy_text_string' name='buzzworthy_options[sitename]' size='60' type='text' value='{$options['sitename']}' />";
  }
  
  // validate our options
  function buzzworthy_options_validate($input) {
    $newinput['sitename'] = trim($input['sitename']);
    // ?? preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)
    return $newinput;
  }

}


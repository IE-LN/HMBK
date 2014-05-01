<?php
/*
Plugin Name: Pi Login
Version: 1.2.9
Author: Postindustria git s
*/

class PiLogin {

	const template_wiget_in = 'widget_in.php';
	const template_wiget_out = 'widget_out.php';
	const theme_templates_path = 'pi-login/';

	public static function init() {
		$plugin_url = path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) ));
		if( file_exists(get_stylesheet_directory().'/plugins/pi-login/pi-login.css') ){ //Child Theme (or just theme)
			wp_enqueue_style( "pi-login", get_stylesheet_directory_uri().'/plugins/pi-login/pi-login.css' );
		}else if( file_exists(get_template_directory().'/plugins/pi-login/pi-login.css') ){ //Parent Theme (if parent exists)
			wp_enqueue_style( "pi-login", get_template_directory_uri().'/plugins/pi-login/pi-login.css' );
		}else{ //Default file in plugin folder
			wp_enqueue_style( "pi-login", $plugin_url."/pi-login.css" );
		}
		add_action('wp_enqueue_scripts', array(__CLASS__,'print_scripts'));
		add_action('wp_ajax_nopriv_pilogin_login', array(__CLASS__,'login_ajax_process_request'));
		add_action('wp_ajax_nopriv_pilogin_register', array(__CLASS__,'register_ajax_process_request'));
		add_action('wp_ajax_nopriv_pilogin_forgot', array(__CLASS__,'forgot_ajax_process_request'));
		add_action('wp_ajax_pilogin_logout', array(__CLASS__,'logout_ajax_process_request'));

        add_action('wp_ajax_pilogin_login', array(__CLASS__,'login_ajax_process_request'));
        add_action('wp_ajax_pilogin_register', array(__CLASS__,'register_ajax_process_request'));
        add_action('wp_ajax_pilogin_forgot', array(__CLASS__,'forgot_ajax_process_request'));
        add_action('wp_ajax_nopriv_pilogin_logout', array(__CLASS__,'logout_ajax_process_request'));

		add_action('pi-login-widget',array(__CLASS__,'widget'));
		add_filter('pi-login-get-sign-in',array(__CLASS__,'get_sign_in'));
		add_filter('pi-login-get-sign-up',array(__CLASS__,'get_sign_up'));
		add_filter('pi-login-get-sign-out',array(__CLASS__,'get_sign_out'));

        //reset pass
        add_filter('rewrite_rules_array', array(__CLASS__, 'create_rewrite_rules'));
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));
        add_action('template_redirect', array(__CLASS__, 'template_redirect_resetpassword') );

        add_action('login_init', array(__CLASS__, 'no_wp_login'));
        add_action('wp_head', array(__CLASS__, 'action_wp_head_login'), 1);
        add_filter('logout_url', array(__CLASS__, 'set_redirect_url'), 10, 2);
	}

	public static function get_sign_in($text = '') {
		if (empty($text)) {
			$text = 'Sign in';
		}
		$link = '<a class="sign-in-link" href="#">'.$text.'</a>';
		return $link;
	}

	public static function get_sign_up($text = '') {
		if (empty($text)) {
			$text = 'Join';
		}
		$link = '<a class="sign-up-link" href="#">'.$text.'</a>';
			return $link;
	}

	public static function get_sign_out($text = '') {
		if (empty($text)) {
			$text = 'Sign out';
		}
		$link = '<a class="logout-link" href="#">'.$text.'</a>';
			return $link;
	}

	public static function print_scripts() {
		wp_enqueue_script( 'pi-login', plugins_url( '/pi-login.js' , __FILE__ ), array( 'jquery', 'jquery-ui-dialog' ), '7' );
		wp_localize_script( 'pi-login', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	public static function widget() {
		if (is_user_logged_in()) {
			$template_name = self::template_wiget_in;
		} else {
			$template_name = self::template_wiget_out;
		}

		$template_path = self::get_template($template_name);
		if (!empty($template_path)) {
			ob_start();
			include $template_path;
			$template = ob_get_clean();

			echo $template;
		}
	}

	public static function get_template($name) {
		$path = locate_template(self::theme_templates_path . '/' . $name);
		if (empty($path)) {
			$path = dirname(__FILE__) . '/' . 'templates' . '/' . $name;
		}
		if (file_exists($path)) {
			return $path;
		}
		return false;
	}

	public static function print_dialogs() {
        $redirect_url = isset($_GET['redirect_to']) && !empty($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
		$template_name = 'dialogs.php';
		$template = self::get_template($template_name);
		if (!empty($template)) {
			ob_start();
			include $template;
			$content = ob_get_clean();

			echo $content;
		}
	}

	function login_ajax_process_request() {
		if ( isset( $_POST["PiLoginName"] ) && isset( $_POST["PiLoginPass"] ) ) {
			$signin_data = array(
				'user_login' => $_POST["PiLoginName"],
				'user_password' => $_POST["PiLoginPass"],
				'remember' => !empty( $_POST["PiLoginRemember"]),
			);

			$signin_result = wp_signon($signin_data);
			if ($signin_result instanceof WP_User) {
				echo json_encode(array('status' => 'ok', 'data' => 'ok'));
				die();
			} elseif ($signin_result instanceof WP_Error){
				$error_code = $signin_result->get_error_code();
				switch ($error_code){
								case 'incorrect_password':
								case 'invalid_username':
												$errmsg = '<strong>ERROR</strong>: The username or password you entered is incorrect. <a class="forgot-link" href="'.home_url('reset-password/?action=lostpassword').'" title="Password Lost and Found" onClick="javascript:openForgotDialog();return false;">Lost your password</a>?';
												break;
								case '':
												$errmsg = '<strong>ERROR</strong>: The username or password can\'t be empty. Please enter your username and password?';
												break;
								default:
												$errmsg = $signin_result->get_error_message();
												break;
				}
				echo json_encode(array(
					'status' => 'error',
					'data' => $errmsg,
				));
				die();
			}
		}
	}

	function logout_ajax_process_request() {
			wp_logout();
			echo json_encode(array('status' => 'ok', 'data' => 'ok'));
			die();
	}

	function register_ajax_process_request() {
			$signup_result = add_user();
			if (is_int($signup_result)) {
				wp_set_auth_cookie( $signup_result, true );
				echo json_encode(array('status' => 'ok', 'data' => 'Registration complete'));
				die();
			} elseif ($signup_result instanceof WP_Error){
				echo json_encode(array(
					'status' => 'error',
					'data' => $signup_result->get_error_message(),
				));
				die();
			}
	}

	function forgot_ajax_process_request() {
		if ( isset( $_POST["PiLoginEmail"] ) ) {
		    $result = self::retrieve_password();
            if($result instanceof WP_Error){
                echo json_encode(array(
                    'status' => 'error',
                    'data' => $result->get_error_message(),
                ));
                die();
            }else{
                echo json_encode(array('status' => 'ok', 'data' => 'Check your e-mail for the confirmation link.'));
                die();
            }

		} else {
			echo json_encode(array(
				'status' => 'error',
				'data' => 'All fields are required',
			));
			die();
		}
	}

    protected function retrieve_password() {
        global $wpdb, $current_site;

        $errors = new WP_Error();

        if ( empty( $_POST['PiLoginEmail'] ) ) {
            $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
        } else if ( strpos( $_POST['PiLoginEmail'], '@' ) ) {
            $user_data = get_user_by( 'email', trim( $_POST['PiLoginEmail'] ) );
            if ( empty( $user_data ) )
                $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
        } else {
            $login = trim($_POST['PiLoginEmail']);
            $user_data = get_user_by('login', $login);
        }

        do_action('lostpassword_post');

        if ( $errors->get_error_code() )
            return $errors;

        if ( !$user_data ) {
            $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
            return $errors;
        }

        // redefining user_login ensures we return the right case in the email
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        do_action('retrieve_password', $user_login);

        $allow = apply_filters('allow_password_reset', true, $user_data->ID);

        if ( ! $allow )
            return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
        else if ( is_wp_error($allow) )
            return $allow;

        $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
        if ( empty($key) ) {
            // Generate something random for a key...
            $key = wp_generate_password(20, false);
            do_action('retrieve_password_key', $user_login, $key);
            // Now insert the new md5 key into the db
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
        }
        $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . network_site_url("reset-password/?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
        //var_dump($message);
        if ( is_multisite() )
            $blogname = $GLOBALS['current_site']->site_name;
        else
            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $title = sprintf( __('[%s] Password Reset'), $blogname );

        $title = apply_filters('retrieve_password_title', $title);
        $message = apply_filters('retrieve_password_message', $message, $key);

        if ( $message && !wp_mail($user_email, $title, $message) ){
            $errors->add('not_be_sent', __('<strong>ERROR</strong>: The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...'));
            return $errors;
        }

            //wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

        return true;
    }

    protected function check_password_reset_key($key, $login) {
        global $wpdb;

        $key = preg_replace('/[^a-z0-9]/i', '', $key);

        if ( empty( $key ) || !is_string( $key ) )
            return new WP_Error('invalid_key', __('Invalid key'));

        if ( empty($login) || !is_string($login) )
            return new WP_Error('invalid_key', __('Invalid key'));

        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

        if ( empty( $user ) )
            return new WP_Error('invalid_key', __('Invalid key'));

        return $user;
    }

    public static function create_rewrite_rules($rules) {
        $newRule = array('reset-password/*?' => 'index.php?reset-password=true');
        $newRules = $newRule + $rules;
        return $newRules;
    }

    public static function add_query_vars($qvars) {
        $qvars[] = 'reset-password';
        $qvars[] = 'action';
        $qvars[] = 'key';
        $qvars[] = 'login';
        $qvars[] = 'msg';
        return $qvars;
    }

    public static function template_redirect_resetpassword() {
        global $wp_query;
        $action = get_query_var('action');
        if (get_query_var('reset-password') && !empty($action)) {
            if(is_user_logged_in()) { wp_redirect(home_url('/')); exit; }

            switch($action){
                case 'resetpass' :
                case 'rp' :
                    $key   = get_query_var('key');
                    $login = get_query_var('login');
                    $user  = self::check_password_reset_key($key, $login);
                    if ( is_wp_error($user) ) {
                        wp_redirect( site_url('reset-password/?action=lostpassword&msg=invalidkey') );
                        exit;
                    }

                    $errors = '';

                    if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] ) {
                        $errors = new WP_Error('password_reset_mismatch', __('The passwords do not match.'));
                    } elseif ( isset($_POST['pass1']) && !empty($_POST['pass1']) ) {
                        do_action('password_reset', $user, $_POST['pass1']);
                        wp_set_password($_POST['pass1'], $user->ID);
                        wp_password_change_notification($user);

                        wp_redirect(home_url('/'));
                        exit;
                    }

                    $template_name = 'resetpassform.php';
                    $template = self::get_template($template_name);
                    if (!empty($template)) {
                        ob_start();
                        include $template;
                        $content = ob_get_clean();
                        echo $content;
                        exit;
                    }
                break;
                case 'lostpassword':
                    $errors = '';
                    if ( isset( $_POST["PiLoginEmail"] ) ) {
                        $result = self::retrieve_password();
                        if($result instanceof WP_Error){
                            $errors = $result;
                        }else{
                            wp_redirect(home_url('/'));
                            exit;
                        }
                    }
                    $msg = get_query_var('msg');
                    if ( !empty($msg) && $msg == 'invalidkey' ) {
                        $errors = new WP_Error('invalidkey', __('Sorry, that key does not appear to be valid.'));
                    }
                    $user_login = isset($_POST['PiLoginEmail']) ? stripslashes($_POST['PiLoginEmail']) : '';
                    $template_name = 'lostpasswordform.php';
                    $template = self::get_template($template_name);
                    if (!empty($template)) {
                        ob_start();
                        include $template;
                        $content = ob_get_clean();
                        echo $content;
                        exit;
                    }
                break;
                default: wp_redirect(home_url('/'));
            }


        }else if(get_query_var('reset-password')){
            wp_redirect(home_url('/'));
            exit;
        }
    }

    public static function no_wp_login(){
        $url = '';
        if(isset($_GET['action']) && $_GET['action'] == 'logout') return;
        if($_GET['redirect_to'] != '') $url = $_GET['redirect_to'];

        $redirect_url = '';
        if(!empty($url)) $redirect_url = '&redirect_to='.urlencode($url);

        $default = array('action', 'redirect_to');
        $another_params = '';
        foreach($_GET as $key => $value){
            if(!in_array($key, $default)){
                $another_params .= '&'.$key.'='.$value;
            }
        }

        $location = home_url('/?action=login');
        wp_redirect($location.$redirect_url.$another_params);
        exit;
    }

    public static function action_wp_head_login(){
        $show_login = isset($_GET['action']) && $_GET['action'] == 'login' ? true : false;

        ?><script language="javascript" type="text/javascript" tag="login-settings">
        <?php
            if($show_login) echo 'var show_login = true;';
            else echo 'var show_login = false;';
        ?>
        </script>
        <?php
    }
	
    public static function set_redirect_url($logout_url, $redirect = ''){
        if(empty($redirect)){
            $args['redirect_to']  = urlencode( home_url());
            $logout_url = add_query_arg($args, $logout_url);
        }
        return $logout_url;
    }

    public static function activate(){
        add_filter('rewrite_rules_array', array(__CLASS__, 'create_rewrite_rules'));
        flush_rewrite_rules();
    }
}


if ( defined('ABSPATH') ) {
	add_action( 'plugins_loaded', array('PiLogin','init') );
    register_activation_hook( __FILE__, array('PiLogin', 'activate'));
}

<?php
/*
Plugin Name: PI Facebook OAuth (use JS SDK)
Version: 0.3
Author: Postindustria
*/


class PIFBOauth
{
    protected static $loaded = false;
//    const TEMPLATES_PATH = 'templates';

    const FACEBOOK_APP_ID_OPTION_NAME = 'pi_fbconnect_api_id';
    const FACEBOOK_SECRET_OPTION_NAME = 'pi_fbconnect_secret';
    const FACEBOOK_PERM_ARRAY_OPTION_NAME = 'pi_fbconnect_perm';
    const FACEBOOK_ERROR_CALLBACK_OPTION_NAME = 'pi_fbconnect_error_callback';

    //constants - Application API ID and Application Secret
    protected static $FACEBOOK_APP_ID = '';
    protected static $FACEBOOK_SECRET = '';
    protected static $FACEBOOK_PERM_ARRAY = array();
    protected static $FACEBOOK_ERROR_CALLBACK_OPTION_NAME = 'piLoginShowErrorMassage';

    public static function init()
    {
        self::$loaded = true;

        /**
         * init filters
         */
        $pluginPath = plugin_dir_path(__FILE__);

        self::$FACEBOOK_APP_ID = self::getFacebookAppId();
        self::$FACEBOOK_SECRET = self::getFacebookSecret();
        self::$FACEBOOK_PERM_ARRAY = self::getFacebookPermArray();

        add_action('admin_menu', array(__CLASS__, 'FBConnectMenu'));

        if (self::getFacebookAppId() == '' || self::getFacebookSecret() == '') {
            add_action('admin_notices', array(__CLASS__, 'FBConnectSettingsMissing'));
        } else {
            add_action('init', array(__CLASS__, 'facebookHeader'));
            add_action('init', array(__CLASS__, 'fbLoginUser'));
            add_action('wp_footer', array(__CLASS__, 'fbFooter'));
//            add_action('admin_print_footer_scripts', 'fbFooter');
            add_filter('get_avatar', array(__CLASS__, 'FBGetAvatar'), 10, 4);
            add_action('render_fb_login_button', array(__CLASS__, 'renderFBOauthButton'));


            /**
             * @TODO CHANGES
             */
//            add_filter('get_login_error_message_by_error_code', 'fb_get_error_message_by_code', 10, 2);

        }

        //resister uninstall function
        register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstallFacebookOauth'));


    }

    /**
     * notification to set the settings before using plugin
     */
    function FBConnectSettingsMissing()
    {
        ?>
        <div class="error">
            <p><?php printf(__('Facebook Connect plugin is almost ready. To start using Facebook Connect
            <strong>you need to set your Facebook Application API ID and Faceook Application secret</strong>.
            You can do that in
                <a href="%1s">Facebook Connect settings page</a>.', 'wpsc'),
                    admin_url('options-general.php?page=pi_fb_connect_options')) ?></p>
        </div>
    <?php
    }

    public static function reInitOptions()
    {
        self::$FACEBOOK_APP_ID = null;
        self::$FACEBOOK_SECRET = null;
        self::$FACEBOOK_PERM_ARRAY = null;
        self::$FACEBOOK_ERROR_CALLBACK_OPTION_NAME = null;
        self::$FACEBOOK_APP_ID = self::getFacebookAppId();
        self::$FACEBOOK_SECRET = self::getFacebookSecret();
        self::$FACEBOOK_PERM_ARRAY = self::getFacebookPermArray();
        self::$FACEBOOK_ERROR_CALLBACK_OPTION_NAME = self::getFacebookErrorCallback();
    }

    /**
     * add options page and register settings
     */
    public static function FBConnectMenu()
    {
        add_options_page('FB Connect options', 'PI FB Connect', 'manage_options', 'pi_fb_connect_options', array(__CLASS__, 'FBConnectOptions'));
        add_action('admin_init', array(__CLASS__, 'registerFBConnectMySettings'));
    }

    /**
     * register settings
     */
    public static function registerFBConnectMySettings()
    {
        register_setting('fb_connect_settings', self::FACEBOOK_APP_ID_OPTION_NAME);
        register_setting('fb_connect_settings', self::FACEBOOK_SECRET_OPTION_NAME);
        register_setting('fb_connect_settings', self::FACEBOOK_PERM_ARRAY_OPTION_NAME);
        register_setting('fb_connect_settings', self::FACEBOOK_ERROR_CALLBACK_OPTION_NAME);
    }

    /**
     * options page
     */
    public static function FBConnectOptions()
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $attributesForSave = array(
                self::FACEBOOK_APP_ID_OPTION_NAME,
                self::FACEBOOK_SECRET_OPTION_NAME,
                self::FACEBOOK_PERM_ARRAY_OPTION_NAME,
                self::FACEBOOK_ERROR_CALLBACK_OPTION_NAME,
            );
            foreach ($attributesForSave as $attr) {
                $value = $_POST[$attr];
                update_option($attr, $value);
            }

            self::reInitOptions();
        }

        ?>
        <div class="wrap">
            <h2><?php _e('Facebook Connect'); ?></h2>
            <? if (!ini_get("allow_url_fopen")) { ?>
                <div class="error"><p>
                        <strong>This plugin will not work!</strong> <br>
                        <strong><em>allow_url_fopen</em></strong> in your php.ini settings needs to be turned on. <br>
                        Otherwise, this plugin will not be able communicate with Facebook to authenticate the user.
                    </p></div>
            <? } ?>
            <form method="post">
                <?php settings_fields('pi_fb_connect_options'); ?>

                <table class="form-table">

                    <tr valign="top">
                        <th scope="row"><?php _e('Facebook Application API ID:'); ?></th>
                        <td><input type="text" name="<?php echo self::FACEBOOK_APP_ID_OPTION_NAME; ?>"
                                   value="<?php echo self::getFacebookAppId(); ?>"/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Faceook Application secret:'); ?></th>
                        <td><input type="text" name="<?php echo self::FACEBOOK_SECRET_OPTION_NAME; ?>"
                                   value="<?php echo self::getFacebookSecret() ?>"/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Show error callback'); ?></th>
                        <td><input type="text" name="<?php echo self::FACEBOOK_ERROR_CALLBACK_OPTION_NAME; ?>"
                                   value="<?php echo self::getFacebookErrorCallback() ?>"/></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Ask email:'); ?></th>
                        <td><input type="checkbox" name="<?php echo self::FACEBOOK_PERM_ARRAY_OPTION_NAME; ?>[]"
                                   value="email" <?php checked(true, in_array('email', self::getFacebookPermArray())); ?>
                            " />
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>

            </form>
        </div>
    <?php
    }

    public static function getFacebookAppId()
    {
        if (!self::$FACEBOOK_APP_ID) {
            self::$FACEBOOK_APP_ID = get_option(self::FACEBOOK_APP_ID_OPTION_NAME);
        }

        return self::$FACEBOOK_APP_ID;
    }

    public static function getFacebookSecret()
    {
        if (!self::$FACEBOOK_SECRET) {
            self::$FACEBOOK_SECRET = get_option(self::FACEBOOK_SECRET_OPTION_NAME);
        }

        return self::$FACEBOOK_SECRET;
    }

    public static function getFacebookPermArray()
    {
        if (!self::$FACEBOOK_PERM_ARRAY) {
            $option = get_option(self::FACEBOOK_PERM_ARRAY_OPTION_NAME);
            if (empty($option)) {
                $option = array();
            }

            self::$FACEBOOK_PERM_ARRAY = $option;
        }

        return self::$FACEBOOK_PERM_ARRAY;
    }
    public static function getFacebookErrorCallback()
    {
        if (!self::$FACEBOOK_ERROR_CALLBACK_OPTION_NAME) {
            $option = get_option(self::FACEBOOK_ERROR_CALLBACK_OPTION_NAME);
            self::$FACEBOOK_ERROR_CALLBACK_OPTION_NAME = $option;
        }

        return self::$FACEBOOK_ERROR_CALLBACK_OPTION_NAME;
    }

    public static function facebookHeader()
    {
        wp_enqueue_script('facebook_connect_js_functions', 'http://connect.facebook.net/en_US/all.js', array());
    }

    public static function fbFooter()
    {
        ?>
        <style>
            #pi-fb-oauth-error-dialog {
                text-align: center;
            }
        </style>
        <div id="fb-root"></div>

        <div id="pi-fb-oauth-error-dialog" title="FACEBOOK WARNING" class="pi-fb-oauth-popup"
             style="text-align: center;">
            <div style="clear: both"></div>
            <span id="pi-fb-oauth-error-dialog-message" style="font-size: 14px;">&nbsp;</span>
            <!--            dirty hack-->
            <div class="ui-dialog-buttonset">
                <button style="top: 80px;left: 80px;" type="button"
                        class="close-pi-fb-oauth-error-dialog ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                        role="button" aria-disabled="false">
                    <span class="ui-button-text">Close</span>
                </button>
            </div>


        </div>
        <script type="text/javascript">
            var PI_OAUTH_FACEBOOK_APP_ID = '<?php echo self::getFacebookAppId(); ?>';
            var PI_OAUTH_FACEBOOK_SCOPE = '<?php echo implode(',',self::getFacebookPermArray());?>';
            var PI_OAUTH_FACEBOOK_ERROR_CALLBACK = <?php echo self::getFacebookErrorCallback();?>;
        </script>

		<?php
        $pluginUrl = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
        wp_enqueue_script('pi_fb_oauth_js_functions', $pluginUrl . '/pi-fb-oauth.js', array(),'3.4.2_1');
    }

    /**
     * Log in facebook user with ajax
     */
    public static function fbLoginUser()
    {
        if (!(isset($_REQUEST['fb_ajax_login']) && ($_POST['fb_ajax_login'] == 1) && $_POST['fb_access_token'])) return;

        if (is_user_logged_in()) self::giveSuccessResponse();
        /**
         * get access token
         */
        $token = $_POST['fb_access_token'];
        $user = self::getFbUser($token);

        $existingUser = self::getExistingUser($user);

        if ($existingUser > 0) {
//            $user_info = get_userdata($existingUser);
            wp_set_auth_cookie($existingUser, true, false);

            self::giveSuccessResponse();
        } else {
            if (self::createNewUserFromFB($user)) {
                self::giveSuccessResponse();
            }
        }
    }

    /**
     * Get facebook avatar
     * @param $avatar
     * @param $id_or_email
     * @param $size
     * @param $default
     * @return string
     */
    public static function FBGetAvatar($avatar, $id_or_email, $size, $default)
    {
        if (!is_object($id_or_email)) {
            return $avatar;
        }
        $fbuid = self::getFBuid($id_or_email->user_id);
        if ($fbuid) {
            return self::renderFBProfilePicture($fbuid);
        } else {
            return $avatar;
        }
    }

    /**
     * render log in button
     */
    public static function renderFBOauthButton()
    {
        echo '<a href="" onclick="fb_start_login(); return false;" class="fb_oauth2_login" title="Sign in with Facebook">Sign in with Facebook</a>';
    }

    /**
     * uninstall hooks
     */
    public static function uninstallFacebookOauth()
    {
        delete_option('fbconnect_api_id');
        delete_option('fbconnect_secret');
    }

    /**
     * Render facebook profile picture
     * @param $user facebook user profile
     * @return string
     */
    protected static function renderFBProfilePicture($user)
    {
        return '<div class="avatar avatar-32">'
        . '<img src="https://graph.facebook.com/' . $user . '/picture" width="32" heigth="32">'
        . '</div>';
    }

    /**
     * Return template source
     * @param $name
     * @return bool|string
     */
    protected static function getTemplate($name)
    {
        $path = locate_template(self::TEMPLATES_PATH . '/' . $name);
        if (empty($path)) {
            $path = dirname(__FILE__) . '/' . 'templates' . '/' . $name;
        }
        if (file_exists($path)) {
            return $path;
        }
        return false;
    }

    /**
     * Get FB id by wordpress ID
     * @param $wpuid
     * @return int|mixed
     */
    protected static function getFBuid($wpuid)
    {
        static $fbc_uidcache = null;
        if ($fbc_uidcache === null) {
            $fbc_uidcache = array();
        }

        if (isset($fbc_uidcache[$wpuid])) {
            return $fbc_uidcache[$wpuid];
        }
        if (!$wpuid) {
            $fbuid = 0;
        } else {
            $fbuid = get_user_meta($wpuid, 'fbuid', true);
        }
        return ($fbc_uidcache[$wpuid] = $fbuid);
    }

    /**
     * Creates new user
     *
     * @return bool|int false if don't create new user
     * @param $fbUser
     */
    protected static function createNewUserFromFB($fbUser)
    {
        $userdata = self::createNewUserData($fbUser);

        $result = wp_insert_user($userdata);

        if (is_wp_error($result)) {
            do_action('pi_log_add', 'facebook_user_creation', array('user_data' => $userdata, 'result' => $result));
            self::giveErrorResponse(fb_get_error_message_by_code(fb_get_error_code_by_wp_error($result)));
        }

        $newUserId = absint($result);
        if ($newUserId > 0) {
            update_user_meta($newUserId, 'fbuid', $fbUser->id);
//            $user_info = get_userdata($newUser);
            wp_set_auth_cookie($newUserId, true, false);
            return $newUserId;
        } else {
            self::giveErrorResponse('User can\'t be create. Try to login again');
        }

        return false;
    }

    /**
     * Get FB user with using FB graph API
     *
     * @param $token FB access token
     * @return array FB user profile
     */
    protected static function getFbUser($token)
    {
        if (!$token) {
            self::giveErrorResponse('Problem with facebook token. Try to reload page and login again');
        }
        $user = json_decode(@file_get_contents('https://graph.facebook.com/me?access_token=' . $token));

        $options = self::getFacebookPermArray();
        if (in_array('email', $options) && empty($user->email)) {
            $result = new WP_Error('fb_email_required');
            self::giveErrorResponse(fb_get_error_message_by_code(fb_get_error_code_by_wp_error($result)));
        }


        if (empty($user)) {
            self::giveErrorResponse('No facebook user available or invalid token. Try to reload page and login again');
        }

        return $user;
    }

    /**
     * Create array with user data
     * @param $user
     * @return mixed|void
     */
    protected static function createNewUserData($user)
    {
        $fbusername = 'fb' . $user->id;

        $userdata = array(
            'user_pass' => wp_generate_password(),
            'user_login' => $fbusername,
            'user_nicename' => $fbusername,
            'user_email' => (!empty($user->email)) ? $user->email : $fbusername . '@wp-fboauth2.fake',
            'display_name' => $user->name,
            'nickname' => $fbusername,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => 'subscriber'
        );

        $userdata = apply_filters('fb_connect_new_userdata', $userdata, $user);

        return $userdata;
    }

    /**
     * Return existing WP user from FB profile
     * @param $fbUser
     * @return mixed
     */
    protected static function getExistingUser($fbUser)
    {
        global $wpdb;
        $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s' LIMIT 1";
        return $wpdb->get_var($wpdb->prepare($sql, 'fbuid', $fbUser->id));
    }

    /**
     * Show success response and kill script
     */
    protected static function giveSuccessResponse()
    {
        echo json_encode(
            array(
                'status' => 'OK',
            ));

        die();
    }

    /**
     * Show error response with message and kill script
     * @param $msg error message
     */
    protected static function giveErrorResponse($msg)
    {
        echo json_encode(
            array(
                'status' => 'ERROR',
                'message' => $msg
            ));

        die();
    }
}

define('FACEBOOK_APP_ID', get_option(PIFBOauth::FACEBOOK_APP_ID_OPTION_NAME));
define('FACEBOOK_SECRET', get_option(PIFBOauth::FACEBOOK_SECRET_OPTION_NAME));

if (defined('ABSPATH')) {
    add_action('plugins_loaded', array('PIFBOauth', 'init'));


    function fb_get_error_code_by_wp_error($error)
    {
        switch ($error->get_error_code()) {
            case 'existing_user_login':
                return 'fb_1';
            case 'existing_user_email':
                return 'fb_2';
            case 'fb_email_required'  :
                return 'fb_3';
            default:
                return 5;
        }
    }

    function fb_get_error_message_by_code($code)
    {
        switch ($code) {
            case 'fb_1':
                $message = "This username is already registered.";
                break;
            case 'fb_2':
                $message = "This email address is already registered.";
                break;
            case 'fb_3':
                $message = "You need allow access to your email.";
                break;
        }
        return $message;
    }

}



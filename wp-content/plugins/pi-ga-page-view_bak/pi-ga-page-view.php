<?php
/*
    Plugin Name: PI GA Page View
    Description: GA Page View
    Version: 0.1
    Author: PostIndustria
*/
/**
 * Class PiGaPageView
 */
class PiGaPageView
{
    const ADMIN_PAGE_SLUG = 'pi_ga_page_view_options';
    const ADMIN_PAGE_NONCE = 'pi_ga_page_view_nonce';
    const OPTION_GROUP_NAME = 'ga_page_view_settings';
    const PROTECTED_DIRECTORY_NAME = 'protected';
    const CACHE_KEY_PREFIX = 'pi_ga_pageview_cache_';
    const CACHE_TIME_LIMIT = 900; // 15 minutes
    const CACHE_TIME_MAIN_DATA = 172800; // 2 days
    const CACHE_TIME_LOCK_DATA = 600; // 10 minutes
    const GA_ACTION_NAME = 'page_view'; 

    /**
     * option variables
     * Set your client id, service account name, and the path to your private key.
     * For more information about obtaining these keys, visit:
     * https://developers.google.com/console/help/#service_account`s
     */
    protected static $SERVICE_ACCOUNT_NAME = '';
    // Make sure you keep your key.p12 file in a secure location, and isn't
    // readable by others.
    protected static $KEY = '';
    protected static $GA_ID = '';
    protected static $GA_WEBPROPERTY_ID = '';
    protected static $GA_PROFILE_ID = '';
    /**
     * track options
     */
    protected static $TRACK_SINGLE_POST = null;
    protected static $TRACK_CATEGORY_LIST = null;
    /**
     * option name constants
     */
    const TRACK_SINGLE_POST_OPTION_NAME = 'ga_page_view_track_single_post';
    const TRACK_CATEGORY_LIST_OPTION_NAME = 'ga_page_view_track_category_list';
    const SERVICE_ACCOUNT_NAME_OPTION_NAME = 'ga_page_view_service_account_name';
    const KEY_OPTION_NAME = 'ga_page_view_key';
    const GA_ID_OPTION_NAME = 'ga_page_view_ga_id';
    const GA_WEBPROPERTY_ID_OPTION_NAME = 'ga_page_view_ga_webproperty_id';
    const GA_PROFILE_ID_OPTION_NAME = 'ga_page_view_ga_profile_id';
    /**
     * lazy load variables
     */
    protected static $_client = null;
    protected static $_analytics = null;
    public static function init() 
    {
        require_once 'lib/Google_Client.php';
        require_once 'lib/contrib/Google_AnalyticsService.php';
        $path = dirname(__FILE__);
        /**
         * save options added here because if we have saved options later
         * we wouldn't got a notification about in correct option values
         */
        self::saveOptions();
        add_action('admin_menu', array(
            __CLASS__,
            'settingsMenu'
        ));
        if (!self::isConfigurationComplete()) 
        {
            if (strlen(self::getServiceAccountName()) < 2 || strlen(self::getKey()) < 2) 
            {
                add_action('admin_notices', array(
                    __CLASS__,
                    'menuSettingsMissing'
                ));
            }
            elseif (!is_object(self::getGoogleClient())) 
            {
                add_action('admin_notices', array(
                    __CLASS__,
                    'settingsMissingInvalid'
                ));
            }
            elseif (!self::getGAId() || !self::getGAWebPropertyId() || !self::getGAProfileId()) 
            {
                add_action('admin_notices', array(
                    __CLASS__,
                    'adminSettingsMissing'
                ));
            }
        }
        else
        {
            /**
             * add needed actions and filters
             */
            add_action('pi-ga-pageview-track', array(
                __CLASS__,
                'attachPageViewScripts'
            ));
            add_filter('pi-ga-pageview-category-list', array(
                __CLASS__,
                'categoryPostListFilter'
            ) , 10, 5);
            add_filter('pi-ga-pageview-single-post', array(
                __CLASS__,
                'singlePostListFilter'
            ) , 10, 4);
            add_filter('pi-ga-pageview-most-popular', array(
                __CLASS__,
                'pageview_most_popular'
            ) , 10, 5);
        }
        add_action('wp_ajax_ga_get_html_for_select_by_type', array(
            __CLASS__,
            'ajaxGetHtmlForSelectByType'
        ));
    }
    public static function isSinglePostTrakingEnabled() 
    {
        
        return (bool)self::getSinglePostTrack();
    }
    public static function isCategoryPostTrakingEnabled() 
    {
        $trackCategories = self::getCategoryListTrack();
        
        return (bool)!empty($trackCategories);
    }
    public static function attachPageViewScripts() 
    {
        global $post;
        $timeStamp = strtotime($post->post_date);
        $id = $post->ID;
        $postGaLabel = '' . $timeStamp . ':' . $id;
        if (self::isSinglePostTrakingEnabled()) 
        {
            if (!empty($post)) 
            {
                if (is_single() && $post->post_type == 'post') 
                {
                    $GACategoty = 'spin_view_single_post';
                    echo '_gaq.push(["_trackEvent", "' . $GACategoty . '", "' . self::GA_ACTION_NAME . '", "' . $postGaLabel . '",0,true]);' . "\n";
                }
            }
        }
        if (self::isCategoryPostTrakingEnabled()) 
        {
            $trackCategories = self::getCategoryListTrack();
            if (in_category($trackCategories)) 
            {
                $categories = get_the_category($post->ID);
                $categoriesArray = array_map(function ($item) 
                {
                    
                    return $item->name;
                }
                , $categories);
                
                foreach ($trackCategories as $cat) 
                {
                    if (in_array($cat, $categoriesArray)) 
                    {
                        $GACategoty = 'spin_view_category_' . strtolower($cat);
                        echo '_gaq.push(["_trackEvent", "' . $GACategoty . '", "' . self::GA_ACTION_NAME . '", "' . $postGaLabel . '"]);' . "\n";
                    }
                }
            }
        }
    }
    public static function ajaxGetHtmlForSelectByType() 
    {
        
        try
        {
            $html = '';
            if ($_POST['gaId']) 
            {
                if ($_POST['type'] == 'profiles') 
                {
                    if ($_POST['webpropertyId']) 
                    {
                        $html.= '<option value="" >Select Profile </option>';
                        self::$GA_ID = $_POST['gaId'];
                        self::$GA_WEBPROPERTY_ID = $_POST['webpropertyId'];
                        $profiles = self::getProfileList($_POST['gaId'], $_POST['webpropertyId']);
                        
                        foreach ($profiles as $profile) 
                        {
                            $html.= '<option value="' . $profile['profileId'] . '" >' . $profile['profileName'] . '</option>';
                        }
                    }
                    else
                    {
                        self::giveErrorResponse("No valid webproperty available. Please reload page and try again!");
                    }
                }
                else
                {
                    $html.= '<option value=""> Select Webproperty </option>';
                    self::$GA_ID = $_POST['gaId'];
                    $webproperties = self::getWebPropertyList(intval($_POST['gaId']));
                    
                    foreach ($webproperties as $webproperty) 
                    {
                        $html.= '<option value="' . $webproperty['webpropertyId'] . '" >' . $webproperty['websiteURL'] . '</option>';
                    }
                }
                echo json_encode(array(
                    "status" => 'OK',
                    'html' => $html
                ));
            }
            else
            {
                self::giveErrorResponse("No valid data available.Please reload page and try again!");
            }
        }
        catch(Exception $e) 
        {
            self::giveErrorResponse("Access problem. Please try again or call to administrator!");
        }
        die();
    }
    public static function getWebPropertyList($accountId, $returnObject = false) 
    {
        if (empty($accountId) || $accountId < 1) 
        {
            
            return array();
        }
        $analytics = self::getGoogleAnalytics();
        if (!is_object($analytics)) 
        {
            
            return array();
        }
        
        try
        {
            $webproperties = $analytics->management_webproperties->listManagementWebproperties($accountId);
        }
        catch(Exception $e) 
        {
            
            return array();
        }
        if (!is_object($webproperties)) 
        {
            
            return array();
        }
        $items = $webproperties->getItems();
        if ($returnObject) 
        {
            
            return $items;
        }
        $webPropertyArray = array();
        
        foreach ($items as $webproperty) 
        {
            $webPropertyArray[] = array(
                'kind' => $webproperty->getKind() ,
                'accountID' => $webproperty->getAccountId() ,
                'webpropertyId' => $webproperty->getId() ,
                'internalWebpropertyId' => $webproperty->getInternalWebPropertyId() ,
                'websiteURL' => $webproperty->getWebsiteUrl() ,
                'created' => $webproperty->getCreated() ,
                'updated' => $webproperty->getUpdated() ,
                'selfLink' => $webproperty->getSelfLink() ,
                'parentLinkHref' => $webproperty->getParentLink()->getHref() ,
                'parentLinkType' => $webproperty->getParentLink()->getType() ,
                'childLinkHref' => $webproperty->getChildLink()->getHref() ,
                'childLLinkType' => $webproperty->getChildLink()->getType() ,
            );
        }
        
        return $webPropertyArray;
    }
    public static function getAccountList($returnObject = false) 
    {
        $analytics = self::getGoogleAnalytics();
        if (!is_object($analytics)) 
        {
            
            return array();
        }
        
        try
        {
            $accounts = $analytics->management_accounts->listManagementAccounts();
        }
        catch(Exception $e) 
        {
            
            return array();
        }
        if (!is_object($accounts)) 
        {
            
            return array();
        }
        $items = $accounts->getItems();
        if ($returnObject) 
        {
            
            return $items;
        }
        $accountArray = array();
        
        foreach ($items as $account) 
        {
            $accountArray[] = array(
                'accountId' => $account->getId() ,
                'kind' => $account->getKind() ,
                'selfLink' => $account->getSelfLink() ,
                'accountName' => $account->getName() ,
                'created' => $account->getCreated() ,
                'updated' => $account->getUpdated() ,
            );
        }
        
        return $accountArray;
    }
    public static function getProfileList($accountId, $webPropertyId, $returnObject = false) 
    {
        $analytics = self::getGoogleAnalytics();
        if (!is_object($analytics)) 
        {
            
            return array();
        }
        if (empty($accountId) || $accountId < 1) 
        {
            
            return array();
        }
        if (empty($webPropertyId) || strlen($webPropertyId) < 2) 
        {
            
            return array();
        }
        
        try
        {
            $profiles = $analytics->management_profiles->listManagementProfiles($accountId, $webPropertyId);
        }
        catch(Exception $e) 
        {
            
            return array();
        }
        if (!is_object($profiles)) 
        {
            
            return array();
        }
        $items = $profiles->getItems();
        if ($returnObject) 
        {
            
            return $items;
        }
        
        foreach ($items as $profile) 
        {
            $accountArray[] = array(
                'accountId' => $profile->getAccountId() ,
                'webPropertyId' => $profile->getWebPropertyId() ,
                'internalwebPropertyId' => $profile->getInternalWebPropertyId() ,
                'profileId' => $profile->getId() ,
                'profileName' => $profile->getName() ,
                'defaultPage' => $profile->getDefaultPage() ,
                'excludeQueryParameters' => $profile->getExcludeQueryParameters() ,
                'siteSearchCategoryParameters' => $profile->getSiteSearchCategoryParameters() ,
                'siteSearchQueryParameters' => $profile->getSiteSearchQueryParameters() ,
                'currency' => $profile->getCurrency() ,
                'timezone' => $profile->getTimezone() ,
                'created' => $profile->getCreated() ,
                'updated' => $profile->getUpdated() ,
            );
        }
        
        return $accountArray;
    }
    protected static function lock($key) 
    {
        return wp_cache_add($key . '_lock', 1, '', self::CACHE_TIME_LOCK_DATA);
    }
    protected static function unlock($key) 
    {
        return wp_cache_delete($key . '_lock', '');
    }
    public static function getPostsListWithLock($category, $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days') 
    {
        $key = self::CACHE_KEY_PREFIX . '_' . md5(implode('_', array(
                $category,
                $limit,
                $createtionPeriod,
                $calculationPeriod,
            )));
        $time = time();
        $data = wp_cache_get($key, '');
        if (!is_array($data)) 
        {
            $postIds = array();
            $lasttimestamp = $time;
        }
        else
        {
            $postIds = is_array($data['data']) ? $data['data'] : array();
            $lasttimestamp = $data['lasttimestamp'] ? $data['lasttimestamp'] : $time;
        }
        $clearCache = (isset($_GET['clear_cache']) && $_GET['clear_cache'] > 0);
        if (($time - intval($lasttimestamp)) >= self::CACHE_TIME_LIMIT || $clearCache || $data === false) 
        {
            if (!self::lock($key)) 
            {
                $postIds = self::getPostsList($category, $limit, $createtionPeriod, $calculationPeriod);
                $data['data'] = $postIds;
                $data['lasttimestamp'] = $time;
                wp_cache_set($key, $data, '', self::CACHE_TIME_MAIN_DATA); 
                self::unlock($key);
            } else {
                return $postIds;
            }
        }
        $postIds = $data['data'];
        if (!is_array($postIds)) 
        {
            $postIds = array();
        }
        
        return $postIds;
    }
    
    public static function pageview_most_popular($category='spin_most_popular', $action='', $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days'){
        $key = self::CACHE_KEY_PREFIX . '_' . md5(implode('_', array(
                $category,
                $action,
                $limit,
                $createtionPeriod,
                $calculationPeriod,
            )));
            
        $time = time();
        $data = wp_cache_get($key, '');
        
        if (!is_array($data)) 
        {
            $postIds = array();
            $lasttimestamp = $time;
        }
        else
        {
            $postIds = is_array($data['data']) ? $data['data'] : array();
            $lasttimestamp = $data['lasttimestamp'] ? $data['lasttimestamp'] : $time;
        }
        $clearCache = (isset($_GET['clear_cache']) && $_GET['clear_cache'] > 0);
        if (($time - intval($lasttimestamp)) >= self::CACHE_TIME_LIMIT || $clearCache || $data === false) 
        {   
            if (!self::lock($key)) 
            {
                $postIds = self::get_most_popular($category, $action, $limit, $createtionPeriod, $calculationPeriod);
                $data['data'] = $postIds;
                $data['lasttimestamp'] = $time;
                wp_cache_set($key, $data, '', self::CACHE_TIME_MAIN_DATA); 
                self::unlock($key);
            } else {
                return $postIds;
            }
        }
        $postIds = $data['data'];
        if (!is_array($postIds)) 
        {
            $postIds = array();
        }
        
        return $postIds;        
    }
    
    public static function get_most_popular($category='', $action='', $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days') 
    {
        if (!self::isConfigurationComplete()) return array();
        
        $dateFrom = date('Y-m-d', strtotime('- ' . $calculationPeriod));
        $dateTo   = date('Y-m-d');
        
        $metrics = 'ga:visits';
        $analytics = self::getGoogleAnalytics();
        
        try{
            $searchRegexp = self::getSearchRegexpExpression($createtionPeriod);
            
            $result = $analytics->data_ga->get('ga:' . self::getGAProfileId() , $dateFrom, $dateTo, $metrics, array(
                'dimensions' => 'ga:eventLabel',
                'sort' => '-ga:visits',
                'filters' => 'ga:eventCategory==' . $category . ';ga:eventAction==' . $action . ';ga:eventLabel=~' . $searchRegexp,
                'max-results' => $limit
            ));
            
            if (count($result->rows) > 0) {
                $ids = array();
                $rows = $result->rows;
                foreach($rows as $i=>$item){
                    $ids[] = intval(substr($rows[$i][0], strpos($rows[$i][0], ':') + 1));    
                }
                
                return $ids;
            }
            else
                return array();
            
        }
        catch(Exception $e) 
        {
            
            return array();
        }
    }
    
    
    public static function categoryPostListFilter($default = array(),$category='', $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days') 
    {
        $category = 'spin_view_category_' . strtolower($category);
        
        return self::getPostsListWithLock($category, $limit, $createtionPeriod, $calculationPeriod);
    }
    public static function singlePostListFilter($default = array(),$limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days') 
    {
        $category = 'spin_view_single_post';
        
        return self::getPostsListWithLock($category, $limit, $createtionPeriod, $calculationPeriod);
    }
    public static function getPostsList($category = 'spin_view_single_post', $limit = 5, $createtionPeriod = '1 week', $calculationPeriod = '3 days') 
    {
        if (!self::isConfigurationComplete()) 
        {
            
            return array();
        }
        $dateFrom = date('Y-m-d', strtotime('- ' . $calculationPeriod));
        $dateTo   = date('Y-m-d');
        
        /**
         * @todo : do not change action
         */
         
        $action = 'view_post';
        $metrics = 'ga:visits';
        $analytics = self::getGoogleAnalytics();
        
        try
        {
            $searchRegexp = self::getSearchRegexpExpression($createtionPeriod);
            $result = $analytics->data_ga->get('ga:' . self::getGAProfileId() , $dateFrom, $dateTo, $metrics, array(
                'dimensions' => 'ga:eventLabel',
                'sort' => '-ga:visits',
                'filters' => 'ga:eventCategory==' . $category . ';ga:eventAction==' . $action . ';ga:eventLabel=~' . $searchRegexp,
                'max-results' => $limit
            ));
            
            if (count($result->rows) > 0) 
            {
                $ids = array();
                $rows = $result->rows;
                foreach($rows as $i=>$item){
                    $ids[] = intval(substr($rows[$i][0], strpos($rows[$i][0], ':') + 1));    
                }
                
                return $ids;
            }
            else
            {
                
                return array();
            }
        }
        catch(Exception $e) 
        {
            
            return array();
        }
    }
    protected static function getSearchRegexpExpression($createtionPeriod) 
    {
        $regexp = '^\d+?:\d+';
        $startTime = '' . strtotime(date('Y-m-d', strtotime('- ' . $createtionPeriod)));
        $endTime = '' . strtotime(date('Y-m-d'));
        $len = strlen($startTime);
        $regexp = '^';
        $i = 0;
        $st = intval($startTime[$i]);
        $et = intval($endTime[$i]);
        /**
         * find the same numbers in 'startTime' and 'endTime' variables
         * for create regex expression to take less results from Google analytics
         */
        
        while ($st == $et) 
        {
            $regexp.= $startTime[$i];
            $i++;
            $st = intval($startTime[$i]);
            $et = intval($endTime[$i]);
        }
        $regexp.= '\d*?:\d+';
        
        return $regexp;
    }
    protected static function getTopPostIdsFromResults($rows, $timestamp, $limit = 5) 
    {
        $timestamp = intval($timestamp);
        $len = count($rows);
        $count = 0;
        $result = array();
        
        for ($i = 0; $i < $len && $count < $limit; $i++) 
        {
            if ($timestamp >= intval($rows[$i][0])) 
            {
                $result[] = intval(substr($rows[$i][0], strpos($rows[$i][0], ':') + 1));
                $count++;
            }
        }
        
        return $result;
    }
    protected static function getGoogleClient() 
    {
        if (is_null(self::$_client)) 
        {
            
            try
            {
                self::$_client = new Google_Client();
                $key = base64_decode(self::getKey());
                /**
                 * @todo  : move pemissions array to variable
                 */
                $assertionCredentials = new Google_AssertionCredentials(self::getServiceAccountName() , array(
                    'https://www.googleapis.com/auth/analytics'
                ) , $key);
                self::$_client->setAssertionCredentials($assertionCredentials);
                self::$_client->setUseObjects(true);
            }
            catch(Google_Exception $e) 
            {
                self::$_client = null;
            }
        }
        
        return self::$_client;
    }
    protected static function getGoogleAnalytics() 
    {
        if (is_null(self::$_analytics)) 
        {
            
            try
            {
                self::$_analytics = new google_AnalyticsService(self::getGoogleClient());
            }
            catch(Exception $e) 
            {
                self::$_analytics = null;
            }
        }
        
        return self::$_analytics;
    }
    /**
     * register settings
     */
    public static function registerGAPageViewSettings() 
    {
        register_setting(self::OPTION_GROUP_NAME, self::SERVICE_ACCOUNT_NAME_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::KEY_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::GA_ID_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::GA_WEBPROPERTY_ID_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::GA_PROFILE_ID_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::TRACK_SINGLE_POST_OPTION_NAME);
        register_setting(self::OPTION_GROUP_NAME, self::TRACK_CATEGORY_LIST_OPTION_NAME);
    }
    /**
     * add options page and register settings
     */
    public static function settingsMenu() 
    {
        wp_enqueue_script('pi-ga-page-view', path_join(WP_PLUGIN_URL, 'pi-ga-page-view') . '/scripts/pi-ga-page-view.js', array() , '0.1.3');
        $scriptData = array(
            'loadImagePath' => path_join(WP_PLUGIN_URL, 'pi-ga-page-view') . '/images/loading.gif'
        );
        wp_localize_script('pi-ga-page-view', 'pi_ga_pageview', $scriptData);
        wp_enqueue_style('pi-ga-page-view', path_join(WP_PLUGIN_URL, 'pi-ga-page-view') . '/styles/pi-ga-page-view.css', array() , '0.1.1');
        add_options_page('Pi GA Page View options', 'PI GA Page View settings', 'manage_options', self::ADMIN_PAGE_SLUG, array(
            __CLASS__,
            'GAPageViewOptions'
        ));
        add_action('admin_init', array(
            __CLASS__,
            'registerGAPageViewSettings'
        ));
    }
    public static function menuSettingsMissing() 
    {
?>
        <div class="error">
            <p><?php
        printf(__('GA Page View plugin is not ready. To start using GA Page View
            <strong>you need to set your GA Page View Application Service Account Name and load key file </strong>.
            You can do that in
                <a href="%1s">GA Page View settings page</a>.', 'wpsc') , admin_url('options-general.php?page=' . self::ADMIN_PAGE_SLUG)) ?></p>
        </div>
        <?php
    }
    public static function adminSettingsMissing() 
    {
?>
        <div class="error">
            <p><?php
        printf(__('GA Page View plugin is almost ready. To start using GA Page View
            <strong>you need to set your GA Page View GA ID, Webproperty and set Profile </strong>.
            You can do that in
                <a href="%1s">GA Page View settings page</a>.', 'wpsc') , admin_url('options-general.php?page=' . self::ADMIN_PAGE_SLUG)) ?></p>
        </div>
        <?php
    }
    public static function settingsMissingInvalid() 
    {
?>
        <div class="error">
            <p><?php
        printf(__('GA Page View plugin have invalid params
            <strong>you need to check your Service Account Name or reload key file </strong>.
            You can do that in
                <a href="%1s">GA Page View settings page</a>.', 'wpsc') , admin_url('options-general.php?page=' . self::ADMIN_PAGE_SLUG)) ?></p>
        </div>
        <?php
    }
    protected static function saveOptions() 
    {
        if (wp_verify_nonce($_POST[self::ADMIN_PAGE_NONCE], self::ADMIN_PAGE_NONCE) && $_SERVER['REQUEST_METHOD'] == "POST" && $_POST['option_page'] == self::OPTION_GROUP_NAME) 
        {
            $attributesForSave = array(
                self::TRACK_CATEGORY_LIST_OPTION_NAME,
                self::SERVICE_ACCOUNT_NAME_OPTION_NAME,
                self::GA_ID_OPTION_NAME,
                self::GA_WEBPROPERTY_ID_OPTION_NAME,
                self::GA_PROFILE_ID_OPTION_NAME,
                self::TRACK_SINGLE_POST_OPTION_NAME,
            );
            
            foreach ($attributesForSave as $attr) 
            {
                $value = $_POST[$attr];
                if (is_array($value)) 
                {
                    $value = array_filter($value, function ($item) 
                    {
                        $item = trim($item);
                        if (empty($item)) 
                        {
                            
                            return false;
                        }
                        
                        return true;
                    });
                }
                if (empty($value) || (is_numeric($value) && $value < 1) || (is_string($value) && strlen($value) < 1)) 
                {
                    delete_option($attr);
                }
                else
                {
                    update_option($attr, $value);
                }
            }
            if ($_FILES['privateFile'] && file_exists($_FILES['privateFile']['tmp_name'])) 
            {
                $tmpFilePath = $_FILES['privateFile']['tmp_name'];
                $mime = mime_content_type($tmpFilePath);
                if (stripos($mime, 'application/octet-stream') === false) 
                {
                    add_action('admin_notices', function () 
                    {
?>
                                <div class="error">
                                    <p>Key file have <strong>invalid</strong> type please load <strong>correct</strong>  file. Previous file was restored</p>
                                </div>
                                <?php
                    }
                    , 100);
                }
                else
                {
                    self::$KEY = base64_encode(file_get_contents($tmpFilePath));
                    update_option(self::KEY_OPTION_NAME, self::$KEY);
                }
            }
            self::reInitOptions();
        }
    }
    /**
     * options page
     */
    public static function GAPageViewOptions() 
    {
?>
        <div class="wrap">
            <h2><?php
        _e('GA Page View API'); ?></h2>
            <?php
        if (!ini_get("allow_url_fopen")) 
        { ?>
                <div class="error"><p>
                        <strong>This plugin will not work!</strong> <br>
                        <strong><em>allow_url_fopen</em></strong> in your php.ini settings needs to be turned on. <br>
                        Otherwise, this plugin will not be able communicate with Facebook to authenticate the user.
                    </p></div>
            <?php
        } ?>
            <form method="post" enctype="multipart/form-data">
                <?php
        wp_nonce_field(self::ADMIN_PAGE_NONCE, self::ADMIN_PAGE_NONCE);
        settings_fields(self::OPTION_GROUP_NAME); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php
        _e('GA Track single post:'); ?></th>
                        <td>
                            <input type="checkbox" name="<?php
        echo self::TRACK_SINGLE_POST_OPTION_NAME; ?>" 
                            value="1"
                            <?php
        checked(true, self::getSinglePostTrack()); ?>
                            />
                        </td>
                    </tr> 

                    <tr valign="top">
                        <th scope="row"><?php
        _e('GA Track post by category:'); ?></th>
                        <td>
                            <?php
        
        foreach (self::getCategoryListTrack() as $cat) 
        {
            echo '<input type="text" name="' . self::TRACK_CATEGORY_LIST_OPTION_NAME . '[]"
                                    value="' . $cat . '"/> <a class="deleteCategoryFromList" href="javascript:void(0);">Delete</a><br />';
        }
?>
                            <input type="text" name="<?php
        echo self::TRACK_CATEGORY_LIST_OPTION_NAME; ?>[]" value=""/>
                            <button class="addMoreCategories">Add category </button>
                        </td>
                    </tr>

                   <tr valign="top">
                        <th scope="row"><?php
        _e('GA Account name (email):'); ?></th>
                        <td>
                            <input type="text" name="<?php
        echo self::SERVICE_ACCOUNT_NAME_OPTION_NAME; ?>" 
                            value="<?php
        echo self::getServiceAccountName(); ?>"/>
                        </td>
                    </tr>

            <tr valign="top">
                        <th scope="row"><?php
        _e('GA Key file:'); ?></th>
                        <td>
                            <input type="file" name="privateFile" />
                            <?php
        if (strlen(self::getKey()) > 1) 
        {
            if (self::isKeyValid()) 
            {
                echo '<p style="color:green;">File had been already uploaded</p>';
            }
            else
            {
                echo '<p style="color:red;">Key file is invalid</p>';
            }
        }
        else
        {
            echo '<p style="color:red;">File had not been uploaded yet. Please upload file</p>';
        }
?>
                        </td>
                    </tr>
                    <?php
        
        try
        {
            $accounts = self::getAccountList();
            $webproperties = array();
            if (self::getGAId()) 
            {
                $webproperties = self::getWebPropertyList(self::getGAId());
            }
            $profiles = array();
            if (self::getGAId() && self::getGAWebPropertyId()) 
            {
                $profiles = self::getProfileList(self::getGAId() , self::getGAWebPropertyId());
            }
        }
        catch(Exception $e) 
        {
            if (!is_array($accounts)) 
            {
                $accounts = array();
            }
            if (!is_array($webproperties)) 
            {
                $webproperties = array();
            }
            if (!is_array($profiles)) 
            {
                $profiles = array();
            }
            add_action('admin_notices', function () 
            {
?>
            <div class="error">
                <p><?php
                printf(__('GA Page View plugin is not ready. Some GA Page View setting is <strong>incorrect</strong>
                <strong>you need to set correct GA Page View Application Service Account Name and load key file </strong>.', 'wpsc') , admin_url('options-general.php?page=' . self::ADMIN_PAGE_SLUG)) ?></p>
            </div>
            <?php
            }
            , 1000);
        }
        if (is_object(self::getGoogleClient()) && strlen(self::getServiceAccountName()) > 2 && strlen(self::getKey()) > 2) 
        {
?>
                    <tr valign="top">
                        <th scope="row"><?php
            _e('GA ID:'); ?></th>
                        <td>
                            <input type="hidden" name="type" value="webproperties"/>
                            <select class="ajaxDataSelectLoad gaIdSelect" name=<?php
            echo self::GA_ID_OPTION_NAME
?> >
                                <option value="">Select ID</option>
                            <?php
            
            foreach ($accounts as $account) 
            {
                echo '<option value="' . $account['accountId'] . '"' . selected(true, $account['accountId'] == self::getGAId()) . '>' . $account['accountName'] . '</option>';
            }
?> 
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php
            _e('GA Webproperty:'); ?></th>
                        <td>
                            <input type="hidden" name="type" value="profiles"/>
                            <select class="ajaxDataSelectLoad gaWebPropertyIdSelect" name=<?php
            echo self::GA_WEBPROPERTY_ID_OPTION_NAME
?> >
                                <option value="">Select Webproperty </option>
                            <?php
            $webproperties = array();
            if (self::getGAId()) 
            {
                $webproperties = self::getWebPropertyList(self::getGAId());
            }
            
            foreach ($webproperties as $webproperty) 
            {
                echo '<option value="' . $webproperty['webpropertyId'] . '" ' . selected(true, $webproperty['webpropertyId'] == self::getGAWebPropertyId()) . '>' . $webproperty['websiteURL'] . '</option>';
            }
?> 
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php
            _e('GA Profile :'); ?></th>
                        <td>
                            <select class="gaProfileIdSelect" name=<?php
            echo self::GA_PROFILE_ID_OPTION_NAME
?> >
                                <option value="">Select Profile </option>
                            <?php
            $profiles = array();
            if (self::getGAId() && self::getGAWebPropertyId()) 
            {
                $profiles = self::getProfileList(self::getGAId() , self::getGAWebPropertyId());
            }
            
            foreach ($profiles as $profile) 
            {
                echo '<option value="' . $profile['profileId'] . '" ' . selected(true, $profile['profileId'] == self::getGAProfileId()) . '>' . $profile['profileName'] . '</option>';
            }
?> 
                            </select>
                        </td>
                    </tr>
                    <?php
        }
?>

                </table>

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php
        _e('Save Changes') ?>"/>
                </p>

            </form>
        </div>
    <?php
    }
    /**
     * Give response with status and message
     *
     * @param  strign $status status messaghe
     * @param  string $msg    response message
     */
    protected static function giveResponse($status, $msg) 
    {
        echo json_encode(array(
            'status' => $status,
            'message' => $msg,
        ));
        die();
    }
    /**
     * Show success response and kill script
     */
    protected static function giveSuccessResponse($msg) 
    {
        self::giveResponse('OK', $msg);
    }
    /**
     * Show error response with message and kill script
     * @param $msg error message
     */
    protected static function giveErrorResponse($msg) 
    {
        self::giveResponse('ERROR', $msg);
    }
    public static function getSinglePostTrack() 
    {
        if (is_null(self::$TRACK_SINGLE_POST)) 
        {
            self::$TRACK_SINGLE_POST = get_option(self::TRACK_SINGLE_POST_OPTION_NAME);
        }
        
        return (bool)self::$TRACK_SINGLE_POST;
    }
    public static function getCategoryListTrack() 
    {
        if (empty(self::$TRACK_CATEGORY_LIST)) 
        {
            self::$TRACK_CATEGORY_LIST = get_option(self::TRACK_CATEGORY_LIST_OPTION_NAME, array());
        }
        if (!is_array(self::$TRACK_CATEGORY_LIST)) 
        {
            
            return array();
        }
        
        return self::$TRACK_CATEGORY_LIST;
    }
    public static function getGAId() 
    {
        if (!self::$GA_ID) 
        {
            self::$GA_ID = get_option(self::GA_ID_OPTION_NAME);
        }
        
        return self::$GA_ID;
    }
    public static function getGAWebPropertyId() 
    {
        if (!self::$GA_WEBPROPERTY_ID) 
        {
            self::$GA_WEBPROPERTY_ID = get_option(self::GA_WEBPROPERTY_ID_OPTION_NAME);
        }
        
        return self::$GA_WEBPROPERTY_ID;
    }
    public static function getGAProfileId() 
    {
        if (!self::$GA_PROFILE_ID) 
        {
            self::$GA_PROFILE_ID = get_option(self::GA_PROFILE_ID_OPTION_NAME);
        }
        
        return self::$GA_PROFILE_ID;
    }
    public static function getServiceAccountName() 
    {
        if (!self::$SERVICE_ACCOUNT_NAME) 
        {
            self::$SERVICE_ACCOUNT_NAME = get_option(self::SERVICE_ACCOUNT_NAME_OPTION_NAME);
        }
        
        return self::$SERVICE_ACCOUNT_NAME;
    }
    public static function getKey() 
    {
        if (!self::$KEY) 
        {
            self::$KEY = get_option(self::KEY_OPTION_NAME);
        }
        
        return self::$KEY;
    }
    public static function isConfigurationComplete() 
    {
        if (strlen(self::getServiceAccountName()) > 2 && strlen(self::getKey()) > 2 && self::getGAId() && self::getGAWebPropertyId() && self::getGAProfileId()) 
        {
            
            return true;
        }
        
        return false;
    }
    protected static function isKeyValid() 
    {
        /**
         * @todo  : find way to validate this key
         */
        
        try
        {
            new Google_P12Signer(base64_decode(self::getKey()) , 'notasecret');
        }
        catch(Exception $e) 
        {
            
            return false;
        }
        
        return true;
    }
    public static function reInitOptions() 
    {
        self::$GA_ID = null;
        self::$GA_WEBPROPERTY_ID = null;
        self::$GA_PROFILE_ID = null;
        self::$GA_ID = self::getGAId();
        self::$GA_WEBPROPERTY_ID = self::getGAWebPropertyId();
        self::$GA_PROFILE_ID = self::getGAProfileId();
    }
}
if (defined('ABSPATH')) 
{
    add_action('plugins_loaded', array(
        'PiGaPageView',
        'init'
    ));
}

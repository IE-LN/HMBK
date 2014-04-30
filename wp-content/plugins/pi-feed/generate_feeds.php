<?php
set_time_limit ( 0 );
ini_set('memory_limit', '2048M');

$def = array(
    "file"=> "onswipe.feed.xml",
    "env" => 'PROD',
    "feed" => 'onswipe'
);
$params = array();

foreach ($argv as $index => $arg){
    if ($index==0) {
        continue;
    }
    list($key, $value) = explode('=', $arg);

    if(!empty($value)){
        $params[$key] = $value;
    }
}
if(isset($params['env'])){
    $_SERVER['BM_ENV'] = $params['env'];

}
else{
    $_SERVER['BM_ENV'] = 'PROD';
}

if(isset($params['site'])){
    $_SERVER['SERVER_NAME'] = $params['site'];
    $_SERVER['HTTP_HOST']  = $params['site'];
}

/** Load WordPress Bootstrap */
require_once( dirname(dirname(dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php');

add_filter('post_limits', 'set_limit');
function set_limit($limit){
    return '';
}

$params = wp_parse_args($params, $def);

$file = $params['file'];

unset($params['file']);
unset($params['env']);
unset($params['site']);

query_posts($params);

ob_start();
do_feed();
$xml = ob_get_contents();
ob_end_clean();

$handle = @fopen($file, 'wb');

@flock($handle, LOCK_EX);
@fwrite($handle, $xml);
@flock($handle, LOCK_UN);
@fclose($handle);
@umask(0000);
@chmod($file, 0666);
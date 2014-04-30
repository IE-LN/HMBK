<?php
/*
Template Name: Sign Out
*/
wp_logout();

// basically if the user came to the login from another site, then dont redirect back to that site
// otherwise attempt to redirect them back to the page they clicked logout on.
$redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
if (empty($redirect_to)) $redirect_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : trailingslashit(site_url());
$cross_site_check = preg_replace('#^'.preg_quote(site_url(), '#').'#', 'SHAZBOT', $redirect_to);
$cross_site_check = preg_replace('#^'.preg_quote(home_url(), '#').'#', 'SHAZBOT', $redirect_to);
if ($cross_site_check == $redirect_to) $redirect_to = trailingslashit(site_url());
wp_safe_redirect($redirect_to);
exit();

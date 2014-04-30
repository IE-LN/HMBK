<?php
get_header();

global $hide_pagenavi, $use_sidebar;
$use_sidebar = 'none';
$hide_pagenavi = true;
do_action('ice-login-load-template', 'modal-login-form.php');

get_footer();

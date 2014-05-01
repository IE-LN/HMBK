<?php
if($instance['css_container_class']=='most-recent-videos')
{
	$template = dirname(__FILE__).'/most.recent.filtered.sxs.php';
}else
{
	$template = dirname(__FILE__).'/most.recent.filtered.oau.php';
}
include $template;

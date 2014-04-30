<?php
if($instance['css_container_class']=='most-recent-videos')
{
	$template = __DIR__.'/most.recent.filtered.sxs.php';
}else
{
	$template = __DIR__.'/most.recent.filtered.oau.php';
}
include $template;

<?php
global $wp_query, $cat;
if (isset($wp_query->queried_object) && is_object($wp_query->queried_object) && isset($wp_query->queried_object->slug))
	$cat = $wp_query->queried_object;
get_template_part('templ-category');

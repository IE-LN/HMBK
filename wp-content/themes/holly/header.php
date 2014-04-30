<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<?php buzzmedia_top_script() ?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width" />
		<title><?php
			if (is_home() || is_front_page()) {
				bloginfo('name');
			} else {
				global $page, $paged;
				wp_title('|', true, 'right');
				bloginfo('name');
				$site_description = get_bloginfo( 'description', 'display' );
				if ($paged >= 2 || $page >= 2) echo ' | '.sprintf('Page %s', max($paged, $page));
			}
		?></title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>?v=<?php echo get_public_version();?>" />
		<link rel="icon" type="image/png" href="<?php get_template_directory_uri(); ?>/images/holly_favicon_3.png"/>
		<!--[if lt IE 9]>
			<script src="<?php get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
		<![endif]-->
		<?php if (is_singular() && get_option('thread_comments')) wp_enqueue_script( 'comment-reply' ); ?>
		<?php wp_head(); ?>
	</head>
	<?php global $use_sub_nav, $extra_body; ?>
	<body <?php body_class($extra_body) ?>>
	<?php
		if (method_exists('PiLogin', 'print_dialogs')){
			PiLogin::print_dialogs();
		}
	?>
    <?php do_action('its-shared-nav', array('theme_location' => 'cb-shared-nav', 'fallback_cb' => 'its_no_default_menu'));?>
		<div class="ads-728-center ads-top-margin ads-bottom-margin">
			<div>
				<div>
					<?php buzzmedia_ad_dart(array('728x90'), '728a') ?>
				</div>
			</div>
		</div>
		<div id="page-wrapper">
			<div class="shadow-shim left-shim"><table><tbody><tr><td class="top"></td></tr><tr><td class="middle"></td></tr><tr><td class="bottom"></td></tr></tbody></table></div>
			<div class="shadow-shim right-shim"><table><tbody><tr><td class="top"></td></tr><tr><td class="middle"></td></tr><tr><td class="bottom"></td></tr></tbody></table></div>
			<div class="shadow-shim bottom-shim"></div>
			<div id="page-inner-wrapper">
				<?php 
					$is_slim_header = (is_single() && in_array($post->post_type, array('gallery', 'attachment'))) ? true : false;
					$header_class = 'normal-height';
					if($is_slim_header) {
						$header_class = 'slim-height';
					}
				?>
				<header id="branding" role="banner" class="<?php empty($use_sub_nav) ? 'extra-header-margin' : '' ?> <?php echo $header_class; ?>">
					<h1 class="site-title"><a href="<?php home_url('/') ?>" title="<?php esc_attr(get_bloginfo('name')) ?>"><?php get_bloginfo('name') ?></a></h1>
					<div class="branding-container">
						<a href="<?php apply_filters('the_permalink', home_url('/')) ?>" title="<?php esc_attr(get_bloginfo('name')) ?>">
							<?php 
							//var_dump($post);
							if ($is_slim_header) {
								echo '<div class="slim-branding-img"></div>';
							} else {
								echo '<div class="branding-img"></div>';
							}
							?>
						</a>
						<nav id="main-nav" role="navigation">
							<?php wp_nav_menu(array('theme_location' => 'main-menu')); ?>
							<div class="clear"></div>
						</nav>
					</div>

					<?php /*
					<div class="left-column">
						<a href="<?php home_url('/') ?>" title="<?php esc_attr(get_bloginfo('name')) ?>"><div class="left-branding-image"></div></a>
					</div>

					<div class="right-column">
						<a href="<?php home_url('/') ?>" title="<?php esc_attr(get_bloginfo('name')) ?>"><div class="right-branding-image"></div></a>
						<nav id="main-nav" role="navigation">
							<?php wp_nav_menu(array('theme_location' => 'main-menu')); ?>
							<div class="clear"></div>
						</nav>
					</div>

					<div class="bottom-border clear"><div class="bottom-branding-image"></div></div>
					*/ ?>
					<?php if (!is_home() && (!is_single() || !in_array($post->post_type, array('gallery', 'attachment')))): ?>
						<?php ob_start(); ?>
						<?php if (!dynamic_sidebar('header-carousel-topbar')): ?><?php endif; ?>
						<?php $out = trim(ob_get_contents()); ?>
						<?php ob_end_clean();?>
						<?php if (!empty($out)): ?>
							<div class="carousel-container <?php is_descendant_of('photos', 'page', true) ? '' : 'add-bottom-gap' ?>" id="featured"><?php $out ?></div>
						<?php endif; ?>
					<?php endif;?>

					<?php if (!empty($use_sub_nav)): ?>
						<nav id="main-sub-nav" role="sub-navigation">
							<?php wp_nav_menu(array('theme_location' => $use_sub_nav, 'fallback_cb' => 'its_no_default_menu')); ?>
							<div class="clear"></div>
						</nav>
					<?php endif; ?>
				</header>
				
				<div id="main">
					<?php global $is_special_page; ?>
					<?php if ($is_special_page): ?><div id="content-column"><?php endif; ?>

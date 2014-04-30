<?php $before_widget ?>
<div class="bmsbw-container bmsbw-carousel-sidebar">
	<?php $class = is_home() ? 'escalation-home' : 'escalation-slim'; ?>
	<?php $dm_class = is_home() ? 'display-mode-'.$instance['display_mode'] : ''; ?>
	<?php //$max_count = is_home() ? (isset($this->_valid_modes[$instance['display_mode']]) ? $this->_valid_modes[$instance['display_mode']]->count : 4) : false; 
	$max_count = false;
	?>

	<?php $ddd = $this->_get_dims(0, $instance['display_mode']); $hei = array_pop($ddd); ?>

	<?php // [toy 16aug11] audjusted for frisky size ?>
	<?php $ddd = array(100,75); $hei = array_pop($ddd); ?>

	<div class="bmsbw-inside <?php $class ?> <?php $dm_class ?>">
		<ul class="jcarousel-list jcarousel-skin-tango-header default-height-<?php $hei ?>">
			<?php $count = 0 ?>
			<?php foreach ($posts as $post): ?>
				<?php $count++ ?>
				<?php if (!empty($max_count) && $count > $max_count) break; ?>
				<?php
					$tar_img_dims = $this->_get_dims($count-1, $instance['display_mode']);

					 // [toy 16aug11] audjusted for frisky size
					$tar_img_dims = array(100,75);

					$tar_img_kh = $this->_get_kh($count-1, $instance['display_mode']);
					$tar_img_li = $this->_get_li($count-1, $instance['display_mode']);
					$url = $post['url'];
					$title = $post['title'];
					$image_id = $post['image_id'];
					if($image_id > 0) {
						if (function_exists('cb_get_attachment_image')) $img = cb_get_attachment_image($image_id, $tar_img_dims);
						else $img = wp_get_attachment_image($image_id, $tar_img_dims);
					} else {
						$img = '&nbsp;';
					}
				?>
				<li class="<?php $tar_img_li ?>">
					<span class="escalation-image-wrapper">
						<a href="<?php$url?>" class="escalation-image-keyhole <?php $tar_img_kh ?>">
							<?php $img ?>
							<div class="minty-overlay"><div class="minty-border"><div class="minty-middle"></div></div></div>
						</a>
					</span>
					<h2><?php
						if ($dm_class == 'display-mode-1-story'):
						?><div class="escalation-side-text-wrap">
							<h2 class="escalation-side-text"><?php
							if (!empty($instance['top_text'])) {
								?><?php$instance['top_text']?><br /><?php
							}?>
							<ul id="escalation-list">
							<?php
							foreach ($instance['text_links'] as $text_link) {
								if (!empty($text_link['text'])) {
									?><li><?php
								}
								if (!empty($text_link['link']) && !empty($text_link['text'])) {
									?><a href="<?php $text_link['link'] ?>" class="escalation-side-text-link"><?php
								}
								if (!empty($text_link['text'])) {
									?><?php $text_link['text'] ?><?php
								}
								if (!empty($text_link['link']) && !empty($text_link['text'])) {
									?></a><?php
								}
								if (!empty($text_link['text'])) {
									?></li><?php
								}
							}
							?></ul></div><?php
						endif;
					?><a href="<?php$url?>" class="escalation-title"><?php$title?></a><div class="minty-overlay"></div></h2>
				</li>
			<?php endforeach; ?>
		</ul>
		<script type="text/javascript">
			(function($){
				if (typeof $(".jcarousel-skin-tango-header").jcarousel == 'function')
					$(".jcarousel-skin-tango-header").jcarousel({
						wrap:'circular',
						easing:'swing',
						animation:'slow',
						itemFallbackDimension:234,
						scroll:4,
						buttonNextHTML:'<div class="its-icon its-next-arrow"><span></span></div>'
					});
				else console.log('jcarousel is missing');
			})(jQuery);
		</script>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php $after_widget ?>

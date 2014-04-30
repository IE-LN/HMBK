<?php $before_widget ?>
<?php if(!empty($instance['title'])) :?>
<h3 class="bmsbw-title widget-title"><?php $instance['title'] ?></h3>
<?php endif; ?>
<div class="bmsbw-container bmsbw-twitter-feed">
	<div class="bmsbw-inside">
		<?php wp_print_scripts(array('twitter-widget-api')); ?>
		<script>
			new TWTR.Widget({
				version: 2,
				type: 'profile',
				rpp: <?php $instance['number_to_show'] ?>,
				interval: 3600,
				width: 'auto',
				height: 'auto',
				theme: {
					shell: {
						background: '<?php $instance['container_bg'] ?>',
						color: '<?php $instance['container_color'] ?>'
					},
					tweets: {
						background: '<?php $instance['tweets_bg'] ?>',
						color: '<?php $instance['tweets_color'] ?>',
						links: '<?php $instance['tweets_links'] ?>'
					}
				},
				features: {
					//scrollbar: <?php $instance['show_scrollbar'] ?>,
					scrollbar: <?php ($instance['show_scrollbar'] == 1) ? 'true' : 'false';  ?>,
					loop: false,
					live: true,
					behavior: 'all'
				}
			}).render().setUser('<?php $instance['follow_twitter'] ?>').start();
		</script>
		<div class="cleaner"></div>
	</div>
</div>
<div class="cleaner"></div>
<?php $after_widget ?>

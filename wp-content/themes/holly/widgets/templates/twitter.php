<?= $before_widget ?>
<?php if(!empty($instance['title'])) :?>
<h3 class="bmsbw-title widget-title"><?= $instance['title'] ?></h3>
<?php endif; ?>
<div class="bmsbw-container bmsbw-twitter-feed">
	<div class="bmsbw-inside">
		<?php wp_print_scripts(array('twitter-widget-api')); ?>
		<script>
			new TWTR.Widget({
				version: 2,
				type: 'profile',
				rpp: <?= $instance['number_to_show'] ?>,
				interval: 3600,
				width: 'auto',
				height: 'auto',
				theme: {
					shell: {
						background: '<?= $instance['container_bg'] ?>',
						color: '<?= $instance['container_color'] ?>'
					},
					tweets: {
						background: '<?= $instance['tweets_bg'] ?>',
						color: '<?= $instance['tweets_color'] ?>',
						links: '<?= $instance['tweets_links'] ?>'
					}
				},
				features: {
					//scrollbar: <?= $instance['show_scrollbar'] ?>,
					scrollbar: <?= ($instance['show_scrollbar'] == 1) ? 'true' : 'false';  ?>,
					loop: false,
					live: true,
					behavior: 'all'
				}
			}).render().setUser('<?= $instance['follow_twitter'] ?>').start();
		</script>
		<div class="cleaner"></div>
	</div>
</div>
<div class="cleaner"></div>
<?= $after_widget ?>

<?= $before_widget ?>
<div class="bmsbw-container bmsbw-embedded-content-flash">
	<?php if (!empty($instance['title'])): ?>
		<?= $before_title ?><h3 class="bmsbw-title"><?= $instance['title'] ?></h3><?= $after_title ?>
	<?php endif; ?>
	<div class="bmsbw-inside blackmaroon">
		<?= $instance['embed'] ?>
	</div>
</div>
<?= $after_widget ?>

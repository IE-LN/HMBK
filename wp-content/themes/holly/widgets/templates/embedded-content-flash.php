<?php $before_widget ?>
<div class="bmsbw-container bmsbw-embedded-content-flash">
	<?php if (!empty($instance['title'])): ?>
		<?php $before_title ?><h3 class="bmsbw-title"><?php $instance['title'] ?></h3><?php $after_title ?>
	<?php endif; ?>
	<div class="bmsbw-inside blackmaroon">
		<?php $instance['embed'] ?>
	</div>
</div>
<?php $after_widget ?>

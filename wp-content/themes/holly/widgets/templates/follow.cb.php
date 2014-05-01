<?= $before_widget ?>
<div class="bmsbw-container bmsbw-followCb">
	<div class="bmsbw-inside blackmaroon">
			<ul>
				<li class="bmsbw-follow-fb"><a href="http://www.facebook.com/CELEBUZZ" target="_blank"></a></li>
				<li class="bmsbw-follow-tw"><a href="http://twitter.com/CELEBUZZ" target="_blank"></a></li>
				<li class="bmsbw-follow-rss"><a href="http://www.google.com/ig/add?feedurl=http%3A%2F%2Fwww.celebuzz.com%2Frss%2Fstories-rss.xml" target="_blank"></a></li>
				<?php if ($instance['show_mobile']): ?>
					<li class="bmsbw-follow-cell"><a href="<?= home_url('/mobile/') ?>"></a></li>
				<?php endif; ?>
			</ul>
	</div>
</div>
<div class="cleaner"></div>
<?= $after_widget ?>

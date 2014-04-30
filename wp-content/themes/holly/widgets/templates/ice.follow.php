<?php $before_widget ?>
<div class="bmsbw-container bmsbw-followCb bmsbw-followICE">
	<div class="bmsbw-inside blackmaroon">
		<table>
			<tbody>
				<tr>
					<td class="follow-label">
						<div class="follow-me-before"><a href="<?php$instance['follow_facebook'];?>" target="_blank"><?php !empty($instance['title']) ? $instance['title'] : 'FOLLOW CELEBUZZ';?></a></div>
					</td>
					<td class="follow-spacer">&nbsp;</td>
					<td class="follow-buttons <?php !empty($instance['follow_custom']) ? 'with-custom' : '' ?>">
						<ul>
							<li class="bmsbw-follow-fb"><a href="<?php$instance['follow_facebook'];?>" target="_blank"></a></li>
							<li class="bmsbw-follow-tw"><a href="<?php$instance['follow_twitter'];?>" target="_blank"></a></li>
							<li class="bmsbw-follow-rss"><a href="<?php$instance['follow_rss'];?>" target="_blank"></a></li>
							<?php if(!empty($instance['follow_custom'])) { ?>
								<li class="bmsbw-follow-custom"><a href="<?php$instance['follow_custom'];?>"></a></li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="cleaner"></div>
<?php $after_widget ?>

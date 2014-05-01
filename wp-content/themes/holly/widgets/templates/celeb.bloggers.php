<?= $before_widget ?>
<div class="bmsbw-container bmsbw-celeb-bloggers">
<h3 class="bmsbw-title widget-title">CELEBRITY BLOGGERS<div class="bmsbw-celeb-bloggers-sub-title">STRAIGHT FROM THE SOURCE</div></h3>
	<div class="bmsbw-celeb-bloggers-tsub-title-icon"></div>
	<div class="bmsbw-inside blackmaroon">
		<script language="javascript" type="text/javascript">
			var _bloggers_first_template = '<li class="bmsbw-post-with-thumb"> <table> <tbody> <tr> <td class="bmsbw-valign-fix-image"><a href="%%READMORE%%" title="Read More" class="bmsbw-image-keyhole t94x94"><img width="94" src="%%ICON%%"></a></td> <td class="bmsbw-valign-fix-title"> <h2 class="latest-blogger"><a target="_blank" href="%%LINK%%" title="Read More">%%TITLE%%</a></h2> <div class="latest-byline-wrapper"><span>By <a href="%%BPERMA%%">%%BNAME%%</a></span></div> </td> </tr> </tbody> </table> </li>';
			var _bloggers_other_template = '<li class="bmsbw-post-with-thumb"> <table> <tbody> <tr> <td class="bmsbw-valign-fix-image"><a href="%%READMORE%%" title="Read More" class="bmsbw-image-keyhole bmsbw-50x50-keyhole"><img src="%%ICON%%"></a></td> <td class="bmsbw-valign-fix-title"> <h2><a target="_blank" href="%%LINK%%" title="Read More">%%TITLE%%</a></h2> <div class="byline-wrapper"><span>By <a href="%%BPERMA%%">%%BNAME%%</a></span></div> </td> </tr> </tbody> </table> </li>';
		</script>
		<ul class="bmsbw-post-list bmice-celebbloggers-widget" posts="<?= $instance['posts_to_show'] ?>" thumbs="<?= $instance['thumbs_to_show'] ?>">
		</ul>
	</div>
    <div class="bmsbw-bottom blue"><a class="see-more" href="<?=site_url();?>/celeb-bloggers/">See More &raquo;</a>
		<div class="clear"></div>
	</div>
</div>
<?= $after_widget ?>

<?= $before_widget ?>
<div class="bmsbw-container bmsbw-photo-search-form bmsbw-gutter-cover">
	<div class="bmsbw-inside">
		<div class="fake-search-container">
			<form id="search-form-cb" method="GET" action="<?php bloginfo('url'); ?>/" onsubmit="return ice_search_submit();">
				<input type="text" id="s" class="inputText" name="s" value="<?=(isset($_GET['s'])) ? $_GET['s'] : $instance['text'];?>"  onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" />
				<input type="submit" value="" class="button" />
			</form>
		</div>
	</div>
</div>
<?= $after_widget ?>
<script>
function ice_search_submit()
{
	<?php
	$o = apply_filters('ice-search-get-settings', false);
	$args = array('ice_search'=>'search','search_value'=>'%s','search_type'=>$instance['type']);
	// old code
	//$args = array('ice_search'=>'s','s'=>'%s','search_type'=>$instance['type']);
	?>
	<?php if($o){ ?>
	var rule='<?= apply_filters('ice-search-link', 'search/%s', $args);?>';
	var ws='<?=$o['word_separator'];?>';
	<?php }else{ ?>
	var rule='<?php bloginfo('url'); ?>/search/%s';
	var ws='%20';
	<?php } ?>
	var v = document.getElementById('s').value.replace(/ /g, ws);
	var url = rule.replace('%s', v);
	window.location = url;
	return false;
}
</script>

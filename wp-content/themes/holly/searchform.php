	<form id="search-form-cb" method="POST" action="<?= home_url() ?>/search/" 
	onsubmit="this.action += filterq()" >
		<div class="search-wrapper">
			<div class="search-wrapper-border">
				<div class="search-wrapper-inner">
					<?php $default_val = 'Search ' . get_bloginfo(); 

					$s = get_query_var('s');
					if(empty($s)) $s = $default_val;
					?>
					<input type="image" class="floatR right search-submit" id="searchSubmit" name="searchSubmit" value="Search" src="<?= BMICE_URL ?>images/search-widget-btn.png" />
					<input type="text" id="s" class="inputText" name="s" value="<?=$s?>"
							onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" />
				</div>
			</div>
		</div>
	</form>

<?= $before_widget ?>
<div class="bmsbw-container bmsbw-global-search-form bmsbw-thin">
	<div class="bmsbw-inside blackmaroon">
		<form id="search-form-cb" method="GET" action="/">
			<div id="search_selector">
				<div id="search_icon" class="list_icon"></div>
				<div class="special_dropdown_arrow"></div>
				<ul>
					<li class="search_all firsty"><div class="list_icon"></div>All results</li>
					<li class="only_photos"><div class="camera_icon"></div>Photos results</li>
				</ul>
			</div>
		
			<input type="hidden" id="search_type" name="search_type" value="<?=isset($_COOKIE['search_pref']) && !empty($_COOKIE['search_pref']) ? $_COOKIE['search_pref']: 'all';?>" />
			<input type="text" id="s" class="inputText" name="s" value="<?=(isset($_GET['s'])) ? $_GET['s'] : 'Search Celebuzz';?>"  onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" />
			<input type="image" class="floatR" id="searchSubmit" name="searchSubmit" value="Search" src="<?= BMICE_URL ?>images/search-widget-btn.png" />
		</form>
	</div>
</div>
<script type="text/javascript">

jQuery(function($) {
	$("#search_selector").click(function() {
		$("#search_selector ul").toggle();
	});
	$(".only_photos").click(function(){
		$(".search_all").show();
		$("#search_type").val("gallery");
		$("#search_icon").removeClass("list_icon");
		$("#search_icon").addClass("camera_icon");
		document.cookie = 'search_pref=' + $("#search_type").val();
	});
	$(".search_all").click(function(){
		$(".only_photos").show();
		$("#search_type").val("all");
		$("#search_icon").removeClass("camera_icon");
		$("#search_icon").addClass("list_icon");
		document.cookie = 'search_pref=' + $("#search_type").val();
	});
});
</script>
<?= $after_widget ?>

// buzzbox
jQuery(document).ready(function(){
  jQuery("#buzzbox_title ul li").click( function(){buzzbox_tabklik(this.id);});
});
function buzzbox_tabklik(tab){
	jQuery("#buzzbox > div:not(#buzzbox_title)").hide();
	jQuery("#buzzbox_title ul li").removeClass("TAB_ON");
	jQuery("#"+tab).addClass("TAB_ON");
	jQuery("#"+tab+"_list").slideDown("slow");
};

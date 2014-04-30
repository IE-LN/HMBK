var Buzzworthy = (function($){
	
	var buzzworthy_div;

	return {

		//
		//
		//
		init: function(){

			buzzworthy_div = document.getElementById('buzzworthy');
			
			if (buzzworthy_div == null){
				return;
			} else {
				$.ajax({
					type : "GET",
					dataType : "jsonp",
					url : 'http://buzzworthy.buzzbytes.net/module/' + buzzworthy_div.innerHTML + '/?callback=true'
				});
			}
		},

		//
		//
		//
		callback: function(data){
			var content_string = "";

			content_string += "<h3><span>Buzz</span>worthy</h3>";

			if(data.length > 0){
				content_string += "<img src='" + data[0].image + "'>";
				content_string += "<ul>";
				for (var i=0; i<data.length; i++){
					content_string += "<li>";
					content_string += "<a href='"+ data[i].url+ "' data-source=\"" + data[i].source + "\">" + data[i].title + "</a>";
					content_string += " - " + data[i].source;
					content_string += "</li>";
				}
				content_string += "</ul>";
			}

			buzzworthy_div.innerHTML = content_string;
			buzzworthy_div.style.display = "block";

			//
			// Google Event Tracking
			//
			$("#buzzworthy a").live("click", function(event){
				event.preventDefault();

				_gaq.push(['_trackEvent', 'Buzzworthy', $(this).data("source"), this.href]);

				// once the tracking is complete, then go to the actual link
				var link = this;
				window.setTimeout(function(){
					_gaq.push(function(){
						window.location = link.href;
					});
				}, 300);
			});
		}
	};

})(jQuery);


jQuery(document).ready(function(){
	Buzzworthy.init();
});
	
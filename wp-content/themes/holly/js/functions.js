function revealModal(type){//'modalSignIn') {
       if (type == 'modalSignIn') {
               jQuery( "#sign-up-dialog" ).dialog( "close" );
               jQuery( "#forgot-dialog" ).dialog( "close" );
               jQuery( "#sign-in-dialog" ).dialog( "open" );
       }
       return false;
}

(function($) {
	$(document).ready(function() {
		$('#respond a.sign-in').click(function() {
			revealModal('modalSignIn');
			return false;
		});
		$('.commentlist-ice a.comment-reply-login').click(function() {
			revealModal('modalSignIn');
			return false;
		});
	});
})(jQuery);

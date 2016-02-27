
        $(function() {
        	$( ".registration" ).click(function() {
  				$('#register').slideDown()
            
                $('html, body').animate({
                    scrollTop: $($('#register')).offset().top
                }, 500);
                return false;
			});
 	
		});

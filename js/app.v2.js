    var charts = [];

    function toPercentage(num, div){
      return  Math.floor(((num / div) * 100));
    }

    
    $(function() {


			$('#map-toggle').click(function () {
                if ($('#location-holder').hasClass('location-fade')) {
                    $('#location-holder').removeClass('location-fade');
                } else {
                    $('#location-holder').addClass('location-fade');
                }
            });



        	$( ".registration" ).click(function() {
  				$('#register').slideDown()
            
                $('html, body').animate({
                    scrollTop: $($('#register')).offset().top
                }, 500);
                return false;
			});
 	




            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 80,
                center: true,
                dots: true,
                nav: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    960: {
                        items: 3
                    },
                    1200: {
                        items: 4
                    }
                }
            });


            //http://imakewebthings.com/waypoints/guides/jquery-zepto/
            /*
            var waypoint = new Waypoint({
                element: document.getElementById('wayX'),
                handler: function (direction) {
                    //notify(this.id + ' hit'); animated shake
                    $("#timeX").removeClass("animated shake");
                    $("#timeX").addClass("animated shake");
                }
            });
            */


            //http://ianlunn.co.uk/plugins/jquery-parallax/
            //.parallax(xPosition, speedFactor, outerHeight) options:
            //xPosition - Horizontal position of the element
            //inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
            //outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport
            //$('#countdown-wrapper').parallax("50%", 0.5);
            //jQuery('.parallaxBg1').parallax("50%", 0.4);




            //https://jsfiddle.net/empht9sw/2/
            //http://codecanyon.net/item/circular-countdown/full_screen_preview/3100472
            var options = {
              scaleColor: false,
              trackColor: 'rgba(255,255,255,0.3)',
              barColor: '#E7F7F5',
              lineWidth: 10,
              lineCap: 'butt',
              size: 140,
              animate: 1000
            };


            var optionsDays = {
              scaleColor: false,
              trackColor: 'rgba(255,255,255,0.3)',
              barColor: '#F1D7F6',
              lineWidth: 30,
              lineCap: 'butt',
              size: 350,
              animate: 1000
            };


            var optionsSeconds = {
              scaleColor: false,
              trackColor: 'rgba(255,255,255,0.3)',
              barColor: '#C0C0C0',
              lineWidth: 6,
              lineCap: 'butt',
              size: 65,
              animate: 50,
              scaleColor: '#dfe0e0'
            };



           charts.push(new EasyPieChart(document.getElementById("day"), optionsDays));
           charts.push(new EasyPieChart(document.getElementById("hour"), options));
           charts.push(new EasyPieChart(document.getElementById("min"),  options));
           charts.push(new EasyPieChart(document.getElementById("sec"),  optionsSeconds));


          
          $('#clock').countdown('2016/12/27 09:00:00', function(event) {
           

            // var $this = $(this).html(event.strftime(''
            //    + '<label><span>%D</span> days </label>'
            //    + '<label><span>%H</span> hr </label>'
            //    + '<label><span>%M</span> min </label>'
            //    + '<label><span>%S</span> sec</label>'));


               var secs = parseFloat(event.strftime('%S'));
               var mins = parseFloat(event.strftime('%M'));
               var hours = parseFloat(event.strftime('%H'));
               var days = parseFloat(event.strftime('%D'));

               $("#day span").html(days);
               charts[0].update(toPercentage(days, 365));

               $("#hour span").html(hours);
               charts[1].update(toPercentage(hours, 24));

               $("#min span").html(mins);
               charts[2].update(toPercentage(mins, 60));     
            
               $("#sec span").html(secs);
               charts[3].update(toPercentage(secs, 60));        

          
                 
          });


	});






    

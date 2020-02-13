'use strict';
(function($){
	/**
	  * Function written to load slick slider based on data attributes.
	**/
	if($('[data-slider="slick"]').length > 0 ){
		$('[data-slider="slick"]').each(function(){
			var slidestoshow = $(this).data('slidestoshow');
			var slidestoscroll = $(this).data('slidestoscroll');
			var arrows = $(this).data('arrows');
			var autoplay = $(this).data('autoplay');
			var speed = $(this).data('speed');
			$(this).slick({
			  "slidesToShow": slidestoshow,
			  "slidesToScroll": slidestoscroll,
			  "arrows": arrows,
			  "autoplay": autoplay,
			  "autoplaySpeed": speed,
			  prevArrow: '<a class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>',
			  nextArrow: '<a class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>',
			  responsive: [
							 {
								breakpoint: 1681,
								settings: {
								  slidesToShow: 1,
								  slidesToScroll: 1,
								}
							 },
									{
								breakpoint: 1367,
								settings: {
								  slidesToShow: 3,
								  slidesToScroll: 1,
								}
							 },
								{
								breakpoint: 1250,
								settings: {
								  slidesToShow: 2,
								  slidesToScroll: 1,
								}
							 },
							{
								breakpoint: 992,
								settings: {
								  slidesToShow: 2,
								  slidesToScroll: 1,
								}
							 },
							 {
								breakpoint: 580,
								settings: {
								  slidesToShow: 1,
								  slidesToScroll: 1,
								}
							 },	            
						  ]
			});
		});
	}
})(jQuery);
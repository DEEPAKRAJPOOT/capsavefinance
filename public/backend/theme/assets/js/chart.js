'use strict';
(function($) {
	/**
	  * Function written to load sparkline chart based on data attributes.
	**/
	var PrtmSparkline = function(){
		if($('[data-chart="sparkline"]').length > 0 ){
			$('[data-chart="sparkline"]').each(function(){
				var values = $(this).data('values');
				var type = $(this).data('type') ? $(this).data('type') : 'bar';
				var width = $(this).data('width');
				var height = $(this).data('height');
				var range = $(this).data('range');
				var linecolor = $(this).data('linecolor');
				var fillcolor = $(this).data('fillcolor');
				var hllcolor = $(this).data('hllcolor');
				var hlscolor = $(this).data('hlscolor');
				var slicecolors = $(this).data('slicecolors');
				var barwidth = $(this).data('barwidth');
				var barspacing = $(this).data('barspacing');
				var barcolor = $(this).data('barcolor');
				var negcolor = $(this).data('negcolor');
				var poscolor = $(this).data('poscolor');
				var tarcolor = $(this).data('tarcolor');
				var percolor = $(this).data('percolor');
				var medcolor = $(this).data('medcolor');
				var boxfcolor = $(this).data('boxfcolor');
				var linewidth = $(this).data('linewidth');
				var minSpotColor = $(this).data('minSpotColor');
				$(this).sparkline($(this).data('values'), {
					type: type, 
					height: height, 
					width: width, 
					chartRangeMax: range,
					lineColor: linecolor,
						fillColor: fillcolor,
						highlightLineColor: hllcolor,
						highlightSpotColor: hlscolor, 
						sliceColors: ['#5e6db3', '#00ca95','#fd7b6c'],
						lineWidth: linewidth,
						barWidth: barwidth,
						barSpacing: barspacing,
						barColor: barcolor,
						negBarColor: negcolor,
						posBarColor: poscolor,
						targetColor: tarcolor,
						performanceColor: percolor,
						boxFillColor: boxfcolor,
						 medianColor: medcolor,
							  minSpotColor: minSpotColor,
						disableHiddenCheck: true
				});
			});
		}
	}

	/**
	  * Function written to load easypie chart based on data attributes.
	**/
	if($('[data-chart="easypie"]').length > 0){
		$('[data-chart="easypie"]').each(function(){
			var animateValue = $(this).data('animate') ? $(this).data('animate') : '2000';
			var sizeValue = $(this).data('size') ? $(this).data('size') : '200';
			var linewidth = $(this).data('linewidth') ? $(this).data('linewidth') : '5' ;
			var barcolor = $(this).data('barcolor') ? $(this).data('barcolor') : '#f44236' ;
			var trackcolor = $(this).data('trackcolor') ? $(this).data('trackcolor') : '#ddd' ;
			var scale = $(this).data('scale');
			var update = $(this).data('update');
	
			$(this).easyPieChart({
				  animate: animateValue,
				  size: sizeValue,
				  lineWidth: linewidth,
				  barColor: barcolor,
				  trackColor: trackcolor,
				  scaleColor: scale,
				  onStep: function(from, to, percent) {
						$(this.el).find('.percent').text(Math.round(percent));
				}
			 });
			 if(update){
				 var chart = window.chart = $(this).data('easyPieChart');
				 $('.easydemo_update').on('click', function() {
					  chart.update(Math.random()*200-100);
				 });
			}
		});
	}

	/**
	  * Function written to load peity charts based on data attributes.
	**/
	if($('[data-chart="peity"]').length > 0){
		$('[data-chart="peity"]').each(function(){
			var type = $(this).data('type') ? $(this).data('type') : 'pie';
			var update = $(this).data('update');
			var updatingChart=$(this).peity(type,{});
			if(update){
				 setInterval(function() {
					  var random = Math.round(Math.random() * 10)
					  var values = updatingChart.text().split(",")
					  values.shift()
					  values.push(random)
	
					  updatingChart
					  .text(values.join(","))
					  .change()
				 }, 1000);
			}
		});
	}
	PrtmSparkline();
	$(window).resize(function(){
		PrtmSparkline();
	});
})(jQuery);
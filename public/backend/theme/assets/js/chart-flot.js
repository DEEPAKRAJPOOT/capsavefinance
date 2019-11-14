'use strict';
/**
  * Function written to load pie flotchart.
**/
function prtm_pie_chart(){
	var data = [];
	var series = Math.floor(Math.random() * 10) + 1;
	series = series < 5 ? 5 : series;
    var colors =['#5e6db3','#00ca95','#31cff9','#f17316','#fd7b6c','#d24636','#7e8ac2']
	for (var i = 0; i < series; i++) {
		data[i] = {
			label: "Series" + (i + 1),
			color: colors[i],
			data: Math.floor(Math.random() * 100) + 1
		};
	}

	// GRAPH 1
	if ($('.pie_chart_1').length > 0) {
		$.plot($(".pie_chart_1"), data, {
			series: {
				pie: {
					show: true
				}
			},
			legend: {
				show: false
			}
		});
	}

	// GRAPH 2
	if ($('.pie_chart_2').length > 0) {
		$.plot($(".pie_chart_2"), data, {
			series: {
				pie: {
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 1,
						formatter: function(label, series) {
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
						},
						background: {
							opacity: 0.8
						}
					}
				}
			},
			legend: {
				show: false
			}
		});
	}

	// GRAPH 3
	if ($('.pie_chart_3').length > 0) {
		$.plot($(".pie_chart_3"), data, {
			series: {
				pie: {
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 3 / 4,
						formatter: function(label, series) {
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
						},
						background: {
							opacity: 0.5
						}
					}
				}
			},
			legend: {
				show: false
			}
		});
	}

	// GRAPH 5
	if ($('.pie_chart_4').length > 0) {
		$.plot($(".pie_chart_4"), data, {
			series: {
				pie: {
					show: true,
					radius: 3 / 4,
					label: {
						show: true,
						radius: 3 / 4,
						formatter: function(label, series) {
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
						},
						background: {
							opacity: 0.5,
							color: '#000'
						}
					}
				}
			},
			legend: {
				show: false
			}
		});
	}

	// GRAPH 9
	if ($('.pie_chart_5').length > 0) {
		$.plot($(".pie_chart_5"), data, {
			series: {
				pie: {
					show: true,
					radius: 1,
					tilt: 0.5,
					label: {
						show: true,
						radius: 1,
						formatter: function(label, series) {
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
						},
						background: {
							opacity: 0.8
						}
					},
					combine: {
						color: '#999',
						threshold: 0.1
					}
				}
			},
			legend: {
				show: false
			}
		});
	}

	// DONUT
	if ($('#donut').length > 0) {
		$.plot($("#donut"), data, {
			series: {
				pie: {
					innerRadius: 0.5,
					show: true
				}
			}
		});
	}

	// INTERACTIVE
	if ($('.interactive').length > 0) {
		$.plot($(".interactive"), data, {
			series: {
				pie: {
					show: true
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			}
		});
		$(".interactive").bind("plothover", pieHover);
		$(".interactive").bind("plotclick", pieClick);
	}

	function pieHover(event, pos, obj) {
		if (!obj)
			return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		$("#hover").html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + obj.series.label + ' (' + percent + '%)</span>');
	}

	function pieClick(event, pos, obj) {
		if (!obj)
			return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		alert('' + obj.series.label + ': ' + percent + '%');
	}
}

/**
  * Function written to load image plots flotchart.
**/
function prtm_error_bars(){
	function drawArrow(ctx, x, y, radius){
		ctx.beginPath();
		ctx.moveTo(x + radius, y + radius);
		ctx.lineTo(x, y);
		ctx.lineTo(x - radius, y + radius);
		ctx.stroke();
	}

	function drawSemiCircle(ctx, x, y, radius){
		ctx.beginPath();
		ctx.arc(x, y, radius, 0, Math.PI, false);
		ctx.moveTo(x - radius, y);
		ctx.lineTo(x + radius, y);
		ctx.stroke();
	}

	var data1 = [
		[1,1,.5,.1,.3],
		[2,2,.3,.5,.2],
		[3,3,.9,.5,.2],
		[1.5,-.05,.5,.1,.3],
		[3.15,1.,.5,.1,.3],
		[2.5,-1.,.5,.1,.3]
	];

	var data1_points = {
		show: true,
		radius: 5,
		fillColor: "blue", 
		errorbars: "xy", 
		xerr: {show: true, asymmetric: true, upperCap: "-", lowerCap: "-"}, 
		yerr: {show: true, color: "red", upperCap: "-"}
	};

	var data2 = [
		[.7,3,.2,.4],
		[1.5,2.2,.3,.4],
		[2.3,1,.5,.2]
	];

	var data2_points = {
		show: true,
		radius: 5,
		errorbars: "y", 
		yerr: {show:true, asymmetric:true, upperCap: drawArrow, lowerCap: drawSemiCircle}
	};

	var data3 = [
		[1,2,.4],
		[2,0.5,.3],
		[2.7,2,.5]
	];

	var data3_points = {
		//do not show points
		radius: 0,
		errorbars: "y", 
		yerr: {show:true, upperCap: "-", lowerCap: "-", radius: 5}
	};

	var data4 = [
		[1.3, 1],
		[1.75, 2.5],
		[2.5, 0.5]
	];

	var data4_errors = [0.1, 0.4, 0.2];
	for (var i = 0; i < data4.length; i++) {
		data4_errors[i] = data4[i].concat(data4_errors[i])
	}

	var data = [
		{color: "#5e6db3", points: data1_points, data: data1, label: "data1"}, 
		{color: "#d24636",  points: data2_points, data: data2, label: "data2"},
		{color: "#00ca95", lines: {show: true}, points: data3_points, data: data3, label: "data3"},
		// bars with errors
		{color: "#fd7b6c", bars: {show: true, align: "center", barWidth: 0.25}, data: data4, label: "data4"},
		{color: "#fd7b6c", points: data3_points, data: data4_errors}
	];
	if($(".error-bars").length > 0){
		$.plot($(".error-bars"), data , {
			legend: {
				position: "sw",
				show: true
			},
			series: {
				lines: {
					show: false
				}
			},
			xaxis: {
				min: 0.6,
				max: 3.1
			},
			yaxis: {
				min: 0,
				max: 3.5
			},
			zoom: {
				interactive: true
			},
			pan: {
				interactive: true
			},
            grid: {
				borderWidth: 0
			}
		});
	}
}

/**
  * Function written to load stacking flotchart.
**/
function prtm_stacking(){
	var d1 = [];
	for (var i = 0; i <= 10; i += 1) {
		d1.push([i, parseInt(Math.random() * 30,30)]);
	}

	var d2 = [];
	for (var i = 0; i <= 10; i += 1) {
		d2.push([i, parseInt(Math.random() * 30,30)]);
	}

	var d3 = [];
	for (var i = 0; i <= 10; i += 1) {
		d3.push([i, parseInt(Math.random() * 30,30)]);
	}

	var stack = 0,
		bars = true,
		lines = false,
		steps = false;
    var data = [
        {color:'#5e6db3', data: d1},
        {color:'#00ca95', data: d2},
        {color:'#31cff9', data: d3},
    ];
	function plotWithOptions() {
		if ($(".stacking").length > 0){
			$.plot(".stacking", data, {
				series: {
					stack: stack,
					lines: {
						show: lines,
						fill: true,
						steps: steps
					},
					bars: {
						show: bars,
						barWidth: 0.6
					}
				},
                grid: {
				borderWidth: 0
			    }
			});
		}
	}

	plotWithOptions();

	$(".stackControls button").on('click',function (e) {
		e.preventDefault();
		stack = $(this).text() == "With stacking" ? true : null;
		plotWithOptions();
	});

	$(".graphControls button").on('click',function (e) {
		e.preventDefault();
		bars = $(this).text().indexOf("Bars") != -1;
		lines = $(this).text().indexOf("Lines") != -1;
		steps = $(this).text().indexOf("steps") != -1;
		plotWithOptions();
	});
}

/**
  * Function written to load toggling series flotchart.
**/
function prtm_toggling_series(){
var datasets = {
		"usa": {
			label: "USA",
			color: '#f17316',
			data: [[1988, 483994], [1989, 479060], [1990, 457648], [1991, 401949], [1992, 424705], [1993, 402375], [1994, 377867], [1995, 357382], [1996, 337946], [1997, 336185], [1998, 328611], [1999, 329421], [2000, 342172], [2001, 344932], [2002, 387303], [2003, 440813], [2004, 480451], [2005, 504638], [2006, 528692]]
		},        
		"russia": {
			label: "Russia",
			color: '#00ca85',
			data: [[1988, 218000], [1989, 203000], [1990, 171000], [1992, 42500], [1993, 37600], [1994, 36600], [1995, 21700], [1996, 19200], [1997, 21300], [1998, 13600], [1999, 14000], [2000, 19100], [2001, 21300], [2002, 23600], [2003, 25100], [2004, 26100], [2005, 31100], [2006, 34700]]
		},
		"uk": {
			label: "UK",
			color: '#d24636',
			data: [[1988, 183994], [1989, 147900], [1990, 145748], [1991, 141949], [1992, 142405], [1993, 140275], [1994, 137787], [1995, 135382], [1996, 133946], [1997, 133685], [1998, 32611], [1999, 132941], [2000, 134172], [2001, 134432], [2002, 138303], [2003, 144083], [2004, 148041], [2005, 150438], [2006, 1152892]]
		},
		"germany": {
			label: "Germany",
			color: '#fd7b6c',
			data: [[1988, 155627], [1989, 155475], [1990, 158464], [1991, 155134], [1992, 152436], [1993, 147139], [1994, 143962], [1995, 143238], [1996, 142395], [1997, 140854], [1998, 140993], [1999, 141822], [2000, 141147], [2001, 140474], [2002, 140604], [2003, 140044], [2004, 138816], [2005, 138060], [2006, 136984]]
		},
		"sweden": {
			label: "Sweden",
			color: '#5e6db3',
			data: [[1988, 896402], [1989, 896474], [1990, 896605], [1991, 896209], [1992, 896035], [1993, 896020], [1994, 896000], [1995, 896018], [1996, 893958], [1997, 895780], [1998, 895954], [1999, 896178], [2000, 6411], [2001, 5993], [2002, 5833], [2003, 5791], [2004, 5450], [2005, 5521], [2006, 5271]]
		},
		"norway": {
			label: "Norway",
			color: '#31cff9',
			data: [[1988, 494382], [1989, 594498], [1990, 294535], [1991, 194398], [1992, 594766], [1993, 694441], [1994, 894670], [1995, 794217], [1996, 894275], [1997, 894203], [1998, 894482], [1999, 294506], [2000, 894358], [2001, 894385], [2002, 895269], [2003, 895066], [2004, 895194], [2005, 894887], [2006, 894891]]
		}
	};

	// hard-code color indices to prevent them from shifting as
	// countries are turned on/off
	var i = 0;
	$.each(datasets, function(key, val) {
		val.color = i;
		++i;
	});
	// insert checkboxes 
	var choiceContainer = $(".choices");
	$.each(datasets, function(key, val) {
		choiceContainer.append("<br/><input type='checkbox' name='" + key +
			"' checked='checked' id='id" + key + "'></input>" +
			"<label for='id" + key + "'>"
			+ val.label + "</label>");
	});
	choiceContainer.find("input").on('click',plotAccordingToChoices);
	function plotAccordingToChoices() {
		var data = [];
		choiceContainer.find("input:checked").each(function () {
			var key = $(this).attr("name");
			if (key && datasets[key]) {
				data.push(datasets[key]);
			}
		});
		if ($(".toggling-series").length > 0){
			if (data.length > 0) {
				$.plot(".toggling-series", data, {
					yaxis: {
						min: 0
					},
					xaxis: {
						tickDecimals: 0
					},
                    grid: {
						borderWidth: 0
					},
					colors: ["#f17316","#00ca85","#d24636","#fd7b6c","#5e6db3","#31cff9"],
				});
			}
		}
	}
	plotAccordingToChoices();
}

/**
  * Function written to load ajax flotchart.
**/
function prtm_real_time_update(){
	if($(".real-timeupdate").length > 0){
		var data = [],
		totalPoints = 300;
		var getRandomData = function() {
			if (data.length > 0)
				data = data.slice(1);
			// Do a random walk
			while (data.length < totalPoints) {
				var prev = data.length > 0 ? data[data.length - 1] : 50,
				y = prev + Math.random() * 10 - 5;
				if (y < 0) {
					y = 0;
				} else if (y > 100) {
					y = 100;
				}
				data.push(y);
			}
	
			// Zip the generated y values with the x values
			var res = [];
			for (var i = 0; i < data.length; ++i) {
				res.push([i, data[i]])
			}
			return res;
		}
	
		// Set up the control widget
		var updateInterval = 30;
			var v = 30;
			if (v && !isNaN(+v)) {
				updateInterval = +v;
				if (updateInterval < 1) {
					updateInterval = 1;
				} else if (updateInterval > 2000) {
					updateInterval = 2000;
				}
				$(this).val("" + updateInterval);
			}
                var data=[{color: '#F8B98B',data:getRandomData()}];
                var plot = $.plot(".real-timeupdate", data, {
                    series: {
                        shadowSize: 0	// Drawing is faster without shadows
                    },
                    yaxis: {
                        min: 0,
                        max: 100
                    },
                    xaxis: {
                        show: false
                    },
                    grid: {
                        borderWidth: 0,
                    },
                    colors: ["#fb9678"],
                });
            var update = function() {
              plot.setData([getRandomData()]);
             // Since the axes don't change, we don't need to call plot.setupGrid()
            plot.draw();
            setTimeout(update, updateInterval);
            }
         update();
	}
}
function getRandomData() {
	if (data.length > 0)
		data = data.slice(1);

	// Do a random walk
	while (data.length < totalPoints) {
		var prev = data.length > 0 ? data[data.length - 1] : 50,
			y = prev + Math.random() * 10 - 5;

		if (y < 0) {
			y = 0;
		} else if (y > 100) {
			y = 100;
		}

		data.push(y);
	}

	// Zip the generated y values with the x values

	var res = [];
	for (var i = 0; i < data.length; ++i) {
		res.push([i, data[i]])
	}
	return res;
}

/**
  * Function written to load adding annotations flotchart.
**/
function prtm_adding_annotations(){
	if ($(".adding-annotations").length > 0){
		var d1 = [];
		for (var i = 0; i < 20; ++i) {
			d1.push([i, Math.sin(i)]);
		}

		var data = [{ data: d1, label: "Pressure", color: "#31CFF9" }];

		var markings = [
			{ color: "#ffffff", yaxis: { from: 1 } },
			{ color: "#ffffff", yaxis: { to: -1 } },
			{ color: "#ffffff", lineWidth: 1, xaxis: { from: 1, to: 1 } },
			{ color: "#ffffff", lineWidth: 1, xaxis: { from: 9, to: 9 } }
		];

		var placeholder = $(".adding-annotations");

		var plot = $.plot(placeholder, data, {
			bars: { show: true, barWidth: 0.7, fill: 1 },
			xaxis: { ticks: [], autoscaleMargin: 0.02 },
			yaxis: { min: -2, max: 2 },
			grid: { markings: markings, borderWidth: 0 }
            
		});

		var o = plot.pointOffset({ x: 2, y: -1.2});

		// Append it to the placeholder that Flot already uses for positioning

		placeholder.append("<div style='position:absolute;left:" + (o.left + 4) + "px;top:" + o.top + "px;color:#666;font-size:smaller'>Warming up</div>");

		o = plot.pointOffset({ x: 8, y: -1.2});
		placeholder.append("<div style='position:absolute;left:" + (o.left + 4) + "px;top:" + o.top + "px;color:#666;font-size:smaller'>Actual measurements</div>");

		// Draw a little arrow on top of the last label to demonstrate canvas
		// drawing
		var ctx = plot.getCanvas().getContext("2d");
		ctx.beginPath();
		o.left += 4;
		ctx.moveTo(o.left, o.top);
		ctx.lineTo(o.left, o.top - 10);
		ctx.lineTo(o.left + 10, o.top - 5);
		ctx.lineTo(o.left, o.top);
		ctx.fillStyle = "#5e6db3";
		ctx.fill();
	}
}


/**
  * Function written to load categories flotchart.
**/
function prtm_categories(){
	if ($(".categories").length > 0){
		var data = {color: '#5e6db3',data:[["January", 25], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] ]};
		$.plot(".categories", [ data ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.8,
					align: "center",
                    fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			},
            grid: {
	        	borderWidth: 0,
	       	},
		});
	}
}

/**
  * Function written to load basic flotchart.
**/
function prtm_basic_chart(){
	if ($(".basic-chart").length > 0){
		var d1 = [];
		for (var i = 0; i < 14; i += 0.5) {
			d1.push([i, Math.sin(i)]);
		}
		var d2 =  {color:'#fd7b6c', data: [[0, 3], [4, 8], [8, 5], [9, 13]]};
		var d3 = {color:'#00ca85' , data: [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]]};
		$.plot(".basic-chart", [ d1, d2, d3 ], {
			grid: {
	        	borderWidth: 0,
	       	},
	       	bars: {
			    show: true,
			    lineWidth: 0,
			    fill: true,
			    fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] }
			}	
		});
	}
}
function prtm_multiple_real_time_update(){
	if($(".multiple-real-timeupdate").length > 0){
		var data = [],
		totalPoints = 300;
		var getRandomData = function() {
			if (data.length > 0)
				data = data.slice(1);
			// Do a random walk
			while (data.length < totalPoints) {
				var prev = data.length > 0 ? data[data.length - 1] : 50,
				y = prev + Math.random() * 10 - 5;
				if (y < 0) {
					y = 0;
				} else if (y > 100) {
					y = 100;
				}
				data.push(y);
			}
	
			// Zip the generated y values with the x values
			var res = [];
			for (var i = 0; i < data.length; ++i) {
				res.push([i, data[i]])
			}
			return res;
		}

		var data2 = [],
		totalPoints = 300;
		var getRandomDataTwo = function() {
			if (data2.length > 0)
				data2 = data2.slice(1);
			// Do a random walk
			while (data2.length < totalPoints) {
				var prev = data2.length > 0 ? data2[data2.length - 1] : 50,
				y = prev + Math.random() * 10 - 5;
				if (y < 0) {
					y = 0;
				} else if (y > 100) {
					y = 100;
				}
				data2.push(y);
			}
	
			// Zip the generated y values with the x values
			var res = [];
			for (var i = 0; i < data2.length; ++i) {
				res.push([i, data2[i]])
			}
			return res;
		}
	
		// Set up the control widget
		var updateInterval = 30;
			var v = 30;
			if (v && !isNaN(+v)) {
				updateInterval = +v;
				if (updateInterval < 1) {
					updateInterval = 1;
				} else if (updateInterval > 2000) {
					updateInterval = 2000;
				}
				$(this).val("" + updateInterval);
			}
                var d1={color: '#F8B98B',data:getRandomData()};
                var d2={color: '#31CFF9',data:getRandomDataTwo()};
                var plot = $.plot(".multiple-real-timeupdate", [d1,d2], {
                    series: {
                        shadowSize: 0	// Drawing is faster without shadows
                    },
                    yaxis: {
                        min: 0,
                        max: 100
                    },
                    xaxis: {
                        show: false
                    },
                    grid: {
                        borderWidth: 0,
                    },
                    colors: ["#fb9678","#31CFF9"],
                });
            var update = function() {
              plot.setData([getRandomData(),getRandomDataTwo()]);
             // Since the axes don't change, we don't need to call plot.setupGrid()
            plot.draw();
            setTimeout(update, updateInterval);
            }
         update();
	}
}


/**
  * Function written to intialized all the functions.
**/
(function($) {
	prtm_basic_chart();
	prtm_categories();
	prtm_adding_annotations();
	prtm_real_time_update();
	prtm_toggling_series();
	prtm_stacking();
	prtm_error_bars();
	prtm_pie_chart();
	prtm_multiple_real_time_update();
})(jQuery);
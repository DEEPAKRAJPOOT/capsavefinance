'use strict';
(function($) {
	Highcharts.setOptions({
		tooltip: {useHTML: true,},
		legend: {useHTML: true,},
		chart:{
			backgroundColor: null,
		},
		xAxis:{
			labels: {useHTML: true,},
			tickInterval: 1,
			minPadding: 0,
			maxPadding: 0,
			startOnTick: true,
			endOnTick: true
		},
		yAxis:{
			labels: {useHTML: true,}
		},
		plotOptions: {
			area: {
				dataLabels: { useHTML: true,}
			},
			bar: {
				dataLabels: { useHTML: true,}
			},
			pie: {
				dataLabels: { useHTML: true,}
			},
			line: {
				dataLabels: { useHTML: true,}
			},
			series: {
				dataLabels: { useHTML: true,}
			},
			scatter: {
				dataLabels: { useHTML: true,}
			},
			column: {
				dataLabels: { useHTML: true,}
			},
			columnrange: {
				dataLabels: { useHTML: true,}
			},
			spline: {
				dataLabels: { useHTML: true,}
			},
		},
		credits: {
			enabled: false
		},
    });
	 
	 var categories = [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ];
    /**
      * Function to load basic area highchart 
    **/
    if ($(".d-area-chart").length > 0){
        $('.d-area-chart').highcharts({
        chart: {
            type: 'areaspline'
        },
        title: {
            text: ''
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 450,
            y: 0,
            floating: true,
            borderWidth: 1,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        xAxis: {
            labels: {
                enabled: true,
                formatter: function () {
                    return categories[this.value];
                }
            },
            tickInterval: 1,
            minPadding: 0,
            maxPadding: 0,
            startOnTick: true,
            endOnTick: true
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ''
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.8,
                    marker:{
                      enabled: false,
                    },
            }
        },
        series: [{
            name: 'Old users',
            color: '#5e6db3',
            data: [0, 450, 380, 200, 550, 220, 200,200,110,140,180,200]
        },{
            name: 'Returning',
            color: '#31cff9',
            data: [0, 550, 400, 150, 80, 270, 160, 100,200,250,150,190]
        },{
            name: 'New users',
            color: '#00ca85',
            data: [0, 450, 380, 150, 90, 290, 180,110,110,140,160,200]
        }]
    });
    }

    /**
      * Function to load area point highchart 
    **/
    if ($(".d-area-points-chart").length > 0){
        $('.d-area-points-chart').highcharts({
            chart: {
                type: 'area',
                spacingBottom: 30
            },
            title: {
                text: ''
            },
            subtitle: {
                text: '',
                floating: true,
                align: 'right',
                verticalAlign: 'bottom',
                y: 15
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'top',
                x: 150,
                y: 100,
                floating: true,
                borderWidth: 1,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
           xAxis: {
               labels: {
						 enabled: true,
						 formatter: function () {
							  return categories[this.value];
						 }
					},
					tickInterval: 1,
					minPadding: 0,
					maxPadding: 0,
					startOnTick: true,
					endOnTick: true
            },
            yAxis: {
					title: {
						 text: ''
					},
					labels: {
					  formatter: function () {
							return this.value;
					  }
					}
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.x + ': ' + this.y;
                }
            },
            plotOptions: {
                area: {
                    fillOpacity: 0.8,
                    marker:{
                      enabled: true,
                      fillColor: '#5e6db3',
                      radius: 5,
                    },
                },
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Revenue',
                color: '#31cff9',
                data: [1500,1200,2000,1200,1800,1000,2500]
            }]
        });
    }

    /**
      * Function to area point highchart 
    **/
    if ($(".area-point-dashboard").length > 0){
         $('.area-point-dashboard').highcharts({
            chart: {
                type: 'area',
                spacingBottom: 0,
                spacingLeft: -10,
                spacingRight: -10,
                height: 336,
            },
            title: {
                text: ''
            },
            subtitle: {
                text: '',
                floating: true,
                align: 'right',
                verticalAlign: 'bottom',
                y: 15
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'top',
                x: 150,
                y: 100,
                floating: true,
                borderWidth: 1,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            xAxis: {
                visible: false,
            },
            yAxis: {
                visible: false,
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.x + ': ' + this.y;
                }
            },
            plotOptions: {
                area: {
                    fillOpacity: 0.5,
                    marker:{
                      enabled: true,
                      lineColor: '#31cff9',
                      fillColor: '#31cff9',
                      radius: 8,
                    },
                },
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'New Visitors',
                color: '#31cff9',
                data: [1500,1200,2000,1200,1800,1000,2500]
            }]
        });
    }

    /**
      * Function to load area highchart 
    **/
    if ($(".dasboard2-area-chart").length > 0){
        Morris.Area({
              element: $('.dasboard2-area-chart'),
              behaveLikeLine: true,
              lineColors: ["#5e6db3","#fd7b6c","#00ca95"],
              data: [
                { y: '2010', a: 0, b: 0, c: 0 },
                { y: '2011', a: 50,  b: 5, c: 15 },
                { y: '2012', a: 35,  b: 60, c: 50 },
                { y: '2013', a: 55,  b: 10, c: 20 },
                { y: '2014', a: 30,  b: 110, c: 20 },
                { y: '2015', a: 25,  b: 35, c: 80 },
                { y: '2016', a: 25, b: 27, c: 30 }
              ],
              xkey: 'y',
              ykeys: ['a', 'b', 'c'],
              labels: ['Product A', 'Product B', 'Product C'],
              resize: true,
              pointSize: 0,
            }).on('click', function(i, row){
              console.log(i, row);
            });
    }

    /**
      * Function to load area morris chart 
    **/
    if ($(".d-morris-chart-network").length > 0){

        var day_data = [
            {y:"", c: 20, b:40, a:60},
            {y:"", c: 25, b:45, a:65},
            {y:"", c: 21, b:41, a:61},
            {y:"", c: 24, b:44, a:64},
            {y:"", c: 28, b:48, a:68},
            {y:"", c: 24, b:44, a:64},
            {y:"", c: 21, b:41, a:61},
            {y:"", c: 27, b:47, a:67},
            {y:"", c: 29, b:49, a:69},
            {y:"", c: 25, b: 45, a:65},
            {y:"", c: 30, b: 50, a:70},
            {y:"", c: 28, b: 48, a:68},
            {y:"", c: 35, b: 55, a:75},
            {y:"", c: 31, b:51, a:71},
            {y:"", c: 34, b:54, a:74},
            {y:"", c: 27, b:47, a:67},
            {y:"", c: 25, b:45, a:65},
            {y:"", c: 30, b:50, a:70},
            {y:"", c: 28, b:48, a:68},
            {y:"", c: 35, b:55, a:75},
            {y:"", c: 38, b:58, a:78},
            {y:"", c: 32, b:52, a:72},
            {y:"", c: 24, b: 44, a:64},
            {y:"", c: 29, b: 49, a:69},
            {y:"", c: 25, b: 45, a:65},
            {y:"", c: 30, b: 50, a:70},
            {y:"", c: 33, b:53, a:73},
            {y:"", c: 28, b:48, a:68},
            {y:"", c: 21, b:41, a:61},
            {y:"", c: 29, b:49, a:69},
            {y:"", c: 27, b:47, a:67},
            {y:"", c: 32, b:52, a:72},
            {y:"", c: 38, b:58, a:78},
            {y:"", c: 33, b:53, a:78},
            {y:"", c: 35, b:55, a:75},
            {y:"", c: 28, b: 58, a:74},
            {y:"", c: 24, b: 44, a:64},
            {y:"", c: 29, b: 49, a:69},
            {y:"", c: 35, b: 55, a:75},
            {y:"", c: 40, b: 60, a:80},
            {y:"", c: 34, b:54, a:74},
            {y:"", c: 37, b:57, a:77},
            {y:"", c: 28, b:48, a:68},
            {y:"", c: 24, b:44, a:64},
            {y:"", c: 27, b:47, a:67},
            {y:"", c: 32, b:52, a:72},
            {y:"", c: 25, b:45, a:65},
            {y:"", c: 29, b:49, a:69},
            {y:"", c: 33, b: 53, a:73},
            {y:"", c: 28, b: 48, a:68},
            {y:"", c: 35, b: 55, a:75},
            {y:"", c: 38, b: 58, a:78},
            {y:"", c: 33, b: 53, a:73}
        ];

        var chart = Morris.Area({
            element : $('.d-morris-chart-network'),
            data: day_data,
            xkey: 'y',
            ykeys: ['a', 'b','c'],
            labels: ['CPU', 'RAM', 'Visitors'],
            lineColors: ['#5e6db3','#00ca95','#414658'],
            lineWidth:[0,0,0],
            pointSize:[0,0,0],
            fillOpacity: 1,
            gridTextColor:'#999',
            parseTime: false,
            resize:true,
            behaveLikeLine : true,
            hideHover: 'auto'
        });
    }

    /**
      * Function to load line chartist chart 
    **/
    if ($(".dashboard2-line").length > 0){

        new Chartist.Line('.dashboard2-line', {
            labels: [1, 2, 3, 4, 5, 6, 7, 8],
            series: [
                [1, 2, 3, 1, 2, 0, 1, 0]
            ]
        }, {
            low: 0,
            showArea: true,
            showPoint: false,
            axisX: {
                showLabel: false,
                showGrid: false
            },
            axisY: {
                showLabel: false,
                showGrid: false
            },
            fullWidth: true,
            chartPadding: {
                right: 0,
                left: -50,
            },
            lineSmooth: Chartist.Interpolation.cardinal({
                fillHoles: true,
            }),
        });
    }

    /**
      * Function to load basic amchart 
    **/
    if ($("#dashboard3-amchart")[0]){
        var chart = AmCharts.makeChart( "dashboard3-amchart", {
          "type": "serial",
          "addClassNames": true,
          "theme": "light",
          "autoMargins": false,
          "marginLeft": 30,
          "marginRight": 8,
          "marginTop": 10,
          "marginBottom": 26,
          "balloon": {
            "adjustBorderColor": false,
            "horizontalPadding": 10,
            "verticalPadding": 8,
            "color": "#ffffff"
          },
          "dataProvider": [ {
            "year": 'Monday',
            "income": 23.5,
            "expenses": 21.1,
            "dashLengthLine": 5
          }, {
            "year": 'Tuesday',
            "income": 26.2,
            "expenses": 30.5,
            "dashLengthLine": 5
          }, {
            "year": 'Wednesday',
            "income": 30.1,
            "expenses": 34.9,
            "dashLengthLine": 5
          }, {
            "year": 'Thursday',
            "income": 29.5,
            "expenses": 31.1,
            "dashLengthLine": 5
          }, {
            "year": 'Friday',
            "income": 30.6,
            "expenses": 26.2,
            "dashLengthLine": 5
          },{
            "year": 'Saturday',
            "income": 32.6,
            "expenses": 28.2,
            "dashLengthLine": 5
          }, {
            "year": 'Sunday',
            "income": 34.1,
            "expenses": 32.9,
            "dashLengthColumn": 5,
            "alpha": 0.2,
            "additional": "(projection)"
          } ],
          "valueAxes": [ {
            "axisAlpha": 0,
            "position": "left"
          } ],
          "startDuration": 1,
          "graphs": [ {
            "alphaField": "alpha",
            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            "fillAlphas": 1,
            "title": "Income",
            "type": "column",
            "fillColors": "#00ca85",
            "valueField": "income",
            "dashLengthField": "dashLengthColumn"
          }, {
            "id": "graph2",
            "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
            "bullet": "round",
            "lineThickness": 3,
            "bulletSize": 7,
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "useLineColorForBulletBorder": true,
            "bulletBorderThickness": 3,
            "fillAlphas": 0,
            "lineAlpha": 1,
            "title": "Expenses",
            "valueField": "expenses",
            "dashLengthField": "dashLengthLine"
          } ],
          "categoryField": "year",
          "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "tickLength": 0
          },
          "export": {
            "enabled": true
          }
        } );
    }
    
    /**
      * Function to load area morris chart 
    **/
    if ($(".dasboard3-area-chart")[0]){
        Morris.Area({
          element: $('.dasboard3-area-chart'),
          behaveLikeLine: true,
          axes:false,
          lineColors: ["#0c9dc3"],
          fillOpacity: '0.5',
          data: [
            { y: '2010', a: 10,},
            { y: '2011', a: 30,},
            { y: '2012', a: 15,},
            { y: '2013', a: 35,},
            { y: '2014', a: 15,},
            { y: '2015', a: 30,},
            { y: '2016', a: 10,}
          ],
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Product A'],
          resize: true,
        }).on('click', function(i, row){
          console.log(i, row);
        });
    }

    /**
      * Function to load Topbar area morris chart 
    **/
    if ($(".dasboard3-top1")[0]){
        Morris.Area({
          element: $('.dasboard3-top1'),
          behaveLikeLine: true,
          axes:false,
          lineColors: ["#0c9dc3"],
          fillOpacity: '0.5',
          data: [
            { y: '2010', a: 30,},
            { y: '2011', a: 50,},
            { y: '2012', a: 35,},
            { y: '2013', a: 55,},
            { y: '2014', a: 30,},
            { y: '2015', a: 25,},
            { y: '2016', a: 25,}
          ],
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Product A'],
          resize: true,
        }).on('click', function(i, row){
          console.log(i, row);
        });
    }

    /**
      * Function to load dont morris chart 
    **/
    if ($(".dashboard3-browser")[0]){
        Morris.Donut({
          defaultLabelColor: '#00ca95',
          element: $('.dashboard3-browser'),
          data: [
            {label: "Firefox", value: 50, labelColor: '#00ca95'},
            {label: "Chrome", value: 25, labelColor: '#5e6db3'},
            {label: "Opera", value: 25, labelColor: '#fd7b6c'}
          ],
        colors: [
            '#00ca95',
            '#5e6db3',
            '#fd7b6c',
          ],
        resize: true,
        });  
    }  

/**
  * Function to load gauge amchart 
 **/
if ($("#dashboard3-email")){
    var gaugeChart = AmCharts.makeChart("dashboard3-email", {
      "type": "gauge",
      "theme": "dark",
      "axes": [{
        "axisAlpha": 0,
        "tickAlpha": 0,
        "labelsEnabled": false,
        "startValue": 0,
        "endValue": 100,
        "startAngle": 0,
        "endAngle": 270,
        "bands": [{
          "color": "#343848",
          "startValue": 0,
          "endValue": 100,
          "radius": "100%",
          "innerRadius": "85%"
        }, {
          "color": "#5e6db3",
          "startValue": 0,
          "endValue": 75,
          "radius": "100%",
          "innerRadius": "85%",
          "balloonText": "75%"
        }, {
          "color": "#343848",
          "startValue": 0,
          "endValue": 100,
          "radius": "80%",
          "innerRadius": "65%"
        }, {
          "color": "#00ca95",
          "startValue": 0,
          "endValue": 85,
          "radius": "80%",
          "innerRadius": "65%",
          "balloonText": "85%"
        }, {
          "color": "#343848",
          "startValue": 0,
          "endValue": 100,
          "radius": "60%",
          "innerRadius": "45%"
        }, {
          "color": "#fd7b6c",
          "startValue": 0,
          "endValue": 30,
          "radius": "60%",
          "innerRadius": "45%",
          "balloonText": "30%"
        }]
      }],
      "allLabels": [{
        "text": "Sent",
        "x": "45%",
        "y": "9%",
        "size": 12,
        "bold": false,
        "color": "#ffffff",
        "align": "right"
      }, {
        "text": "Opened",
        "x": "45%",
        "y": "18%",
        "size": 12,
        "bold": false,
        "color": "#ffffff",
        "align": "right"
      }, {
        "text": "Spam",
        "x": "45%",
        "y": "26%",
        "size": 12,
        "bold": false,
        "color": "#ffffff",
        "align": "right"
      }, ],
      "export": {
        "enabled": true
      }
    });
}

	/**
	  * Function to load full-calendar 
	 **/
	if ($("#full-clndr").length > 0){
	  var currentMonth = moment().format('YYYY-MM');
	  var nextMonth    = moment().add(1,'month').format('YYYY-MM');
	  var thirdMonth    = moment().add(2,'month').format('YYYY-MM');
	  var fourthMonth    = moment().add(3,'month').format('YYYY-MM');
	
	  var events = [
		 { date: currentMonth + '-' + '10', title: 'Persian Kitten Auction', location: 'Center for Beautiful Cats' },
		 { date: currentMonth + '-' + '19', title: 'Cat Frisbee', location: 'Jefferson Park' },
		 { date: currentMonth + '-' + '23', title: 'Kitten Demonstration', location: 'Center for Beautiful Cats' },
		 { date: nextMonth + '-' + '07',    title: 'Small Cat Photo Session', location: 'Center for Cat Photography' },
		 { date: nextMonth + '-' + '10', title: 'Persian Kitten Auction', location: 'Center for Beautiful Cats' },
		 { date: nextMonth + '-' + '19', title: 'Cat Frisbee', location: 'Jefferson Park' },
		 { date: thirdMonth + '-' + '10', title: 'Persian Kitten Auction', location: 'Center for Beautiful Cats' },
		 { date: thirdMonth + '-' + '19', title: 'Cat Frisbee', location: 'Jefferson Park' },
		 { date: thirdMonth + '-' + '23', title: 'Kitten Demonstration', location: 'Center for Beautiful Cats' },
		 { date: fourthMonth + '-' + '10', title: 'Persian Kitten Auction', location: 'Center for Beautiful Cats' },
		 { date: fourthMonth + '-' + '19', title: 'Cat Frisbee', location: 'Jefferson Park' },
		 { date: fourthMonth + '-' + '23', title: 'Kitten Demonstration', location: 'Center for Beautiful Cats' },
	  ];
	
	  var clndr = $('#full-clndr').clndr({
		 template: $('#full-clndr-template').html(),
		 events: events,
		 forceSixRows: true
	  });
	}
    /*-------- SlimScroll bar -------------*/
    if ($(".slimscrollbar").length > 0){
        $('.slimscrollbar').slimscroll({
          color: '#bbbbbb',
          size: '5px',
          alwaysVisible: true,
          railOpacity: 0.2,
        });
    }
})(jQuery);
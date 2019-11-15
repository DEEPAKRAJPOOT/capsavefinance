google.charts.load('current', {packages: ['corechart', 'bar', 'line', 'orgchart', 'treemap', 'table', 'timeline', 'gauge','geochart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

	/**
	  * Function written to load column google chart.
	**/
	if ($("#column-chart-1").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Year', 'Sales', 'Expenses', 'Profit'],
			['2014', 1000, 400, 200],
			['2015', 1170, 460, 250],
			['2016', 660, 1120, 300],
			['2017', 1030, 540, 350]
		]);

		var options = {
			chart: {
				title: 'Company Performance',
				subtitle: 'Sales, Expenses, and Profit: 2014-2017',
			},
			bars: 'vertical',
			vAxis: {format: 'decimal'},
			height: 400,
			colors: ['#00ca95', '#f17316', '#5e6db3']
		};
		var chart = new google.charts.Bar(document.getElementById('column-chart-1'));

		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
     
    /**
	  * Function written to load column google chart.
	**/
    if ($("#column-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
	        ["Element", "Density", { role: "style" } ],
	        ["Copper", 8.94, "#b87333"],
	        ["Silver", 10.49, "silver"],
	        ["Gold", 19.30, "gold"],
	        ["Platinum", 21.45, "color: #e5e4e2"]
	      ]);

	      var view = new google.visualization.DataView(data);
	      view.setColumns([0, 1,
	                       { calc: "stringify",
	                         sourceColumn: 1,
	                         type: "string",
	                         role: "annotation" },
	                       2]);

	      var options = {
	        title: "Density of Precious Metals, in g/cm^3",
	        width: 400,
	        height: 400,
	        bar: {groupWidth: "95%"},
	        legend: { position: "none" },
	      };
	      var chart = new google.visualization.ColumnChart(document.getElementById("column-chart-2"));
	      chart.draw(view, options);
	}
	
	/**
	  * Function written to load bar google chart.
	**/
	if ($("#bar-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Year', 'Sales', 'Expenses', 'Profit'],
			['2014', 1000, 400, 200],
			['2015', 1170, 460, 250],
			['2016', 660, 1120, 300],
			['2017', 1030, 540, 350]
		]);

		var options = {
			chart: {
				title: 'Company Performance',
				subtitle: 'Sales, Expenses, and Profit: 2014-2017',
			},
			bars: 'horizontal', // Required for Material Bar Charts.
			hAxis: {format: 'decimal'},
			height: 400,
			colors: ['#00ca95', '#f17316', '#5e6db3']
		};
		var chart = new google.charts.Bar(document.getElementById('bar-chart-1'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
		
	/**
	  * Function written to load bar google chart.
	**/
	if ($("#bar-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			["Element", "Density", { role: "style" } ],
			["Copper", 8.94, "#b87333"],
			["Silver", 10.49, "silver"],
			["Gold", 19.30, "gold"],
			["Platinum", 21.45, "color: #e5e4e2"]
		]);

		var view = new google.visualization.DataView(data);
		view.setColumns([0, 1,{
			calc: "stringify",
			sourceColumn: 1,
			type: "string",
			role: "annotation" },2]);

		var options = {
			title: "Density of Precious Metals, in g/cm^3",
			width: 400,
			height: 400,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		var chart = new google.visualization.BarChart(document.getElementById("bar-chart-2"));
		chart.draw(view, options);
	}
	
	/**
	  * Function written to load line google chart.
	**/
	if ($("#line-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Year', 'Sales', 'Expenses'],
			['2004',  1000,      400],
			['2005',  1170,      460],
			['2006',  660,       1120],
			['2007',  1030,      540]
		]);
		var options = {
			title: 'Company Performance',
			subtitle: 'Sales and Expenses: 2004-2007',
			curveType: 'function',
			height: 400,
			colors: ['#00ca95', '#f17316'],
			legend: { position: 'bottom' }
		};
		var chart = new google.visualization.LineChart(document.getElementById('line-chart-1'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load line google chart.
	**/
	if ($("#line-chart-2").length > 0){ 
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'Day');
		data.addColumn('number', 'Guardians of the Galaxy');
		data.addColumn('number', 'The Avengers');
		data.addColumn('number', 'Transformers: Age of Extinction');

		data.addRows([
			[1,  37.8, 80.8, 41.8],
			[2,  30.9, 69.5, 32.4],
			[3,  25.4,   57, 25.7],
			[4,  11.7, 18.8, 10.5],
			[5,  11.9, 17.6, 10.4],
			[6,   8.8, 13.6,  7.7],
			[7,   7.6, 12.3,  9.6],
			[8,  12.3, 29.2, 10.6],
			[9,  16.9, 42.9, 14.8],
			[10, 12.8, 30.9, 11.6],
			[11,  5.3,  7.9,  4.7],
			[12,  6.6,  8.4,  5.2],
			[13,  4.8,  6.3,  3.6],
			[14,  4.2,  6.2,  3.4]
		]);
		var options = {
			chart: {
				title: 'Box Office Earnings in First Two Weeks of Opening',
				subtitle: 'in millions of dollars (USD)'
			},
			height: 400,
			colors: ['#00ca95', '#f17316', '#5e6db3'],
			axes: {
				x: {
					0: {side: 'top'}
				}
			}
		};
		var chart = new google.charts.Line(document.getElementById('line-chart-2'));
		chart.draw(data, options);
	}

	/**
	  * Function written to load combo google chart.
	**/	
	if ($("#combo-chart").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Average'],
			['2004/05',  165,      938,         522,             998,           450,      614.6],
			['2005/06',  135,      1120,        599,             1268,          288,      682],
			['2006/07',  157,      1167,        587,             807,           397,      623],
			['2007/08',  139,      1110,        615,             968,           215,      609.4],
			['2008/09',  136,      691,         629,             1026,          366,      569.6]
		]);
		var options = {
			title : 'Monthly Coffee Production by Country',
			vAxis: {title: 'Cups'},
			hAxis: {title: 'Month'},
			seriesType: 'bars',
			height:500,
			colors: ['#00ca95', '#f17316', '#5e6db3', '#fd7b6c', '#d24636'],
			series: {5: {type: 'line'}}
		};
		var chart = new google.visualization.ComboChart(document.getElementById('combo-chart'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load area google chart.
	**/
	if ($("#area-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Year', 'Sales', 'Expenses'],
			['2013',  1000,      400],
			['2014',  1170,      460],
			['2015',  660,       1120],
			['2016',  1030,      540]
		]);

		var options = {
			title: 'Company Performance',
			hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
			vAxis: {minValue: 0},
			height:400,
			colors: ['#00ca95', '#f17316'],	
		};

		var chart = new google.visualization.AreaChart(document.getElementById('area-chart-1'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load area google chart.
	**/
	if ($("#area-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Year', 'Car', 'Trucks', 'Drones', 'Segways'],
			['2013',  870,   460,     540,    600],
			['2014',  460,   720,     720,    900],
			['2015',  930,   540,     1200,   840],
			['2016',  1030,  800,     1250,   1200]
		]);

		var options = {
			isStacked: false,
			height: 400,
			colors: ['#00ca95', '#f17316', '#5e6db3', '#fd7b6c'],
			legend: {position: 'top', maxLines: 3},
			vAxis: {minValue: 0}
		};

		var chart = new google.visualization.AreaChart(document.getElementById('area-chart-2'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load stepped area google chart.
	**/
	if ($("#stepped-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Director (Year)',  'Rotten Tomatoes', 'IMDB'],
			['Alfred Hitchcock (1935)', 8.4,         7.9],
			['Ralph Thomas (1959)',     6.9,         6.5],
			['Don Sharp (1978)',        6.5,         6.4],
			['James Hawes (2008)',      4.4,         6.2]
		]);
		var options = {
			title: 'The decline of \'The 39 Steps\'',
			vAxis: {title: 'Accumulated Rating'},
			isStacked: true,
			height:400,
			colors: ['#00ca95', '#f17316'],
		};
		var chart = new google.visualization.SteppedAreaChart(document.getElementById('stepped-chart-1'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load stepped area google chart.
	**/
	if ($("#stepped-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Year', 'Colonial' ,'Victorian', 'Modern', 'Contemporary'],
			['2013',  5.2,         3.6,        2.8,         2.0],
			['2014',  5.6,         4.0,        2.8,         3.0],
			['2015',  7.2,         2.2,        2.2,         6.0],
			['2016',  8.0,         1.7,        0.8,         4.0]
		]);
		var options = {
			isStacked: true,
			height: 400,
			colors: ['#00ca95', '#f17316', '#5e6db3', '#fd7b6c'],
			legend: {position: 'bottom', maxLines: 3},
			vAxis: {minValue: 0}
		};
		var chart = new google.visualization.SteppedAreaChart(document.getElementById('stepped-chart-2'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load pie google chart.
	**/
	if ($("#pie-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Task', 'Hours per Day'],
			['Work',     11],
			['Eat',      4],
			['Commute',  9],
			['Watch TV', 5],
			['Sleep',    7]
		]);
		var options = {
			title: 'My Daily Activities',
			height:400,
			colors: ['#00ca95', '#f17316', '#5e6db3', '#fd7b6c', '#d24636'],
		};
		var chart = new google.visualization.PieChart(document.getElementById('pie-chart-1'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load pie google chart.
	**/
	if ($("#pie-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Task', 'Hours per Day'],
			['Work',     11],
			['Eat',      2],
			['Commute',  2],
			['Watch TV', 2],
			['Sleep',    7]
		]);
		var options = {
			title: 'My Daily Activities',
			is3D: true,
			height:400,
			colors:['#00ca95', '#f17316', '#5e6db3', '#fd7b6c', '#d24636'],
		};
		var chart = new google.visualization.PieChart(document.getElementById('pie-chart-2'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load pie google chart.
	**/
	if ($("#pie-chart-3").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Language', 'Speakers (in millions)'],
			['Assamese', 13], ['Bengali', 83], ['Bodo', 1.4],
			['Dogri', 2.3], ['Gujarati', 46], ['Hindi', 300],
			['Kannada', 38], ['Kashmiri', 5.5], ['Konkani', 5],
			['Maithili', 20], ['Malayalam', 33], ['Manipuri', 1.5],
			['Marathi', 72], ['Nepali', 2.9], ['Oriya', 33],
			['Punjabi', 29], ['Sanskrit', 0.01], ['Santhali', 6.5],
			['Sindhi', 2.5], ['Tamil', 61], ['Telugu', 74], ['Urdu', 52]
		]);
		var options = {
			title: 'Indian Language Use',
			legend: 'none',
			pieSliceText: 'label',
			height:400,
			colors:['#00ca95', '#f17316', '#5e6db3', '#fd7b6c', '#d24636','#7e8ac2','#33d5aa','#5ad9fa','#fd9589'],
			slices: {  4: {offset: 0.2},
				12: {offset: 0.3},
				14: {offset: 0.4},
				15: {offset: 0.5},
			},
		};
		var chart = new google.visualization.PieChart(document.getElementById('pie-chart-3'));
		chart.draw(data, options);
	}

	/**
	  * Function written to load pie google chart.
	**/
	if ($("#pie-chart-4").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['Task', 'Hours per Day'],
			['Work',     11],
			['Eat',      2],
			['Commute',  2],
			['Watch TV', 2],
			['Sleep',    7]
		]);
		var options = {
			title: 'My Daily Activities',
			pieHole: 0.4,
			height:400,
			colors:['#00ca95', '#f17316', '#5e6db3', '#fd7b6c', '#d24636'],
		};
		var chart = new google.visualization.PieChart(document.getElementById('pie-chart-4'));
		chart.draw(data, options);
	}

	/**
	  * Function written to load bubble google chart.
	**/
	if ($("#bubble-chart-1").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['ID', 'X', 'Y', 'Temperature'],
			['',   80,  167,      120],
			['',   79,  136,      130],
			['',   78,  184,      50],
			['',   72,  278,      230],
			['',   81,  200,      210],
			['',   72,  170,      100],
			['',   68,  477,      80]
		]);
		var options = {
			colorAxis: {colors: ['#00ca95', '#f17316']},
			height:400,

		};
		var chart = new google.visualization.BubbleChart(document.getElementById('bubble-chart-1'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load bubble goole chart.
	**/
	if ($("#bubble-chart-2").length > 0){ 
		var data = google.visualization.arrayToDataTable([
			['ID', 'Life Expectancy', 'Fertility Rate', 'Region',     'Population'],
			['CAN',    80.66,              1.67,      'North America',  33739900],
			['DEU',    79.84,              1.36,      'Europe',         81902307],
			['DNK',    78.6,               1.84,      'Europe',         5523095],
			['EGY',    72.73,              2.78,      'Middle East',    79716203],
			['GBR',    80.05,              2,         'Europe',         61801570],
			['IRN',    72.49,              1.7,       'Middle East',    73137148],
			['IRQ',    68.09,              4.77,      'Middle East',    31090763],
			['ISR',    81.55,              2.96,      'Middle East',    7485600],
			['RUS',    68.6,               1.54,      'Europe',         141850000],
			['USA',    78.09,              2.05,      'North America',  307007000]
		]);

		var options = {
			title: 'Correlation between life expectancy, fertility rate ' +
			'and population of some world countries (2010)',
			hAxis: {title: 'Life Expectancy'},
			vAxis: {title: 'Fertility Rate'},
			height:400,
			colors:['#00ca95', '#f17316', '#5e6db3'],
			bubble: {textStyle: {fontSize: 11}}
		};
		var chart = new google.visualization.BubbleChart(document.getElementById('bubble-chart-2'));
		chart.draw(data, options);
	}
	
	/**
	  * Function written to load tree google chart.
	**/
	if ($("#tree-chart").length > 0){ 
		 var data = google.visualization.arrayToDataTable([
			  ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
			  ['Global',    null,                 0,                               0],
			  ['America',   'Global',             0,                               0],
			  ['Europe',    'Global',             0,                               0],
			  ['Asia',      'Global',             0,                               0],
			  ['Australia', 'Global',             0,                               0],
			  ['Africa',    'Global',             0,                               0],
			  ['Brazil',    'America',            11,                              10],
			  ['USA',       'America',            52,                              31],
			  ['Mexico',    'America',            24,                              12],
			  ['Canada',    'America',            16,                              -23],
			  ['France',    'Europe',             42,                              -11],
			  ['Germany',   'Europe',             31,                              -2],
			  ['Sweden',    'Europe',             22,                              -13],
			  ['Italy',     'Europe',             17,                              4],
			  ['UK',        'Europe',             21,                              -5],
			  ['China',     'Asia',               36,                              4],
			  ['Japan',     'Asia',               20,                              -12],
			  ['India',     'Asia',               40,                              63],
			  ['Laos',      'Asia',               4,                               34],
			  ['Mongolia',  'Asia',               1,                               -5],
			  ['Israel',    'Asia',               12,                              24],
			  ['Iran',      'Asia',               18,                              13],
			  ['Pakistan',  'Asia',               11,                              -52],
			  ['Egypt',     'Africa',             21,                              0],
			  ['S. Africa', 'Africa',             30,                              43],
			  ['Sudan',     'Africa',             12,                              2],
			  ['Congo',     'Africa',             10,                              12],
			  ['Zaire',     'Africa',             8,                               10]
		]);
		
		tree = new google.visualization.TreeMap(document.getElementById('tree-chart'));

		tree.draw(data, {
			minColor: '#f00',
			midColor: '#ddd',
			maxColor: '#0d0',
			headerHeight: 15,
			fontColor: 'black',
			height:400,
			showScale: true,
		});
	}

	/**
	  * Function written to load table google chart.
	**/
	if ($("#table-chart").length > 0){ 
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Name');
		data.addColumn('number', 'Salary');
		data.addColumn('boolean', 'Full Time Employee');
		data.addRows([
			['Mike',  {v: 10000, f: '$10,000'}, true],
			['Jim',   {v:8000,   f: '$8,000'},  false],
			['Alice', {v: 12500, f: '$12,500'}, true],
			['Bob',   {v: 7000,  f: '$7,000'},  true]
		]);
		var table = new google.visualization.Table(document.getElementById('table-chart'));
		table.draw(data, {showRowNumber: true, width: '100%', height: '400'});
	}
	
	/**
	  * Function written to load timeline google chart.
	**/
	if ($("#timeline-chart").length > 0){ 
		var container = document.getElementById('timeline-chart');
	    var chart = new google.visualization.Timeline(container);
	    var dataTable = new google.visualization.DataTable();
	    dataTable.addColumn({ type: 'string', id: 'Room' });
	    dataTable.addColumn({ type: 'string', id: 'Name' });
	    dataTable.addColumn({ type: 'date', id: 'Start' });
	    dataTable.addColumn({ type: 'date', id: 'End' });
	    dataTable.addRows([
			[ 'Magnolia Room',  'CSS Fundamentals',    new Date(0,0,0,12,0,0),  new Date(0,0,0,14,0,0) ],
			[ 'Magnolia Room',  'Intro JavaScript',    new Date(0,0,0,14,30,0), new Date(0,0,0,16,0,0) ],
			[ 'Magnolia Room',  'Advanced JavaScript', new Date(0,0,0,16,30,0), new Date(0,0,0,19,0,0) ],
			[ 'Gladiolus Room', 'Intermediate Perl',   new Date(0,0,0,12,30,0), new Date(0,0,0,14,0,0) ],
			[ 'Gladiolus Room', 'Advanced Perl',       new Date(0,0,0,14,30,0), new Date(0,0,0,16,0,0) ],
			[ 'Gladiolus Room', 'Applied Perl',        new Date(0,0,0,16,30,0), new Date(0,0,0,18,0,0) ],
			[ 'Petunia Room',   'Google Charts',       new Date(0,0,0,12,30,0), new Date(0,0,0,14,0,0) ],
			[ 'Petunia Room',   'Closure',             new Date(0,0,0,14,30,0), new Date(0,0,0,16,0,0) ],
			[ 'Petunia Room',   'App Engine',          new Date(0,0,0,16,30,0), new Date(0,0,0,18,30,0) ]]);
	    var options = {
			timeline: { colorByRowLabel: true },
			backgroundColor: '#ffd',
			height:400,
	    };
	    chart.draw(dataTable, options);
	}
	
	/**
	  * Function written to load org google chart.
	**/
	if ($("#org-chart").length > 0){ 
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Name');
		data.addColumn('string', 'Manager');
		data.addColumn('string', 'ToolTip');
		data.addRows([
			[{v:'Mike', f:'Mike<div style="color:red; font-style:italic">President</div>'},
			'', 'The President'],
			[{v:'Jim', f:'Jim<div style="color:red; font-style:italic">Vice President</div>'},
			'Mike', 'VP'],
			['Alice', 'Mike', ''],
			['Bob', 'Jim', 'Bob Sponge'],
			['Carol', 'Bob', ''],
			['Carol', 'Bob', ''],
			['Jack', 'Mike', ''],
			['rock', 'Jack', ''],
		]);
		var chart = new google.visualization.OrgChart(document.getElementById('org-chart'));
		chart.draw(data, {allowHtml:true});
	}

	/**
	  * Function written to load gauge google chart.
	**/
	if ($("#gauge-chart").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Label', 'Value'],
			['Memory', 80],
			['CPU', 55],
			['Network', 68]
		]);

		var options = {
			width: 400, height: 400,
			redFrom: 90, redTo: 100,
			yellowFrom:75, yellowTo: 90,
			minorTicks: 5
		};
		var chart = new google.visualization.Gauge(document.getElementById('gauge-chart'));
		chart.draw(data, options);
		setInterval(function() {
			data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
			chart.draw(data, options);
		}, 13000);
		setInterval(function() {
			data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
			chart.draw(data, options);
		}, 5000);
		setInterval(function() {
			data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
			chart.draw(data, options);
		}, 26000);
	}
		
	/**
	  * Function written to load candlestick google chart.
	**/
	if ($("#candlestick-chart").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Mon', 20, 28, 38, 45],
			['Tue', 31, 38, 55, 66],
			['Wed', 50, 55, 77, 80],
			['Thu', 77, 77, 66, 50],
			['Fri', 68, 66, 22, 15]
		], true);
		var options = {
			legend:'none',
			height:400,
			colors:['#5e6db3'],
		};
		var chart = new google.visualization.CandlestickChart(document.getElementById('candlestick-chart'));
		chart.draw(data, options);
	}
    
	/**
	  * Function written to load geo google chart.
	**/
	if ($("#geo-chart").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Country', 'Popularity'],
			['Germany', 200],
			['United States', 300],
			['Brazil', 400],
			['Canada', 500],
			['France', 600],
			['RU', 700]
		]);
		var options = {height:400, colors:['#00ca95'],};
		var chart = new google.visualization.GeoChart(document.getElementById('geo-chart'));
		chart.draw(data, options);
	}

	/**
	  * Function written to load scattered google chart.
	**/
	if ($("#scattered-chart").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Age', 'Weight'],
			[ 8,      12],
			[ 4,      5.5],
			[ 11,     14],
			[ 4,      5],
			[ 3,      3.5],
			[ 6.5,    12],
			[ 15,      30],
			[ 18,      36],
			[ 20,     40],
			[ 24,      28],
			[ 13,      26],
			[ 9.5,    18]
		]);
		var options = {
			title: 'Age vs. Weight comparison',
			hAxis: {title: 'Age', minValue: 0, maxValue: 15},
			vAxis: {title: 'Weight', minValue: 0, maxValue: 15},
			legend: 'none',
			height:400
		};
		var chart = new google.visualization.ScatterChart(document.getElementById('scattered-chart'));
		chart.draw(data, options);
	}

	/**
	  * Function written to load histogram google chart.
	**/
	if ($("#histogram-chart-1").length > 0){
		var data = google.visualization.arrayToDataTable([
	          ['Dinosaur', 'Length'],
	          ['Acrocanthosaurus (top-spined lizard)', 12.2],
	          ['Albertosaurus (Alberta lizard)', 9.1],
	          ['Allosaurus (other lizard)', 12.2],
	          ['Apatosaurus (deceptive lizard)', 22.9],
	          ['Archaeopteryx (ancient wing)', 0.9],
	          ['Argentinosaurus (Argentina lizard)', 36.6],
	          ['Baryonyx (heavy claws)', 9.1],
	          ['Brachiosaurus (arm lizard)', 30.5],
	          ['Ceratosaurus (horned lizard)', 6.1],
	          ['Coelophysis (hollow form)', 2.7],
	          ['Compsognathus (elegant jaw)', 0.9],
	          ['Deinonychus (terrible claw)', 2.7],
	          ['Diplodocus (double beam)', 27.1],
	          ['Dromicelomimus (emu mimic)', 3.4],
	          ['Gallimimus (fowl mimic)', 5.5],
	          ['Mamenchisaurus (Mamenchi lizard)', 21.0],
	          ['Megalosaurus (big lizard)', 7.9],
	          ['Microvenator (small hunter)', 1.2],
	          ['Ornithomimus (bird mimic)', 4.6],
	          ['Oviraptor (egg robber)', 1.5],
	          ['Plateosaurus (flat lizard)', 7.9],
	          ['Sauronithoides (narrow-clawed lizard)', 2.0],
	          ['Seismosaurus (tremor lizard)', 45.7],
	          ['Spinosaurus (spiny lizard)', 12.2],
	          ['Supersaurus (super lizard)', 30.5],
	          ['Tyrannosaurus (tyrant lizard)', 15.2],
	          ['Ultrasaurus (ultra lizard)', 30.5],
	          ['Velociraptor (swift robber)', 1.8]]);

	        var options = {
	          title: 'Lengths of dinosaurs, in meters',
	          legend: { position: 'none' },
	          height:400,
	          colors:['#5e6db3'],
	        };

	        var chart = new google.visualization.Histogram(document.getElementById('histogram-chart-1'));
	        chart.draw(data, options);
	}
		
	/**
	  * Function written to load histogram google chart.
	**/
	if ($("#histogram-chart-2").length > 0){
		var data = google.visualization.arrayToDataTable([
			['Quarks', 'Leptons', 'Gauge Bosons', 'Scalar Bosons'],
			[2/3, -1, 0, 0],
			[2/3, -1, 0, null],
			[2/3, -1, 0, null],
			[-1/3, 0, 1, null],
			[-1/3, 0, -1, null],
			[-1/3, 0, null, null],
			[-1/3, 0, null, null]
		]);

		var options = {
			title: 'Charges of subatomic particles',
			legend: { position: 'top', maxLines: 2 },
			colors:['#00ca95', '#f17316', '#5e6db3', '#fd7b6c'],
			interpolateNulls: false,
			height:400
		};
		var chart = new google.visualization.Histogram(document.getElementById('histogram-chart-2'));
        chart.draw(data, options);
    }
}

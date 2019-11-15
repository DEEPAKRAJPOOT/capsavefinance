//--------- D3 charts js -----------//

'use strict';
(function($) {
	d3_area_chart();
	d3_stacked_area_chart();
	d3_bar_chart();
	d3_group_bar();
	d3_multi_series();
	d3_line_chart();
	d3_stacked_bar();
})(jQuery);

function d3_area_chart(){
	if($('#d3-area-chart').length > 0){
		var svg1 = d3.select("#d3-area-chart"),
			 margin = {top: 20, right: 20, bottom: 30, left: 50},
			 width = +svg1.attr("width") - margin.left - margin.right,
			 height = +svg1.attr("height") - margin.top - margin.bottom,
			 g1 = svg1.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		var parseTime = d3.timeParse("%d-%b-%y");
	
		var x = d3.scaleTime()
			 .rangeRound([0, width]);
	
		var y = d3.scaleLinear()
			 .rangeRound([height, 0]);
	
		var area = d3.area()
			 .x(function(d) { return x(d.date); })
			 .y1(function(d) { return y(d.close); });
	
		d3.tsv("assets/js/data/area-data.tsv", function(d) {
		  d.date = parseTime(d.date);
		  d.close = +d.close;
		  return d;
		}, function(error, data) {
		  if (error) throw error;
	
		  x.domain(d3.extent(data, function(d) { return d.date; }));
		  y.domain([0, d3.max(data, function(d) { return d.close; })]);
		  area.y0(y(0));
	
		  g1.append("path")
				.datum(data)
				.attr("fill", "#5e6db3")
				.attr("d", area);
	
		  g1.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x));
	
		  g1.append("g")
				.call(d3.axisLeft(y))
			 .append("text")
				.attr("fill", "#000")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", "0.71em")
				.attr("text-anchor", "end")
				.text("Price ($)");
		});
	}
}
function d3_stacked_area_chart(){
	if($("#d3-stacked-area").length > 0){
		var svg2 = d3.select("#d3-stacked-area"),
			 margin = {top: 20, right: 20, bottom: 30, left: 50},
			 width = svg2.attr("width") - margin.left - margin.right,
			 height = svg2.attr("height") - margin.top - margin.bottom;
	
		var parseDate = d3.timeParse("%Y %b %d");
	
		var x = d3.scaleTime().range([0, width]),
			 y = d3.scaleLinear().range([height, 0]),
			 z = d3.scaleOrdinal(d3.schemeCategory10);
	
		var stack = d3.stack();
	
		var area = d3.area()
			 .x(function(d, i) { return x(d.data.date); })
			 .y0(function(d) { return y(d[0]); })
			 .y1(function(d) { return y(d[1]); });
	
		var g2 = svg2.append("g")
			 .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		d3.tsv("assets/js/data/stacked-area-data.tsv", type, function(error, data) {
			if (error) throw error;
	
			var keys = data.columns.slice(1);
			
			x.domain(d3.extent(data, function(d) { return d.date; }));
			z.domain(keys);
			stack.keys(keys);
	
			var layer = g2.selectAll(".layer")
				.data(stack(data))
				.enter().append("g")
				.attr("class", "layer")
				.attr("fill", "#fff");
			
			layer.append("path")
				.attr("class", "area")
				.style("fill", function(d) { return z(d.key); })
				.attr("d", area);
			
			layer.filter(function(d) { return d[d.length - 1][1] - d[d.length - 1][0] > 0.01; })
				.append("text")
				.attr("x", width - 6)
				.attr("y", function(d) { return y((d[d.length - 1][0] + d[d.length - 1][1]) / 2); })
				.attr("dy", ".35em")
				.style("font", "10px sans-serif")
				.style("text-anchor", "end")
				.text(function(d) { return d.key; });
				
			g2.append("g").attr("class", "axis axis--x").attr("transform", "translate(0," + height + ")").call(d3.axisBottom(x));
			g2.append("g").attr("class", "axis axis--y").call(d3.axisLeft(y).ticks(10, "%"));
		});
	
		function type(d, i, columns) {
			d.date = parseDate(d.date);
			for (var i = 1, n = columns.length; i < n; ++i) d[columns[i]] = d[columns[i]] / 100;
			return d;
		}
	}
}
	
	/*-------- d3 bar chart ---------*/
function d3_bar_chart(){
	if($("#d3-bar-chart").length > 0){
		var svg3 = d3.select("#d3-bar-chart"),
			 margin = {top: 20, right: 20, bottom: 30, left: 40},
			 width = +svg3.attr("width") - margin.left - margin.right,
			 height = +svg3.attr("height") - margin.top - margin.bottom;
	
		var x = d3.scaleBand().rangeRound([0, width]).padding(0.1),
			 y = d3.scaleLinear().rangeRound([height, 0]);
	
		var g3 = svg3.append("g")
			 .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		d3.tsv("assets/js/data/bar-data.tsv", function(d) {
		  d.frequency = +d.frequency;
		  return d;
		}, function(error, data) {
		  if (error) throw error;
	
		  x.domain(data.map(function(d) { return d.letter; }));
		  y.domain([0, d3.max(data, function(d) { return d.frequency; })]);
	
		  g3.append("g")
				.attr("class", "axis axis--x")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x));
	
		  g3.append("g")
				.attr("class", "axis axis--y")
				.call(d3.axisLeft(y).ticks(10, "%"))
			 .append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", "0.71em")
				.attr("text-anchor", "end")
				.text("Frequency");
	
		  g3.selectAll(".bar")
			 .data(data)
			 .enter().append("rect")
				.attr("class", "bar")
				.attr("x", function(d) { return x(d.letter); })
				.attr("y", function(d) { return y(d.frequency); })
				.attr("width", x.bandwidth())
				.attr("height", function(d) { return height - y(d.frequency); });
		});
	}
}

function d3_group_bar(){
	if($("#d3-group-bar").length > 0){
		var svg4 = d3.select("#d3-group-bar"),
			 margin = {top: 20, right: 20, bottom: 30, left: 40},
			 width = +svg4.attr("width") - margin.left - margin.right,
			 height = +svg4.attr("height") - margin.top - margin.bottom,
			 g4 = svg4.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		var x0 = d3.scaleBand()
			 .rangeRound([0, width])
			 .paddingInner(0.1);
	
		var x1 = d3.scaleBand()
			 .padding(0.05);
	
		var y = d3.scaleLinear()
			 .rangeRound([height, 0]);
	
		var z = d3.scaleOrdinal()
			 .range(["#99ead5", "#33d5aa", "#00ca95", "#adecfd", "#5ad9fa", "#31cff9", "#0bc9fb"]);
	
		d3.csv("assets/js/data/group-bar-data.csv", function(d, i, columns) {
			for (var i = 1, n = columns.length; i < n; ++i) d[columns[i]] = +d[columns[i]];
			return d;
		}, function(error, data) {
			if (error) throw error;
			var keys = data.columns.slice(1);	
			x0.domain(data.map(function(d) { return d.State; }));
			x1.domain(keys).rangeRound([0, x0.bandwidth()]);
			y.domain([0, d3.max(data, function(d) { return d3.max(keys, function(key) { return d[key]; }); })]).nice();
	
			g4.append("g")
				.selectAll("g")
				.data(data)
				.enter().append("g")
				.attr("transform", function(d) { return "translate(" + x0(d.State) + ",0)"; })
				.selectAll("rect")
				.data(function(d) { return keys.map(function(key) { return {key: key, value: d[key]}; }); })
				.enter().append("rect")
				.attr("x", function(d) { return x1(d.key); })
				.attr("y", function(d) { return y(d.value); })
				.attr("width", x1.bandwidth())
				.attr("height", function(d) { return height - y(d.value); })
				.attr("fill", function(d) { return z(d.key); });
	
		  g4.append("g")
				.attr("class", "axis")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x0));
	
		  g4.append("g")
				.attr("class", "axis")
				.call(d3.axisLeft(y).ticks(null, "s"))
			 	.append("text")
				.attr("x", 2)
				.attr("y", y(y.ticks().pop()) + 0.5)
				.attr("dy", "0.32em")
				.attr("fill", "#000")
				.attr("font-weight", "bold")
				.attr("text-anchor", "start")
				.text("Population");
	
		  var legend = g4.append("g")
				.attr("font-family", "sans-serif")
				.attr("font-size", 10)
				.attr("text-anchor", "end")
			 	.selectAll("g")
			 	.data(keys.slice().reverse())
			 	.enter().append("g")
				.attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });
	
		  legend.append("rect")
				.attr("x", width - 19)
				.attr("width", 19)
				.attr("height", 19)
				.attr("fill", z);
	
		  legend.append("text")
				.attr("x", width - 24)
				.attr("y", 9.5)
				.attr("dy", "0.32em")
				.text(function(d) { return d; });
		});
	}
}

function d3_stacked_bar(){
	if($("#d3-stacked-bar").length > 0){
		var svg5 = d3.select("#d3-stacked-bar"),
			 margin = {top: 20, right: 20, bottom: 30, left: 40},
			 width = +svg5.attr("width") - margin.left - margin.right,
			 height = +svg5.attr("height") - margin.top - margin.bottom,
			 g5 = svg5.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		var x = d3.scaleBand()
			 .rangeRound([0, width])
			 .paddingInner(0.05)
			 .align(0.1);
	
		var y = d3.scaleLinear()
			 .rangeRound([height, 0]);
	
		var z = d3.scaleOrdinal()
			 .range(["#fecac4", "#fd9589", "#fd7b6c", "#bfc5e1", "#7e8ac2", "#5e6db3", "#5b6dc2"]);
	
		d3.csv("assets/js/data/stacked-bar-data.tsv", function(d, i, columns) {
			var t= 0;
		  for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
		  d.total = t;
		  return d;
		}, function(error, data) {
		  if (error) throw error;
	
		  var keys = data.columns.slice(1);
	
		  data.sort(function(a, b) { return b.total - a.total; });
		  x.domain(data.map(function(d) { return d.State; }));
		  y.domain([0, d3.max(data, function(d) { return d.total; })]).nice();
		  z.domain(keys);
	
		  g5.append("g")
			 .selectAll("g")
			 .data(d3.stack().keys(keys)(data))
			 .enter().append("g")
				.attr("fill", function(d) { return z(d.key); })
			 .selectAll("rect")
			 .data(function(d) { return d; })
			 .enter().append("rect")
				.attr("x", function(d) { return x(d.data.State); })
				.attr("y", function(d) { return y(d[1]); })
				.attr("height", function(d) { return y(d[0]) - y(d[1]); })
				.attr("width", x.bandwidth());
	
		  g5.append("g")
				.attr("class", "axis")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x));
	
		  g5.append("g")
				.attr("class", "axis")
				.call(d3.axisLeft(y).ticks(null, "s"))
			 .append("text")
				.attr("x", 2)
				.attr("y", y(y.ticks().pop()) + 0.5)
				.attr("dy", "0.32em")
				.attr("fill", "#000")
				.attr("font-weight", "bold")
				.attr("text-anchor", "start")
				.text("Population");
	
		  var legend = g5.append("g")
				.attr("font-family", "sans-serif")
				.attr("font-size", 10)
				.attr("text-anchor", "end")
			 .selectAll("g")
			 .data(keys.slice().reverse())
			 .enter().append("g")
				.attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });
	
		  legend.append("rect")
				.attr("x", width - 19)
				.attr("width", 19)
				.attr("height", 19)
				.attr("fill", z);
	
		  legend.append("text")
				.attr("x", width - 24)
				.attr("y", 9.5)
				.attr("dy", "0.32em")
				.text(function(d) { return d; });
		});
	}
}
	
	/*------ d3 line charts -------*/
function d3_line_chart(){
	if($("#d3-line-chart").length > 0){
		var svg6 = d3.select("#d3-line-chart"),
			 margin = {top: 20, right: 20, bottom: 30, left: 50},
			 width = +svg6.node().getBoundingClientRect().width - margin.left - margin.right,
			 height = +svg6.attr("height") - margin.top - margin.bottom,
			 g6 = svg6.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		var parseTime = d3.timeParse("%d-%b-%y");
	
		var x = d3.scaleTime().rangeRound([0, width]);
	
		var y = d3.scaleLinear().rangeRound([height, 0]);
	
		var line = d3.line().x(function(d) { return x(d.date); }).y(function(d) { return y(d.close); });
	
		d3.tsv("assets/js/data/line-data.tsv", function(d) {
			d.date = parseTime(d.date);
			d.close = +d.close;
			return d;
		}, function(error, data) {
			if (error) throw error;
	
		  x.domain(d3.extent(data, function(d) { return d.date; }));
		  y.domain(d3.extent(data, function(d) { return d.close; }));
	
		  g6.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x))
			 .select(".domain")
				.remove();
	
		  g6.append("g")
				.call(d3.axisLeft(y))
			 .append("text")
				.attr("fill", "#000")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", "0.71em")
				.attr("text-anchor", "end")
				.text("Price ($)");
	
		  g6.append("path")
				.datum(data)
				.attr("fill", "none")
				.attr("stroke", "#00ca85")
				.attr("stroke-linejoin", "round")
				.attr("stroke-linecap", "round")
				.attr("stroke-width", 2)
				.attr("d", line);
		});
	}
}
	
	
	//------------ Multi series line chart js --------------//
function d3_multi_series(){
	if($("#d3-multi-series").length > 0){
		var svg7 = d3.select("#d3-multi-series"),
			 margin = {top: 20, right: 80, bottom: 30, left: 50},
			 width = svg7.attr("width") - margin.left - margin.right,
			 height = svg7.attr("height") - margin.top - margin.bottom,
			 g7 = svg7.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	
		var parseTime = d3.timeParse("%Y%m%d");
	
		var x = d3.scaleTime().range([0, width]),
			 y = d3.scaleLinear().range([height, 0]),
			 z = d3.scaleOrdinal(d3.schemeCategory10);
	
		var line = d3.line()
			 .curve(d3.curveBasis)
			 .x(function(d) { return x(d.date); })
			 .y(function(d) { return y(d.temperature); });
	
		d3.tsv("assets/js/data/multi-data.tsv", type, function(error, data) {
		  if (error) throw error;
	
		  var cities = data.columns.slice(1).map(function(id) {
			 return {
				id: id,
				values: data.map(function(d) {
				  return {date: d.date, temperature: d[id]};
				})
			 };
		  });
	
		  x.domain(d3.extent(data, function(d) { return d.date; }));
	
		  y.domain([
			 d3.min(cities, function(c) { return d3.min(c.values, function(d) { return d.temperature; }); }),
			 d3.max(cities, function(c) { return d3.max(c.values, function(d) { return d.temperature; }); })
		  ]);
	
		  z.domain(cities.map(function(c) { return c.id; }));
	
		  g7.append("g")
				.attr("class", "axis axis--x")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x));
	
		  g7.append("g")
				.attr("class", "axis axis--y")
				.call(d3.axisLeft(y))
			 .append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", "0.71em")
				.attr("fill", "#000")
				.text("Temperature, ºF");
	
		  var city = g7.selectAll(".city")
			 .data(cities)
			 .enter().append("g")
				.attr("class", "city");
	
		  city.append("path")
				.attr("class", "line")
				.attr("fill", "none")
				.attr("stroke-width", 2)
				.attr("d", function(d) { return line(d.values); })
				.style("stroke", function(d) { return z(d.id); });
	
		  city.append("text")
				.datum(function(d) { return {id: d.id, value: d.values[d.values.length - 1]}; })
				.attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
				.attr("x", 3)
				.attr("dy", "0.35em")
				.style("font", "10px sans-serif")
				.text(function(d) { return d.id; });
		});
	
		var type = function(d, _, columns) {
		  d.date = parseTime(d.date);
		  for (var i = 1, n = columns.length, c; i < n; ++i) d[c = columns[i]] = +d[c];
		  return d;
		}
	}
}
//--------- Dimple charts js -----------//
'use strict';
(function($){
	dimple_line_chart();
	dimple_line_group();
	dimple_line_multiple();
	dimple_line_group_multiple();
	dimple_line_curve();
	dimple_dual_measure();
	dimple_line_vetical();
	dimple_vert_multiple();
	dimple_vert_group();
	dimple_vert_group_multiple();
	dimple_horz_bar();
	dimple_horz_stacked();
	dimple_full_horizontal();
	dimple_horz_grouped();
	dimple_horz_group();
	dimple_horz_full();
	dimple_vertical_bar();
	dimple_vertical_stacked();
	dimple_vert_full();
	dimple_vert_grouped();
	dimple_vertical_group();
	dimple_vertical_full();
	dimple_pie_chart();
	dimple_pie_matrix();
	dimple_lollypop_group_pie();
	dimple_lollypop_pie();
	dimple_vertical_stack_area();
	dimple_vertical_area();
	dimple_full_vertical();
	dimple_vert_group_area();
	dimple_areachart();
	dimple_stacked_area();
	dimple_full_stacked_area();
	dimple_group_area();
})(jQuery);

function resize(myChart) {
	setTimeout(function() {
		myChart.draw(0, true);
	}, 100);
}
function dimple_line_chart(){
	if($("#chartContainer").length > 0){
		var svg = dimple.newSvg("#chartContainer", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 505, 305);
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries(null, dimple.plot.line);
			myChart.draw();
		});
	}
}

function dimple_line_group(){
	if($("#horizontal-group").length > 0){
		var svg = dimple.newSvg("#horizontal-group", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, "100%", "100%");
			var x = myChart.addCategoryAxis("x", ["Owner", "Month"]);
			x.addGroupOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries("Owner", dimple.plot.line);
			s.barGap = 0.05;
			myChart.draw();
		});
	}
}
function dimple_line_multiple(){
	if($("#dimple-multiple-line").length > 0){
		var svg = dimple.newSvg("#dimple-multiple-line", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 505, 305);
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addSeries("Channel", dimple.plot.line);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_line_group_multiple(){	
	if($("#group-multiple-line").length > 0){
		var svg = dimple.newSvg("#group-multiple-line", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 430, 330);
			var x = myChart.addCategoryAxis("x", ["Owner", "Month"]);
			x.addGroupOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries(["Brand"], dimple.plot.line);
			s.barGap = 0.05;
			myChart.addLegend(510, 20, 100, 300, "left");
			myChart.draw();
		});
	}
}

function dimple_line_curve(){	
	if($("#curvy-line").length > 0){
		var svg = dimple.newSvg("#curvy-line", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, "100%", "100%");
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries("Channel", dimple.plot.line);
			s.interpolation = "cardinal";
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_dual_measure(){	
	if($("#dual-measure").length > 0){
		var svg = dimple.newSvg("#dual-measure", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(50, 55, 500, 310)
			var x = myChart.addMeasureAxis("x", "Distribution");
			x.overrideMin = 20;
			var y = myChart.addMeasureAxis("y", "Price");
			y.overrideMin = 50
			var s = myChart.addSeries(["Month", "Owner"], dimple.plot.line);
			s.addOrderRule("Date");
			s.aggregate = dimple.aggregateMethod.avg;
			myChart.addLegend(130, 10, 400, 35, "right");
			myChart.draw();
		});
	}
}

function dimple_line_vetical(){
	if($("#single-vertical").length > 0){
		var svg = dimple.newSvg("#single-vertical", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 485, 330);
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			var s = myChart.addSeries(null, dimple.plot.line);
			myChart.draw();
		});
	}
}

function dimple_vert_multiple(){	
	if($("#multiple-vertical").length > 0){
		var svg = dimple.newSvg("#multiple-vertical", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 480, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries("Channel", dimple.plot.line);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}
	
function dimple_vert_group(){
	if($("#grouped-multiple").length > 0){
		var svg = dimple.newSvg("#grouped-multiple", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(90, 30, 470, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", ["Owner", "Month"]);
			y.addGroupOrderRule("Date");
			var s = myChart.addSeries("Owner", dimple.plot.line);
			s.barGap = 0.05;
			myChart.draw();
		});
	}
}

function dimple_vert_group_multiple(){	
	if($("#vert-grouped-multi").length > 0){
		var svg = dimple.newSvg("#vert-grouped-multi", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(90, 30, 400, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", ["Owner", "Month"]);
			y.addGroupOrderRule("Date");
			var s = myChart.addSeries(["Brand"], dimple.plot.line);
			s.barGap = 0.05;
			myChart.addLegend(510, 20, 100, 300, "left");
			myChart.draw();
		});
	}
}

function dimple_horz_bar(){	
	if($("#horizontal-bar").length > 0){
		var svg = dimple.newSvg("#horizontal-bar", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 480, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries(null, dimple.plot.bar);
			myChart.draw();
		});
	}
}

function dimple_horz_stacked(){
	if($("#horizontal-stacked-bar").length > 0){
		var svg = dimple.newSvg("#horizontal-stacked-bar", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 480, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(60, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_full_horizontal(){	
	if($("#horizontal-full").length > 0){
		var svg = dimple.newSvg("#horizontal-full", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 480, 330)
			myChart.addPctAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(60, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_horz_grouped(){	
	if($("#horizontal-grouped").length > 0){
		var svg = dimple.newSvg("#horizontal-grouped", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(80, 30, 480, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			myChart.addCategoryAxis("y", ["Price Tier", "Channel"]);
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(60, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_horz_group(){	
	if($("#horiz-group").length > 0){
		var svg = dimple.newSvg("#horiz-group", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(80, 30, 480, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			myChart.addCategoryAxis("y", ["Price Tier", "Channel"]);
			myChart.addSeries("Owner", dimple.plot.bar);
			myChart.addLegend(200, 10, 380, 20, "right");
			myChart.draw();
		});
	}
}
	
function dimple_horz_full(){
	if($("#horiz-full").length > 0){
		var svg = dimple.newSvg("#horiz-full", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(80, 30, 480, 330)
			myChart.addPctAxis("x", "Unit Sales");
			myChart.addCategoryAxis("y", ["Price Tier", "Channel"]);
			myChart.addSeries("Owner", dimple.plot.bar);
			myChart.addLegend(200, 10, 380, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vertical_bar(){	
	if($("#vertical-bar").length > 0){
		var svg = dimple.newSvg("#vertical-bar", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 510, 305)
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addSeries(null, dimple.plot.bar);
			myChart.draw();
		});
	}
}

function dimple_vertical_stacked(){
	if($("#vertical-stacked").length > 0){
		var svg = dimple.newSvg("#vertical-stacked", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 510, 305)
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(60, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vert_full(){	
	if($("#vertical-full").length > 0){
		var svg = dimple.newSvg("#vertical-full", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(65, 30, 505, 305)
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addPctAxis("y", "Unit Sales");
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(60, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vert_grouped(){
	if($("#vertical-grouped").length > 0){
		var svg = dimple.newSvg("#vertical-grouped", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 510, 330)
			myChart.addCategoryAxis("x", ["Price Tier", "Channel"]);
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addSeries("Channel", dimple.plot.bar);
			myChart.addLegend(65, 10, 510, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vertical_group(){	
	if($("#vert-group").length > 0){
		var svg = dimple.newSvg("#vert-group", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 45, 510, 315)
			myChart.addCategoryAxis("x", ["Price Tier", "Channel"]);
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addSeries("Owner", dimple.plot.bar);
			myChart.addLegend(200, 10, 380, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vertical_full(){
	if($("#vert-full").length > 0){
		var svg = dimple.newSvg("#vert-full", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(65, 45, 505, 315)
			myChart.addCategoryAxis("x", ["Price Tier", "Channel"]);
			myChart.addPctAxis("y", "Unit Sales");
			myChart.addSeries("Owner", dimple.plot.bar);
			myChart.addLegend(200, 10, 380, 20, "right");
			myChart.draw();
		});
	}
}
function dimple_pie_chart(){	
	if($("#dimple-pie-chart").length > 0){
		var svg = dimple.newSvg("#dimple-pie-chart", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(20, 20, 460, 360)
			myChart.addMeasureAxis("p", "Unit Sales");
			myChart.addSeries("Owner", dimple.plot.pie);
			myChart.addLegend(500, 20, 90, 300, "left");
			myChart.draw();
		});
	}
}
function dimple_pie_matrix(){	
	if($("#pie-matrix").length > 0){
		var svg = dimple.newSvg("#pie-matrix", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(95, 25, 475, 335)
			myChart.addCategoryAxis("x", "Price Tier");
			myChart.addCategoryAxis("y", "Pack Size");
			myChart.addMeasureAxis("p", "Unit Sales");
			var pies = myChart.addSeries("Channel", dimple.plot.pie);
			pies.radius = 25;
			myChart.addLegend(240, 10, 330, 20, "right");
			myChart.draw();
		});
	}
}
function dimple_lollypop_pie(){	
	if($("#lollipop-pie").length > 0){
		var svg = dimple.newSvg("#lollipop-pie", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Date", [
			"01/07/2012", "01/08/2012", "01/09/2012",
			"01/10/2012", "01/11/2012", "01/12/2012"]);
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 500, 330)
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addMeasureAxis("p", "Unit Sales");
			var pies = myChart.addSeries("Channel", dimple.plot.pie);
			pies.radius = 20;
			myChart.addLegend(140, 10, 360, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_lollypop_group_pie(){	
	if($("#group-lollipop-pie").length > 0){
		var svg = dimple.newSvg("#group-lollipop-pie", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(65, 50, 505, 310)
			myChart.addCategoryAxis("x", ["Price Tier", "Channel"]);
			myChart.addMeasureAxis("y", "Unit Sales");
			myChart.addMeasureAxis("p", "Unit Sales");
			var pies = myChart.addSeries("Owner", dimple.plot.pie);
			pies.radius = 20;
			myChart.addLegend(170, 10, 410, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_vertical_area(){	
	if($("#vertical-area").length > 0){
		var svg = dimple.newSvg("#vertical-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"]);
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 485, 330);
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			var s = myChart.addSeries(null, dimple.plot.area);
			myChart.draw();
		});
	}
}

function dimple_vertical_stack_area(){	
	if($("#vertical-stack-area").length > 0){
		var svg = dimple.newSvg("#vertical-stack-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"]);
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 485, 330);
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries("Channel", dimple.plot.area);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}
function dimple_full_vertical(){	
	if($("#full-vertical-stack").length > 0){
		var svg = dimple.newSvg("#full-vertical-stack", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"]);
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(75, 30, 485, 330);
			myChart.addPctAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", "Month");
			y.addOrderRule("Date");
			myChart.addSeries("Channel", dimple.plot.area);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}
function dimple_vert_group_area(){	
	if($("#vt-group-area").length > 0){
		var svg = dimple.newSvg("#vt-group-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(90, 30, 470, 330)
			myChart.addMeasureAxis("x", "Unit Sales");
			var y = myChart.addCategoryAxis("y", ["Owner", "Month"]);
			y.addGroupOrderRule("Date");
			var s = myChart.addSeries("Owner", dimple.plot.area);
			s.lineWeight = 1;
			s.barGap = 0.05;
			myChart.draw();
		});
	}
}

function dimple_areachart(){
	if($("#dimple-areachart").length > 0){
		var svg = dimple.newSvg("#dimple-areachart", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 505, 305);
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries(null, dimple.plot.area);
			myChart.draw();
		});
	}
}
function dimple_stacked_area(){	
	if($("#stacked-area").length > 0){
		var svg = dimple.newSvg("#stacked-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 505, 305);
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries("Channel", dimple.plot.area);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}

function dimple_full_stacked_area(){	
	if($("#full-stacked-area").length > 0){
		var svg = dimple.newSvg("#full-stacked-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(65, 30, 505, 305);
			var x = myChart.addCategoryAxis("x", "Month");
			x.addOrderRule("Date");
			myChart.addPctAxis("y", "Unit Sales");
			myChart.addSeries("Channel", dimple.plot.area);
			myChart.addLegend(60, 10, 500, 20, "right");
			myChart.draw();
		});
	}
}
function dimple_group_area(){	
	if($("#group-area").length > 0){
		var svg = dimple.newSvg("#group-area", "100%", 400);
		d3.tsv("assets/js/data/dimple_data.tsv", function (data) {
			data = dimple.filterData(data, "Owner", ["Aperture", "Black Mesa"])
			var myChart = new dimple.chart(svg, data);
			myChart.setBounds(60, 30, 500, 330)
			var x = myChart.addCategoryAxis("x", ["Owner", "Month"]);
			x.addGroupOrderRule("Date");
			myChart.addMeasureAxis("y", "Unit Sales");
			var s = myChart.addSeries("Owner", dimple.plot.area);
			s.lineWeight = 1;
			s.barGap = 0.05;
			myChart.draw();
		});
	}
}
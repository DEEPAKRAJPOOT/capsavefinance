$(function () {


    // Mobile Navigation
    $("body").on("click", "#mobileMenuTrigger", function () {
        $(this).toggleClass("active");
        $("#mobile-navigation").slideToggle();
    });

	$(".slide-accordian").find(".card").slideUp();
    $("body").on("click", ".slide-accordian .table-content-link", function () {
        var $this = $(this);
        var $target = $this.data("content");
            $this.parents(".slide-accordian").find($target).slideDown();
			$('.overlay_wallet').show();   
    });
	$("body").on("click", ".slide-accordian .close-popup", function () {
        var $this = $(this);
        var $target = $this.data("content");
            $(".slide-accordian .table-content-link").removeClass("active");
            $(".slide-accordian").find(".card").slideUp();
			$('.overlay_wallet').hide();});

    // Tab Wizard 
    $("body").on("click", ".tab-wizard-btn", function () {
        var $parent = $(this).closest(".tab-wizard");
        var $active = $parent.find(".tab-head-item.active");
        var $next = $active.next();
        var $prev = $active.prev();

        if ($(this).text() !== 'Previous') {
            $next.trigger("click");
            $active.addClass("visited");
        } else {
            $prev.trigger("click");
        }
    });

    // User Dashboard Area Chart
    if ($("#user-dashboard-chart").length > 0 || $("#user-dashboard-chart1").length > 0 || $("#user-dashboard-chart2").length > 0 || $("#user-dashboard-chart3").length > 0 || $("#user-dashboard-chart4").length > 0){

        Chart.pluginService.register({
            beforeDraw: function (chart, easing) {
                if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
                    var ctx = chart.chart.ctx;
                    var chartArea = chart.chartArea;

                    ctx.save();
                    ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
                    ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
                    ctx.restore();
                }
            }
        });

        var config = {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sept", "Oct", "Nov", "Dec"],
                datasets: [
                    {
                        data: ["109", "102", "105", "101", "102", "103", "109", "108", "105", "104", "100", "101"],
                        borderColor: "#006f45",
                        borderWidth: "3",
                        hoverBorderColor: "#ff7c3f",
                        fill: false
                    }]
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Dashboard Chart'
                },
                legend: {
                    display: false,
                },
                ticks: {
                    autoSkip: false
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    titleFontFamily: 'Montserrat',
                    backgroundColor: 'rgba(0,0,0,0.8)'
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false,
                            labelString: 'Value'
                        }
                        /*,
                            gridLines : {
                            display : false
                            }*/
                    }]
                }
            }
        };
		if ($("#user-dashboard-chart").length > 0){
			  var ctx = document.getElementById("user-dashboard-chart").getContext("2d");
			   new Chart(ctx, config);
		}
		if ($("#user-dashboard-chart1").length > 0){
			  var ctx1 = document.getElementById("user-dashboard-chart1").getContext("2d");
			   new Chart(ctx1, config);
		}
		if ($("#user-dashboard-chart2").length > 0){
			  var ctx2 = document.getElementById("user-dashboard-chart2").getContext("2d");
			   new Chart(ctx2, config);
		}
		if ($("#user-dashboard-chart3").length > 0){
			  var ctx3 = document.getElementById("user-dashboard-chart3").getContext("2d");
			   new Chart(ctx3, config);
		}
		if ($("#user-dashboard-chart4").length > 0){
			  var ctx4 = document.getElementById("user-dashboard-chart4").getContext("2d");
			   new Chart(ctx4, config);
		}
      
    }

    if ($(".ui-slider").length > 0) {
        $(".ui-slider").slider({
            min: 0,
            max: 30000,
            step: 100,
            range: "min",
            animate: true,
            value: 20000,
            slide: function (event, ui) {
                return false;
            }
        });
    }

    // Custom Selectbox
    selectbox();

    // Click Dropdown
    $("body").on("click", ".has-nav-dropdown.click-dropdown .trigger", function () {

        var $target = $(this).closest(".has-nav-dropdown");

        if (!$target.hasClass('active')) {
            $(".has-nav-dropdown.click-dropdown").removeClass("active");
            $target.addClass("active");
        } else {
            $target.removeClass("active");
        }
        
    });
    
    $("body").on("click", function (evt) {
        var $target = evt.target.className;
        /*var $targetParent = $("."+$target).parent();
        console.log($target);*/
        if (!$target.match("trigger")) {
           
            $(".has-nav-dropdown").removeClass("active");
            /*$(".has-nav-dropdown").each(function () {
                var $this = $(this);
                if ($this.hasClass('active')) {
                    $this.removeClass("active");
                }
            });*/
        }

    });
    
    
    
    // Menu Active Class
    /* $("body").on("click", ".sidebar-list-item", function() {
                $(".sidebar-list-item").removeClass("active");
            });


            $("body").on("click", ".sidebar-list-item", function() {
                var menuText = $(this).text();
                sessionStorage.setItem('Current Menu', menuText);
            });

            var menutoActive = sessionStorage.getItem("Current Menu");
 
            $(".sidebar-navigation .sidebar-list-item").each(function() {
                var $this = $(this);

                var textToMatch = $this.text();

                if (textToMatch === menutoActive) {
                    $(".sidebar-navigation .sidebar-list-item").removeClass("active");
                    $this.addClass("active");
                }

                if (menutoActive === "none") {
                    $(".sidebar-navigation .sidebar-list-item").removeClass("active");
                }

            });
 */
    

    // Custom Browse File Input
    $("body").on("change", ".custom-input-file .inputfile", function () {
        var $this = $(this);
        var $target = $this.closest(".custom-input-file").find(".selected-value .text");
        var filename = $this.val();
        if (filename.substring(3, 11) == 'fakepath') {
            filename = filename.substring(12);
        }
        $target.text(filename).parent().show();
        console.log("sds");
    });
    $("body").on("click", ".custom-input-file .input-close", function () {
        var $this = $(this);
        $this.closest(".custom-input-file").find(".inputfile").val('');
        $this.parent().hide();
    });

    // Card Header Filters
    $("body").on("click", ".card-filters-menu", function () {
        $(this).toggleClass("active").closest(".card-header-filters-wrap").find(".card-header-filters").slideToggle();
    });


    // Piechart
    if ($("#currentHoldingChart").length > 0) {
        var data = [{
                label: "Bitcoin",
                data: 20,
                color: "#82d13b"
        }, {
                label: "Stellar",
                data: 20,
                color: "#01c75c"
        }, {
                label: "Etherium",
                data: 20,
                color: "#604bb5"
        }, {
                label: "Ripple",
                data: 20,
                color: "#1c42af",
        },
            {
                label: "Neo",
                data: 20,
                color: "#26b3f0"
        }];

        $.plot($("#currentHoldingChart"), data, {
            series: {
                pie: {
                    show: true,
                    radius: 80,
                    innerRadius: 0.6,
                    label: {
                        show: true,
                        radius: 0.75,
                        formatter: function (label, series) {
                            return "<div style='font-size:0.6875rem; text-align:center; padding:15px; color:white;'>" + Math.round(series.percent) + "%</div>";
                        }
                    }
                }
            },
            legend: {
                show: true,
                container: $("#currentHoldingChartLegends")
            }
        });
    }


    // Calling Functions here
    TabNavigation();
    ResponsiveTable();

});


// On Scroll Function
$(window).on("scroll", function () {
    var scrollTop = $(window).scrollTop();

    if (scrollTop >= 20) {
        $("#header").addClass("scrolled").find(".primary-btn").addClass('has-shadow');

    } else {
        $("#header").removeClass("scrolled");
    }

});

// On Load Function
$(window).on("load", function () {

});

function selectbox() {

    if ($(".theme-selectbox").length > 0) {

        if (window.matchMedia('(min-width: 1025px)').matches) {
            $(".theme-selectbox").select2({
                minimumResultsForSearch: Infinity
            });
        }
    }
}

function TabNavigation() {

    $("body").on('click', ".tab-navigation .tab-head-item", function (e) {
        var $this = $(this);
        var $tabHead = $this.closest(".tab-head");
        var $target = $this.data("tab");
        var $targetWrapper = $($target).parent(".tab-content");
        $tabHead.find(".tab-head-item").removeClass('active');
        $targetWrapper.find("> .tab-pane:visible").removeClass('active').hide();
        $($target).addClass('active').show();
        $this.addClass('active');
        e.preventDefault();
    });

    $(".tab-navigation.has-memory").on("click", ".tab-head-item", function () {
        var tabText = $(this).text();
        localStorage.setItem('Current Active Tab', tabText);
    });

    var tabtoActive = localStorage.getItem("Current Active Tab");
    $(".tab-navigation .tab-head-item").each(function () {
        var $this = $(this);

        var textToMatch = $this.text();

        if (textToMatch === tabtoActive) {
            $this.trigger("click");
        }
    });

}

function Accordion() {
    if ($(".site-accordion").length > 0) {
        $('.site-accordion .accordion-block-content').hide();
        $('.site-accordion .accordion-block-heading').on("click", function () {
            var $trigger = $(this);

            if (!$trigger.hasClass('active')) {
                $('.site-accordion .accordion-block-heading').removeClass("active");

                $trigger.closest(".site-accordion").find(".accordion-block-content").stop(0, 0).slideUp("slow");
                $trigger.addClass("active").next().stop(0, 0).slideDown("slow");
                $trigger.next(".accordion-block-content").addClass("active");
            } else {
                $trigger.removeClass("active").next().stop(0, 0).slideUp("slow");
                $trigger.next(".accordion-block-content").removeClass("active");
            }

            return false;
        });

        if ($(".accordion-block-heading").hasClass("active-on-load")) {
            $(".accordion-block-heading.active-on-load").trigger("click");
        }
    }
}

function ResponsiveTable() {
    if ($(".responsive-table").length > 0) {
        $(".responsive-table tbody td").each(function (index) {
            cell = this.cellIndex;

            var ThValue = $(this).closest('table').find('th:eq(' + cell + ')').text();
            var dataLabelValue = "";

            if (ThValue !== "")
                dataLabelValue = ThValue;

            $(this).attr("data-label", dataLabelValue);
        });
    }
}

function removeTabMemory() {
    localStorage.removeItem("Current Active Tab");
}


$(document).ready(function () {
   $('#showmenu').click(function (e) {
        $('.login-wrapper').stop(true).toggleClass('slide_active');
		$('body,html').stop(true).toggleClass('overflow-hidden');
		$(this).stop(true).toggleClass('open');
    }); 
    $(document).click(function (e) {
        if (!$(e.target).closest('#showmenu, .login-wrapper').length) {
            $('.login-wrapper').stop(true).removeClass('slide_active');
            $('body,html').stop(true).removeClass('overflow-hidden');
			$('#showmenu').stop(true).removeClass('open');
        }
		
    });
});

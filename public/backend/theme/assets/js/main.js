'use strict';
(function($) {
	var Prtm = {
		Constants: {
			LEFTMARGIN:'315px',
			COLLAPSELEFTMARGIN: '63px',
		},
		MobileBreakPoint : 992,
		SidebarBreakPoint : 992,
		PrtmEle:{
			WINDOW: $(window),
			BODY: $('body'),
			SIDEBAR: $('.prtm-sidebar'),
			SIDENAV: $('.sidebar-nav'),
			MAIN: $('.prtm-main'),
			HEADER: $('.prtm-header'),
			CONTENTWRAP: $('.prtm-content-wrapper'),
			CONTENT: $('.prtm-content'),
			PRTMBLOCK: $('.prtm-block'),
			FOOTER: $('.prtm-footer'),
			HAMBURGER: $('.prtm-bars'),
		},
	   ResizeHandler:function(){
			$(window).resize(function(){
				if(Prtm.IsMobile()){
					$('.prtm-sidebar').addClass('mobile-menu');
				}
				else{
					$('.prtm-sidebar').show();
				}
			});
		},
		BindEvents:function(){
			this.PrtmEle.HAMBURGER.on('click',this.CollapseSidebar.bind(this));
		},
		CollapseSidebar: function(){
			$('.sidebar-nav').css('opacity',0);
			$('.sub-menu').slideUp();
			$('li.has-children').removeClass('opened');
			if(Prtm.IsMobile()){
         	this.PrtmEle.SIDEBAR.toggleClass('mobile-menu-in');      
			}
			if(Prtm.IsMobile()){
				 this.PrtmEle.SIDEBAR.slideToggle();
			}
			else{
				 this.PrtmEle.HAMBURGER.toggleClass("prtm-sidebar-closed is-active");
				 this.PrtmEle.BODY.toggleClass("prtm-sidebar-closed is-active");
				 this.PrtmEle.SIDEBAR.toggleClass('collapse');
			}
			if(this.PrtmEle.SIDEBAR.hasClass('collapse')){
				 setTimeout(function(){ $('.sidebar-nav').css('opacity',1);}, 500);
			}
			else{
				setTimeout(function(){$('.sidebar-nav').css('opacity',1);}, 500);
			}
		},
		IsMobile:function(){
			var mediaQueryString = '(max-width: ' + Prtm.SidebarBreakPoint + 'px)';
            var mediaQueryList = window.matchMedia(mediaQueryString);
			if(mediaQueryList.matches){
				this.PrtmEle.BODY.addClass('mobile').removeClass('desktop');
				return true;
			}
			else{
				this.PrtmEle.BODY.removeClass('mobile').addClass('desktop');
				return false;
			}
		},
		MenuHandler:function(){
			$(".prtm-sidebar .has-children > a").on('click',function (e) {
				e.preventDefault();
				if($("body").hasClass("prtm-sidebar-closed") && !$("body").hasClass("mobile")){
					return false;
				}
				var $parent = $(this).closest('li.has-children').toggleClass('opened')
				$('li.has-children').not($parent).removeClass('opened')
			   var $submenu = $(this).next('.sub-menu');
			   var $parentsubmenu = $(this).closest('.sub-menu');
			   $('.sidebar-menu .sub-menu').not($submenu).not($parentsubmenu).slideUp()
			   $(this).next('.sub-menu').slideToggle();
			});
		},
		RemoveEle:function(){
			$(document).on('click','[data-toggle^="remove"]',function(){
				$(this).closest($('.'+$(this).data('target'))).slideUp(400,function(){
					$(this).remove();
				});
			})
		},
		Modal:function(){
			$('.modal').insertAfter($('body'));
			if($('#draggable').length > 0){
				$("#draggable").draggable({
					 handle: ".modal-header"
				});
			}
		},
		SearchIcon:function(){
			$(".prtm-search-icon").on('click',function () {
        		$(".prtm-search-icon .prtm-navbar-search").addClass("active");
    		});
    		$(document).delegate('.prtm-search-area', 'click', function () {
        		$('.prtm-navbar-search').removeClass('active');
    		});
		},
		Counter:function(){
			/*----------- Counter ----------------*/
			$.each($('.count-item'), function () {
			    var count = $(this).data('count'),
			    numAnim = new CountUp(this, 0, count);
			    numAnim.start();
			});

	        /*----------- Counter down ----------------------*/
		    $('.getting-started').countdown('2017/09/01', function(event) {
		      var $this = $(this).html(event.strftime(''
		        + '<span>%w</span> weeks '
		        + '<span>%d</span> days '
		        + '<span>%H</span> hr '
		        + '<span>%M</span> min '
		        + '<span>%S</span> sec'));
		    });
		},
		BackToTop:function(){
			/*------- code for back to top animation --------*/
		    $("#back-top").hide();
		    $(window).scroll(function () {
		        if ($(this).scrollTop() > 300){
		            $('#back-top').fadeIn(0);
		        } else {
		            $('#back-top').fadeOut(0);
		        }
		    });
		    $('#back-top').on('click',function(e){
		        e.preventDefault();
		        $('body,html').animate({
		            scrollTop: 0
		        }, 1000);
		        return false;
		    });
		},
		RowEqualHeight:function(){
			/*---------- Row equal height -----------*/
		    $('.row-equal-height').each(function(){
		        var highestBox = [];
		        var i=0;
		        $('> div', this).each(function()
		        {
		            highestBox[i] = $(this).outerHeight();
		            i++;
		        });
		        var min_height=Math.min.apply(Math,highestBox);
		        $('> div',this).addClass('overflow-auto').css('max-height', min_height);
		        $('.overflow-auto .prtm-block').css('height', min_height);
		    });
		},
		ShowLoader:function(){
			$('body').append('<div class="prtm-loader-wrap"><div class="showbox"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div></div>');
		},
		Init:function(){
			this.ShowLoader();
			this.BindEvents();
			this.IsMobile();
			this.ResizeHandler();
			this.MenuHandler();
			this.RemoveEle();
			this.GlobalFeatures();
			this.Modal();
			this.SearchIcon();
			this.Counter();
			this.BackToTop();
			this.RowEqualHeight();
		},
		GlobalFeatures:function(){
			$('[data-toggle="tooltip"]').tooltip();
			/*---- code to initiate progress bars -----*/
			$(window).on('load',function(){
				$('.progress-bar').each(function(){
					$(this).scrollSpy();
					$(this).on('scrollSpy:enter', function () {
						$(this).width($(this).data('width'));
					}).scrollSpy();;
					$(this).scrollSpy();
				});
				$('.prtm-loader-wrap').fadeOut(300);
				$(window).resize();
			});
		},
		InitSliders:function(){
			$('[data-toggle="slider"]').slider({tooltip: 'always'});
		}
	};
	Prtm.Init();
})(jQuery);
$(document).ready(function(){
	$('.prtm-content-wrapper').addClass('loaded-block');	
	if($('#zoom_03').length > 0){
		$("#zoom_03").elevateZoom({
			gallery:'gallery_01', 
			cursor: 'pointer', 
			galleryActiveClass: 'active', 
			imageCrossfade: true, 
		}); 
	}
})
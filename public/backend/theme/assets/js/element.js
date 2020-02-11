'use strict';
(function($) {
	var PrtmElement = {
		/**
		  * Function written to load UI slider based on data attributes.
		**/
		FormSliders:function(){
			$('[data-slider="form"]').each(function(){
				var min = $(this).data('min');
				var max = $(this).data('max');
				var value = $(this).data('value');
				var step = $(this).data('step');
				var orientation = $(this).data('orientation');
				var tooltip = $(this).data('tooltip');
				var toolposition = $(this).data('toolposition');
			    $(this).slider({
			    	range:"min",
			        min: min,
			        max: max,
			        value: value,
			        step: step,
			        orientation: orientation,
			        tooltip_position: toolposition,
			        tooltip: tooltip,
			    });
			});
		},
		/**
		  * Function written to load Datepickers.
		**/
		DatePickers:function(){
				$('.datepicker').datepicker();
		    var startDate = new Date(2012,1,20);
		    var endDate = new Date(2012,1,25);
		    $('.datepicker-start-date').datepicker()
		        .on('changeDate', function(ev){
		            if (ev.date.valueOf() > endDate.valueOf()){
		                $('#alert').show().find('strong').text('The start date can not be greater then the end date');
		            } else {
		                $('#alert').hide();
		                startDate = new Date(ev.date);
		                $('#startDate').text($('.datepicker-start-date').data('date'));
		            }
		            $('.datepicker-start-date').datepicker('hide');
		        });
		    $('.datepicker-end-date').datepicker()
		        .on('changeDate', function(ev){
		            if (ev.date.valueOf() < startDate.valueOf()){
		                $('#alert').show().find('strong').text('The end date can not be less then the start date');
		            } else {
		                $('#alert').hide();
		                endDate = new Date(ev.date);
		                $('#endDate').text($('.datepicker-end-date').data('date'));
		            }
		        $('.datepicker-end-date').datepicker('hide');
		    });

		    // Disabling dates
		    var nowTemp = new Date();
		    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

		    var checkin = $('.datepicker-disable-date').datepicker({
		      onRender: function(date) {
		        return date.valueOf() < now.valueOf() ? 'disabled' : '';
		    }
		    }).on('changeDate', function(ev) {
		      if (ev.date.valueOf() > checkout.date.valueOf()) {
		        var newDate = new Date(ev.date)
		        newDate.setDate(newDate.getDate() + 1);
		        checkout.setValue(newDate);
		      }
		      checkin.hide();
		    }).data('datepicker');
		},
		/**
		  * Function to load  Date and Time picker
		**/
		DateTimePickers:function(){
		    $('.datetimepicker').datetimepicker({
		        weekStart: 1,
		        todayBtn:  1,
		        autoclose: 1,
		        todayHighlight: 1,
		        startView: 2,
		        forceParse: 0,
		        showMeridian: 1
		    });
		    $('.datetimepicker-date').datetimepicker({
		        weekStart: 1,
		        todayBtn:  1,
		        autoclose: 1,
		        todayHighlight: 1,
		        startView: 2,
		        minView: 2,
		        forceParse: 0
		    });
		    $('.datetimepicker-time').datetimepicker({
		        weekStart: 1,
		        todayBtn:  1,
		        autoclose: 1,
		        todayHighlight: 1,
		        startView: 1,
		        minView: 0,
		        maxView: 1,
		        forceParse: 0
		    });
		},
		/**
		  * Function to load Timepickers
		**/
		TimePickers:function(){
		//-------------- Time picker -----------------*/
		$('.timepicker-default').timepicker({
		        showInputs: false,
		    });
		    $('.timepicker-seconds').timepicker({
		        disableFocus: true,
		        showInputs: false,
		        showSeconds: true,
		        defaultValue: '12:45:30'
		    });
		    $('.timepicker-24').timepicker({
		        maxHours: 24,
		        showInputs: false,
		        showMeridian: false,
		    });
		    $('.timepicker').parent('.input-group').on('click', '.input-group-addon', function(e){
		        e.preventDefault();
		        $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
		    });
		},
		/**
		  * Function to load  Datepicker between ranges
		**/
		DateRangePickers:function(){
			/*------------- Date range Picker ----------------*/
		$('input[name="daterange"]').daterangepicker();
		    $('.dateTimerange').daterangepicker({
		        timePicker: true,
		        timePickerIncrement: 30,
		        locale: {
		            format: 'MM/DD/YYYY h:mm A'
		        }
		    });
		    var start = moment().subtract(29, 'days');
		    var end = moment();

		    function cb(start, end) {
		        $('.reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		    }

		    $('.reportrange').daterangepicker({
		        startDate: start,
		        endDate: end,
		        ranges: {
		           'Today': [moment(), moment()],
		           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		           'This Month': [moment().startOf('month'), moment().endOf('month')],
		           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		        }
		    }, cb);
		    cb(start, end);
		},
		/**
		  * Function to load  Clockface picker
		**/
		ClockFace:function(){
        $('.clockface-default').clockface();  
		    $('.clockface-toggle').clockface({
		        format: 'HH:mm',
		        trigger: 'manual'
		    });
		    $('#toggle-btn').on('click',function(e){   
		        e.stopPropagation();
		        $('.clockface-toggle').clockface('toggle');
		    }); 
		    $('.clockface-inline').clockface({
		        format: 'H:mm'
		    }).clockface('show', '14:30');
		},
		/**
		  * Function to load  Colorpicker
		**/
		ColorPicker:function(){
			/*-------------- Color Picker ---------------------*/
		    $('.colorpicker').each( function() {
		        $(this).minicolors({
		            control: $(this).attr('data-control') || 'hue',
		            defaultValue: $(this).attr('data-defaultValue') || '',
		            format: $(this).attr('data-format') || 'hex',
		            keywords: $(this).attr('data-keywords') || '',
		            inline: $(this).attr('data-inline') === 'true',
		            letterCase: $(this).attr('data-letterCase') || 'lowercase',
		            opacity: $(this).attr('data-opacity'),
		            position: $(this).attr('data-position') || 'bottom left',
		            swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
		            change: function(value, opacity) {
		                if( !value ) return;
		                if( opacity ) value += ', ' + opacity;
		                if( typeof console === 'object' ) {
		                    console.log(value);
		                }
		            },
		            theme: 'bootstrap'
		        });
		    });
		},
		Init:function(){
			if($('.FormSliders').length > 0){
				this.FormSliders();
			}
			if($('.datepicker').length > 0){
				this.DatePickers();
			}
			if($('.datetimepicker').length > 0){
				this.DateTimePickers();
			}
			if($('.timepicker-default').length > 0){
				this.TimePickers();
			}
			if($('.dateTimerange').length > 0){
				this.DateRangePickers();
			}
			if($('.clockface-default').length > 0){
				this.ClockFace();
			}
			if($('.colorpicker').length > 0){
				this.ColorPicker();
			}
		},
	};
	PrtmElement.Init();
})(jQuery);


$(function(){
	function initToolbarBootstrapBindings() {
	var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 
			'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
			'Times New Roman', 'Verdana'],
			fontTarget = $('[title=Font]').siblings('.dropdown-menu');
	$.each(fonts, function (idx, fontName) {
		 fontTarget.append($('<li><a data-edit="fontName ' + fontName +'" style="font-family:\''+ fontName +'\'">'+fontName + '</a></li>'));
	});
	$('a[title]').tooltip({container:'body'});
	$('.dropdown-menu input').click(function() {return false;})
		 .change(function () {$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');})
	  .keydown('esc', function () {this.value='';$(this).change();});
	
	$('[data-role=magic-overlay]').each(function () { 
	  var overlay = $(this), target = $(overlay.data('target')); 
	  overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
	});
	if ("onwebkitspeechchange"  in document.createElement("input")) {
	  var editorOffset = $('.prtm-editor').offset();
	  $('#voiceBtn').css('position','absolute').offset({top: editorOffset.top, left: editorOffset.left+$('.prtm-editor').innerWidth()-35});
	} else {
	  $('#voiceBtn').hide();
	}
	};
	function showErrorAlert (reason, detail) {
	var msg='';
	if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
	else {
		console.log("error uploading file", reason, detail);
	}
	$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+ 
	 '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
	};
	initToolbarBootstrapBindings(); 
	if($('.prtm-editor').length > 0){ 
		$('.prtm-editor').wysiwyg({ fileUploadError: showErrorAlert} );
	}
});
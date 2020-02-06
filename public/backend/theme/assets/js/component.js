'use strict';
(function($) {
	var PrtmComponent = {
		NotificationToaster:function(){
		    toastr.success('Page Loaded!');
		    toastr.options.fadeIn = 300,
		    toastr.options.fadeOut = 1000,
		    toastr.options.timeOut = 2000, // 1.5s
		    $('.toastrInfo').on('click',function() {
		    	toastr.info('info messages');
		    });
		    $('.toastrWarning').on('click',function() {
		    	toastr.warning('warning messages');
		    });
		    $('.toastrSuccess').on('click',function() {
		    	toastr.success('Success messages');
		    });
		    $('.toastrError').on('click',function() {
		    	toastr.error('Danger messages');
		    });

		    var i = -1;
		    var toastCount = 0;
		    var $toastlast;

		    var getMessage = function () {
		     var msgs = ['My name is Inigo Montoya. You killed my father. Prepare to die!',
		            '<div><input class="input-small form-control mrgn-b-sm" value="textbox"/>&nbsp;<a class="mrgn-b-sm display-ib" href="http://johnpapa.net" target="_blank">This is a hyperlink</a></div><div><button type="button" id="okBtn" class="btn btn-primary">Close me</button><button type="button" id="surpriseBtn" class="btn btn-inverse" style="margin: 0 8px 0 8px">Surprise me</button></div>',
		            'Are you the six fingered man?',
		            'Inconceivable!',
		            'I do not think that means what you think it means.',
		            'Have fun storming the castle!'
		        ];
		        i++;
		        if (i === msgs.length) {
		            i = 0;
		        }

		        return msgs[i];
		    };

		    var getMessageWithClearButton = function (msg) {
		        msg = msg ? msg : 'Clear itself?';
		        msg += '<br /><br /><button type="button" class="btn clear">Yes</button>';
		        return msg;
		    };

		    $('.showtoast').on('click',function () {
		        var shortCutFunction = $(".toastTypeGroup input:radio:checked").val();
		        var msg = $('.message').val();
		        var title = $('.title').val() || '';
		        var $showDuration = $('.showDuration');
		        var $hideDuration = $('.hideDuration');
		        var $timeOut = $('.timeOut');
		        var $extendedTimeOut = $('.extendedTimeOut');
		        var $showEasing = $('.showEasing');
		        var $hideEasing = $('.hideEasing');
		        var $showMethod = $('.showMethod');
		        var $hideMethod = $('.hideMethod');
		        var toastIndex = toastCount++;
		        var addClear = $('#addClear').prop('checked');

		        toastr.options = {
		            closeButton: $('.closeButton').prop('checked'),
		            debug: $('.debugInfo').prop('checked'),
		            newestOnTop: $('#newestOnTop').prop('checked'),
		            progressBar: $('#progressBar').prop('checked'),
		            positionClass: $('.positionGroup input:radio:checked').val() || 'toast-top-right',
		            preventDuplicates: $('#preventDuplicates').prop('checked'),
		            onclick: null
		        };

		        if ($('.addBehaviorOnToastClick').prop('checked')) {
		            toastr.options.onclick = function () {
		                alert('You can perform some custom action after a toast goes away');
		            };
		        }

		        if ($showDuration.val().length) {
		            toastr.options.showDuration = $showDuration.val();
		        }

		        if ($hideDuration.val().length) {
		            toastr.options.hideDuration = $hideDuration.val();
		        }

		        if ($timeOut.val().length) {
		            toastr.options.timeOut = addClear ? 0 : $timeOut.val();
		        }

		        if ($extendedTimeOut.val().length) {
		            toastr.options.extendedTimeOut = addClear ? 0 : $extendedTimeOut.val();
		        }

		        if ($showEasing.val().length) {
		            toastr.options.showEasing = $showEasing.val();
		        }

		        if ($hideEasing.val().length) {
		            toastr.options.hideEasing = $hideEasing.val();
		        }

		        if ($showMethod.val().length) {
		            toastr.options.showMethod = $showMethod.val();
		        }

		        if ($hideMethod.val().length) {
		            toastr.options.hideMethod = $hideMethod.val();
		        }

		        if (addClear) {
		            msg = getMessageWithClearButton(msg);
		            toastr.options.tapToDismiss = false;
		        }
		        if (!msg) {
		            msg = getMessage();
		        }

		        $('#toastrOptions').text('Command: toastr["'
		                + shortCutFunction
		                + '"]("'
		                + msg
		                + (title ? '", "' + title : '')
		                + '")\n\ntoastr.options = '
		                + JSON.stringify(toastr.options, null, 2)
		        );

		        var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
		        $toastlast = $toast;

		        if(typeof $toast === 'undefined'){
		            return;
		        }

		        if ($toast.find('#okBtn').length) {
		            $toast.delegate('#okBtn', 'click', function () {
		                alert('you clicked me. i was toast #' + toastIndex + '. goodbye!');
		                $toast.remove();
		            });
		        }
		        if ($toast.find('#surpriseBtn').length) {
		            $toast.delegate('#surpriseBtn', 'click', function () {
		                alert('Surprise! you clicked me. i was toast #' + toastIndex + '. You could perform an action here.');
		            });
		        }
		        if ($toast.find('.clear').length) {
		            $toast.delegate('.clear', 'click', function () {
		                toastr.clear($toast, { force: true });
		            });
		        }
		    });

		    function getLastToast(){
		        return $toastlast;
		    }
		    $('#clearlasttoast').on('click',function () {
		        toastr.clear(getLastToast());
		    });
		    $('.cleartoasts').on('click',function () {
		        toastr.clear();
		    });
		},
		SweetAlert:function(){
			/*------------------ Sweet Alerts ----------------*/
		    if($('.sweet-1').length > 0 ) {
		        document.querySelector('.sweet-1').onclick = function(){
		            swal({
		              title: "Are you sure?",
		              text: "this imaginary file!",
		              type: "info",
		              showCancelButton: true,
		              confirmButtonClass: 'btn-primary',
		              confirmButtonText: 'Primary!',
                      cancelButtonClass: 'btn-inverse'
		            });
		        };
		    }
		    if($('.sweet-2').length > 0 ) {
		        document.querySelector('.sweet-2').onclick = function(){
		            swal({
		                title: "Good job!",
		                text: "You clicked the button!",
		                type: "success",
		                showCancelButton: true,
		                confirmButtonClass: 'btn-success',
                        cancelButtonClass: 'btn-inverse',
		            });
		        };
		    }
		    if($('.sweet-3').length > 0 ) {
		        document.querySelector('.sweet-3').onclick = function(){
		            swal({
		              title: "Are you sure?",
		              text: "this imaginary file!",
		              type: "warning",
		              showCancelButton: true,
		              confirmButtonClass: 'btn-warning',
		              confirmButtonText: 'warning!',
                      cancelButtonClass: 'btn-inverse'
		            });
		        };
		    }
		    if($('.sweet-4').length > 0 ) {
		        document.querySelector('.sweet-4').onclick = function(){
		            swal({
		              title: "Are you sure?",
		              text: "this imaginary file!",
		              type: "info",
		              showCancelButton: true,
		              confirmButtonClass: 'btn-info',
		              confirmButtonText: 'info!',
                      cancelButtonClass: 'btn-inverse'
		            });
		        };
		    }
		    if($('.sweet-5').length > 0 ) {
		        document.querySelector('.sweet-5').onclick = function(){ 
		            swal({
		            title: "Are you sure?",
		            text: "Your will not be able to recover this imaginary file!",
		            type: "warning",
		            showCancelButton: true,
		            confirmButtonClass: "btn-warning",
		            confirmButtonText: "Yes, delete it!",
		            closeOnConfirm: false
		            },
		            function(){
		              swal("Deleted!", "Your imaginary file has been deleted.", "success");
		            });
		        };
		    }
		    if($('.sweet-6').length > 0 ) {
		        document.querySelector('.sweet-6').onclick = function(){
		            swal({
		                title: "An input!",
		                text: "Write something interesting:",
		                type: "input",
		                showCancelButton: true,
		                closeOnConfirm: false,
		                inputPlaceholder: "Write something",
                        cancelButtonClass: 'btn-inverse'
		                }, function (inputValue) {
		                if (inputValue === false) return false;
		                if (inputValue === "") {
		                swal.showInputError("You need to write something!");
		                return false
		                }
		                swal("Nice!", "You wrote: " + inputValue, "success");
		            });   
		        };
		    } 
		},
		MultiSelect:function(){
			/*------------- Bootstrap multiselect -------------*/
			if($('.selectstyle').length > 0) {
		    $('.selectstyle').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.inverse-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-inverse',
		    });
		    $('.primary-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-primary',
		    });
		    $('.success-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-success',
		    });
		    $('.warning-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-warning',
		    });
		    $('.danger-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-danger',
		    });
		    $('.info-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-info',
		    });
		    $('.inverse-outline-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.primary-outline-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-primary',
		    });
		    $('.success-outline-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-success',
		    });
		    $('.warning-outline-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-warning',
		    });
		    $('.danger-outline-select-box').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-danger',
		    });
		    $('.info-outline-select-box').multiselect({
		       buttonClass: 'btn btn-outline-info',
		       buttonWidth: '100%',
		    });
		    $('.inverse-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-inverse btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.primary-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-primary btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.success-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-success btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.warning-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-warning btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.danger-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-danger btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.info-rounded-select-box').multiselect({
		       buttonClass: 'btn btn-info btn-rounded',
		       buttonWidth: '100%', 
		    });
		    $('.filter-dropdown').multiselect({
		        enableClickableOptGroups: true,
		        enableCollapsibleOptGroups: true,
		        enableFiltering: true,
		        includeSelectAllOption: true,
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.select-group').multiselect({
		        enableClickableOptGroups: true,
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.clickable-filter').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		        enableFiltering: true, 
		    });
		    $('.dropright').multiselect({
		        dropRight: true,
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.fixedDropdown').multiselect({
		        buttonWidth: '80%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.fixedHight').multiselect({
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		        maxHeight: 200,
		    });
		    $('.onDropdownShow').multiselect({
		            onDropdownShow: function(event) {
		            alert('Dropdown shown.');
		        },
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		        maxHeight: 200,
		    });
		    $('.onDropdownHide').multiselect({
		            onDropdownHide: function(event) {
		            alert('Dropdown closed.');
		        },
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
		    $('.onchange').multiselect({
		        onChange: function(option, checked, select) {
		        alert('Changed option ' + $(option).val() + '.');
		        },
		        buttonWidth: '100%',
		        buttonClass: 'btn btn-outline-inverse',
		    });
			}
		},
		Init:function(){
			this.NotificationToaster();
			this.SweetAlert();
			this.MultiSelect();
		},
	};
	PrtmComponent.Init();
	if($('#fileupload').length > 0){
		$('#fileupload').fileupload({
			// Uncomment the following to send cross-domain cookies:
			//xhrFields: {withCredentials: true},
			url: 'server/php/'
		});
	}
})(jQuery);
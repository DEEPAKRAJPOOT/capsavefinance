/* global messages, message */
try {
    jQuery(document).ready(function ($) {


        $.validator.addMethod('lessThan', function (value, element, param) {
            var min = value.replace(/,/g, "");
            var max = $(param).val().replace(/,/g, "");

            var i = parseInt(min);
            var j = parseInt(max);
            return i <= j;
        }, "Please enter value equal or less then Remaining Anchor Limit");


        $.validator.addMethod('min_loan_size', function (value, element, param) {
            var min = value.replace(/,/g, "");
            var max = $(param).val().replace(/,/g, "");

            var i = parseInt(min);
            var j = parseInt(max);
            return i <= j;
        }, "Min loan size should not be greater than max loan size");


        $.validator.addMethod('lessThanEquals', function (value, element, param) {
            var min = value.replace(/,/g, "");
            var max = $(param).val().replace(/,/g, "");

            var i = parseInt(min);
            var j = parseInt(max);
            return i <= j;
        }, "Interest Rate Min  should not be greater than interest rate max");
        
         $.validator.addMethod('lessLoanSize', function (value, element, param) {
            var min = value.replace(/,/g, "");
            var max = $(param).val().replace(/,/g, "");

            var i = parseInt(min);
            var j = parseInt(max);
            return i <= j;
        }, "Max loan size should not be greater than Limit");

        /**
         * handle Industry Change evnet
         * 
         * @returns {undefined} mixed
         */
        $.fn.handleIndustryChange = function () {
            let selector = $(this);
            let currentValue = selector.val();
//            alert(currentValue);
            let subIndus = $('.sub_industry');
            let selected = null;
            $.ajax({
                url: messages.get_sub_industry,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: currentValue,
                    _token: messages.token
                },
                success: function (data) {
                    subIndus.removeClass('error');
                    subIndus.find('option').remove().end();
                    subIndus.append('<option value="">' + messages.please_select + '</option>');
                    $.each(data, function (index, data) {
                        var check = '';
                        if (data.id == selected) {
                            check = 'selected="selected"';
                        }
                        subIndus.append('<option  value="' + data.id + '" ' + check + ' >' + data.name + '</option>');
                    });
                },
                error: function () {
                    console.log('Error while getting city');
                }
            });
        };

        /**
         *  Handle change event
         */
//        $(document).on('change', '.industry_change', function () {
//            $(this).handleIndustryChange();
//        });
        $('.industry_change').on('change', function(){
            let country_id = $(this).val();
            $(this).handleIndustryChange(country_id);
        });


        $(document).on('click', '.submit', function (e) {
            e.preventDefault();
            let form = $('#addProgram');

            let validRules = {
                rules: {
                    product_id: {
                        required: true
                    },
                    prgm_name: {
                        required: true,
                        lettersonly: true
                    },
                    industry_id: {
                        required: true
                    },
                    sub_industry_id: {
                        required: false
                    },
                    anchor_limit: {
                        required: true,
                    },
                    is_fldg_applicable: {
                        required: true
                    },
                    anchor_id: {
                        required: true
                    },

                },
                messages: {

                }
            }

            form.validate(validRules);
            var valid = form.valid();
            if (valid) {
                form.submit();
            }

        });


        oTables = $('#program_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                url: messages.get_program_list,
                method: 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.anchor_id = messages.anchor_id;
                    d._token = messages.token;
                },
                error: function () { // error handling

                    $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'prgm_id'},
                {data: 'product_id'},

                {data: 'f_name'},
                {
                    data: 'prgm_name'
                },
                {
                    data: 'anchor_limit'
                },

                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ],
            aoColumnDefs: [{
                    'bSortable': false,
                    'aTargets': []
                }]

        });

        $('.multi-select-demo').multiselect({
            maxHeight: 400,
            enableFiltering: true,
            numberDisplayed: 2,
            selectAll: true,
        });



        $('[data-toggle="tooltip"]').tooltip();

        $(".trigger").click(function () {

            if ($(this).hasClass("minus")) {

                $(this).removeClass("minus");

            } else {
                $(this).addClass("minus");

            }

            $(this).parents("tr").next(".dpr").slideToggle();


        });




        $(".adhoc").change(function () {

            var v = $('input[name=' + $(this).attr('name') + ']' + ':checked').val();
            if (v == 1) {
                $("#facility1").show();

            } else {

                $("#facility1").hide();
            }

        });

        $(".adhoc").trigger('change');


        $(".int-checkbox").change(function () {

            var v = $('input[name=' + $(this).attr('name') + ']' + ':checked').val();
            if (v == 1) {
                $(".fixed").show();

            } else {

                $(".fixed").hide();
            }

        });

        $(".int-checkbox").change(function () {

            var v = $('input[name=' + $(this).attr('name') + ']' + ':checked').val();
            if (v == 2) {
                $(".floating").show();
                $(".fixed").show();

            } else {

                $(".floating").hide();
                //    $(".fixed").hide();
            }

        });


        $(".int-checkbox").trigger('change');

        $(".grace").change(function () {

            var v = $('input[name=' + $(this).attr('name') + ']' + ':checked').val();
            if (v == 1) {
                $("#facility2").show();

            } else {

                $("#facility2").hide();
            }

        });
        $(".grace").trigger('change');



        $(".charge-calculation1").change(function () {

            var vl = $(this).val();

            if (vl == 1) {

                $(".flat-amount").show();
            } else {

                $(".flat-amount").hide();
            }

            if (vl == 2) {

                $(".rate").show();
                $(".charge-time-type").prop("disabled", false);
            } else {

                $(".rate").hide();
                $(".charge-time-type").prop("disabled", true);
            }
            console.log(x);

        });



        $.fn.handleCharges = function () {
            let selector, currentValue, parentDiv, targetDiv, parentDivLen;
            selector = $(this);
            currentValue = selector.val();
            parentDiv = selector.parents('.charge_parent_div');
            parentDivLen = selector.data('rel');
            targetDiv = parentDiv.find('.html_append');
            targetDiv.empty();
            if (!currentValue > 0) {

                return false;
            }
            $.ajax({
                url: messages.get_charges_html,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: currentValue,
                    len: parentDivLen,
                    _token: messages.token
                },
                success: function (data) {
                    targetDiv.html(data.contents);
                    setTabIndex();
                },
                error: function () {

                }
            });

        };

        $(document).on('change', '.charges', function () {
            var selector = $(this);
            var currentValue = selector.val();
            var currentId = selector.attr('id');
            $.each($('.charges'), function (index, value) {
                let elm_value = $(value).val();
                let elm_id = $(value).attr('id');
                if (elm_id === currentId) {
                    return true;
                }
                if (elm_value === currentValue) {
                    customAlert('Alert!', 'This item is already Selected.');
                    selector.val('');
                    return false;
                }
            });
            $(this).handleCharges();
        });




        /**
         *  Handle add more event
         */
        $(document).on('click', '.add_more', function () {
            $(this).hide();
            let parent_div = $('.charge_parent_div');
            let num = parent_div.length + 1;

            let new_line = parent_div.first().clone();
            new_line.find('.add_more').show();
            new_line.find('select[name="charge[0]"]').attr({id: 'charge_' + num, name: 'charge[' + num + '] ', 'data-rel': num}).val('').removeClass('error')
            new_line.find("label[class='error']").remove();
            new_line.find('.delete_btn').show();
            new_line.find('.html_append').html('');
            parent_div.last().after(new_line);
            setTabIndex();
        });

        $.fn.showHideBtn = function () {
            let delete_btn, addMoreBtn;
            delete_btn = $('.delete_btn');
            addMoreBtn = $('.add_more');
            addMoreBtn.last().show();
            let chargesLen = $('.charges').length;
            if (chargesLen > 1) {
                delete_btn.last().show();
            }
        };

        /**
         * Handle delete 
         */
        $(document).on('click', '.delete_btn', function () {
            let selector = $(this);
            let parent_div = selector.parents('.charge_parent_div').remove();
            $(this).showHideBtn();
            setTabIndex();
        });


        $('.delete_btn').showHideBtn();

        var subProgramList = $('#sub_program_list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: false,
            bSort: true,
            ajax: {
                url: messages.get_sub_program_list,
                method: 'POST',
                data: function (d) {
                    d.by_email = $('input[name=by_email]').val();
                    d.is_assign = $('select[name=is_assign]').val();
                    d.anchor_id = messages.anchor_id;
                    d._token = messages.token;
                    d.program_id = messages.program_id;
                },
                error: function () { // error handling

                    $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#leadMaster_processing").css("display", "none");
                }
            },
            columns: [
                {data: 'prgm_id'},
                {data: 'f_name'},
                {
                    data: 'product_name'
                },
                {
                    data: 'prgm_type'
                },
                {
                    data: 'anchor_limit'
                },
                {
                    data: 'anchor_sub_limit'
                },
                {
                    data: 'loan_size'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ],
            aoColumnDefs: [{
                    'bSortable': false,
                    'aTargets': []
                }]

        });




        $(document).on('click', '.save_sub_program', function (e) {
            e.preventDefault();

            if (messages.invoiceDataCount == 'true')
            {
                customAlert('Alert!', 'This sub-program can not be update.');
                return false;
            }

            let form = $('#add_sub_program');
            var rules = {};
            var msg = {};
            form.removeData('validator');
            $("label[class='error']").remove();
            var maxloan = $("input[name='max_loan_size']").val().replace(/,/g, "");
            let validationRules = {
                rules: {
                    prgm_type: {
                        required: true
                    },
                    product_name: {
                        required: true,
                        lettersonly: true
                    },
                    anchor_sub_limit: {
                        required: true,
                        lessThan: "#anchor_limit",
                        // min: 1,
                        // number: true
                    },
                    min_loan_size: {
                        required: true,
                        // number: true,
                        ///  min: maxloan
                        min_loan_size: '.max_loan_size'

                    },
                    max_loan_size: {
                        required: true,
                        // number: true
                        lessLoanSize : 'input[name="anchor_sub_limit"]'
                    },
                    interest_rate: {
                        required: true
                    },
                    overdue_interest_rate: {
                        required: true,
                        number: true
                    },
                    interest_borne_by: {
                        required: true
                    },
                    margin: {
                        required: true,
                        number: true,
                        max: 100
                    },
                    is_adhoc_facility: {
                        required: true
                    },
                    adhoc_interest_rate: {
                        required: true,
                        number: true
                    },
                    grace_period: {
                        required: true,
                        number: true
                    },
                    disburse_method: {
                        required: true
                    },
                    'invoice_upload[]': {
                        required: true
                    },
                    'bulk_invoice_upload[]': {
                        required: true
                    },

                    'invoice_approval[]': {
                        required: true
                    },
                    'charge[1]': {
                        required: true
                    },
                    min_interest_rate: {
                        number: true,
                        max: 100,
                        lessThanEquals: 'input[name="max_interest_rate"]'
                    },
                    max_interest_rate: {
                        number: true,
                        max: 100
                    }, status: {
                        required: true
                    },
                    'doa_level[]': {
                        required: true
                    },
                    'pre_sanction[]': {
                        required: true
                    },
                    'post_sanction[]': {
                        required: true
                    }
                },
                messages: {

                }
            }

            $('.clsRequired ').each(function (index, value) {
                $(this).removeClass('error');
                rules[value.name] = {
                    required: true
                };
            });
            $('.valid_perc ').each(function (index, value) {
                $(this).removeClass('error');
                rules[value.name] = {
                    number: true,
                    max: 100
                };
            });


            var validRules = {
                rules: Object.assign(validationRules.rules, rules),
                messages: Object.assign(validationRules.messages, msg),
                ignore: ":hidden"
            };



            form.validate(validRules);
            var valid = form.valid();
            if (valid) {
                form.submit();
            }

        });



        $(document).on('input', '.number_format', function (event) {
            // skip for arrow keys
            if (event.which >= 37 && event.which <= 40)
                return;

            // format number
            $(this).val(function (index, value) {
                return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        });
        
         $(document).on('keyup', '.percentage', function () {
            var result = $(this).val();
            if (result == 0) {
                $(this).val('');
            }
            if (result >= 0 && result <= 100) {
                if (parseFloat(result)) {
                      if ($.inArray(".",result) !== -1) {
                        if (result.split(".")[1].length > 2) {
                            var array_conv = result.split(".")[1].substring(0,2);                           
                            var output = result.split(".")[0] + '.' + array_conv;
                            this.value = this.value.replace(result, output);
                        }
                    }
                }
            } else {
                this.value = this.value.replace(/\D/g, "").replace(result, result.substr(0, 2));
            }
        });

        //$('.number_format').trigger('blue');



        $(document).on('click', '.program_status', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: messages.token
                },
                success: function (data) {
                    if (data.success) {
                        oTables.draw();
                    }
                },
                error: function () {

                }
            });

        });



        function setTabIndex()
        {
            var n = 1;
            $('input.form-control,input.form-check-input, select.form-control').each(function () {

                $(this).attr('tabindex', n++);
            });

        }


        setTabIndex();

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
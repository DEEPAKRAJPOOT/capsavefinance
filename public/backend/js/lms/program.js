/* global messages, message */
try {
    jQuery(document).ready(function ($) {


        $.validator.addMethod('lessThan', function (value, element, param) {
            var i = parseInt(value);
            var j = parseInt($(param).val());
            return i <= j;
        }, "Please enter value equal or less then Anchor Limit");

        /**
         * handle Industry Change evnet
         * 
         * @returns {undefined} mixed
         */
        $.fn.handleIndustryChange = function () {
            let selector = $(this);
            let currentValue = selector.val();
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
        $(document).on('change', '.industry_change', function () {
            $(this).handleIndustryChange();
        });


        $(document).on('click', '.submit', function (e) {
            e.preventDefault();
            let form = $('#addProgram');

            let validRules = {
                rules: {
                    prgm_type: {
                        required: true
                    },
                    prgm_name: {
                        required: true
                    },
                    industry_id: {
                        required: true
                    },
                    sub_industry_id: {
                        required: true
                    },
                    anchor_limit: {
                        required: true
                    },
                    is_fldg_applicable: {
                        required: true
                    },
                    anchor_user_id: {
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
                {data: 'f_name'},
                {
                    data: 'prgm_name'
                },
                {
                    data: 'prgm_type'
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
            enableFiltering: true
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

            var v = $(this).val();
            if (v == "yes") {
                $("#facility1").show();

            } else {

                $("#facility1").hide();
            }

        });




        $(".int-checkbox").change(function () {

            var v = $(this).val();
            if (v == 1) {
                $(".fixed").show();

            } else {

                $(".fixed").hide();
            }

        });

        $(".int-checkbox").change(function () {

            var v = $(this).val();
            if (v == 2) {
                $(".floating").show();
                $(".fixed").show();

            } else {

                $(".floating").hide();
                //    $(".fixed").hide();
            }

        });




        $(".grace").change(function () {

            var v = $(this).val();
            if (v == "yes") {
                $("#facility2").show();

            } else {

                $("#facility2").hide();
            }

        });




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
            parentDivLen = $('.charge_parent_div').length;
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
            new_line.find('select[name="charge[1]"]').attr({id: 'charge_' + num, name: 'charge[' + num + '] '}).val('').removeClass('error')
            new_line.find("label[class='error']").remove();
            new_line.find('.delete_btn').show();
            new_line.find('.html_append').html('');
            parent_div.last().after(new_line);

        });

        $.fn.showHideBtn = function () {
            let delete_btn, addMoreBtn;
            delete_btn = $('.delete_btn');
            addMoreBtn = $('.add_more');
            addMoreBtn.last().show();
        };

        /**
         * Handle delete 
         */
        $(document).on('click', '.delete_btn', function () {
            let selector = $(this);
            let parent_div = selector.parents('.charge_parent_div').remove();
            $(this).showHideBtn();
        });



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
            let form = $('#add_sub_program');
            var rules = {};
            var msg = {};



            let validationRules = {
                rules: {
                    product_name: {
                        required: true
                    },
                    anchor_sub_limit: {
                        required: true,
                        lessThan: "#anchor_limit",
                        min: 1
                    },
                    min_loan_size: {
                        required: true
                    },
                    max_loan_size: {
                        required: true
                    },
                    interest_rate: {
                        required: true
                    },
                    overdue_interest_rate: {
                        required: true
                    },
                    interest_borne_by: {
                        required: true
                    },
                    margin: {
                        required: true
                    },
                    is_adhoc_facility: {
                        required: true
                    },
                    adhoc_interest_rate: {
                        required: true
                    },
                    grace_period: {
                        required: true
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





    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
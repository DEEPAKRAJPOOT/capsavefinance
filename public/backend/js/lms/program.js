/* global messages, message */
try {
    jQuery(document).ready(function ($) {

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
                    'aTargets': [7, 8]
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
            if (v == "fixed") {
                $(".fixed").show();

            } else {

                $(".fixed").hide();
            }

        });

        $(".int-checkbox").change(function () {

            var v = $(this).val();
            if (v == "floating") {
                $(".floating").show();

            } else {

                $(".floating").hide();
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




    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
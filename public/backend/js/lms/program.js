
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
            let  subIndus = $('.sub_industry');
            let selected = null;
            $.ajax({
                url: messages.get_sub_industry,
                type: 'POST',
                dataType: 'json',
                data: {id: currentValue, _token: messages.token},
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
                    prgm_detail: {
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

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

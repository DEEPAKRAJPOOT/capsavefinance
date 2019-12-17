
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
        
        
        $(document).on('click','.submit' , function (e) {
            e.preventDefault();
        });

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

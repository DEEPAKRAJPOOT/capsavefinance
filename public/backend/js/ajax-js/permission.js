
/* global messages, message */

try {
        jQuery(document).ready(function ($) { 
            
                $("input[type='checkbox']").change(function () {
                    $(this).siblings('ul')
                        .find("input[type='checkbox']")
                        .prop('checked', this.checked);
                });
            });
        



            // $('.nested input[type=checkbox]').click(function () {
            //     $(this).parent().find('li input[type=checkbox]').prop('checked', $(this).is(':checked'));
            //     var sibs = false;
            //     $(this).closest('ul').children('li').each(function () {
            //     if($('input[type=checkbox]', this).is(':checked')) sibs=true;
            //     })
            //     $(this).parents('ul').prev().prop('checked', sibs);
            //     });
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

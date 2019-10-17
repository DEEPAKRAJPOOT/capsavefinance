/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        
        /*validation for Login page */
        $("#compregisterForm").validate({
            rules: {
                first_name: {
                    required: true,
                    maxlength: 50

                },
                middle_name: {
                    required: true,
                    maxlength: 50

                },
                last_name: {
                    required: true,
                   maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 100

                },
                
               
                phone: {
                    minlength: 10,
                    maxlength: 10,
                    number: true
                },
                country_id: {
                    required: true,
                    number: true
                },
               
            },
            messages: {
                first_name: {
                    required: messages.req_first_name,
                    maxlength: messages.first_name_max_length
                },
                middle_name: {
                    required: messages.req_middle_name,
                    maxlength: messages.middle_name_max_length
                },
                last_name: {
                    required: messages.req_last_name,
                    maxlength: messages.last_name_max_length
                },
                email: {
                    required: messages.req_email,
                    email: messages.invalid_email,
                    maxlength: messages.email_max_length
                },
                
                 
                phone: {
                    minlength: messages.phone_minlength,
                    maxlength: messages.phone_maxlength,
                    number: messages.invalid_phone
                },
                country_id: {
                    required: messages.req_country,
                    number: messages.invalid_country
                },
                
                
               
            }
        });

        /*End*/

        //Preview profile pic
       

        //Function to show image before upload

        
        
        
        
        //allow only number....
        $(".numcls").keypress(function (evt) {
            var iKeyCode = (evt.which) ? evt.which : evt.keyCode
         if (iKeyCode = 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
             return false;

         return true;
       });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

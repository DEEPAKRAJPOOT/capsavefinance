/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#registerForm").validate({
            rules: {
                otp: {
                    required: true,
                    number: true
                    
                },
            },
            messages: {
                otp: {
                    required: messages.req_otp,
                    number: messages.invalid_otp
                    
                    
                },
            }
        });

        /*End*/
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Forgot password page */
        $("#backendForgotPassFrm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
            },
            messages: {
                email: {
                    required: messages.req_email,
                    email: messages.invalid_email,
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

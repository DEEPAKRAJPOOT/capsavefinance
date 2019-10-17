/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#backendFrmLogin").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                email: {
                    required: messages.req_email,
                    email: messages.invalid_email,
                },
                password: {
                    required: messages.req_password,
                }
            }
        });

        /*End*/        
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

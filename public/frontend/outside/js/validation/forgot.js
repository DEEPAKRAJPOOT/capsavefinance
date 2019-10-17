/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#resetForgotFm").validate({
            rules: {
                password: {
                    required: true
                },
                password_confirmation: {
                    required: true
                }
            },
            messages: {
                password: {
                    required: messages.req_password
                },
                password_confirmation: {
                    required: messages.req_confirm_password
                }
            }
        });

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

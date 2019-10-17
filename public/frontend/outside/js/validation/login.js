/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#frmLogin").validate({
            rules: {
                user_name: {
                    required: true
                    
                },
                password: {
                    required: true
                }
            },
            messages: {
                user_name: {
                    required: messages.req_user_name
                    
                },
                password: {
                    required: messages.req_password
                }
            }
        });

        /*End*/

        /**
         * Validation for forgot pasword
         */
        $("#forgotPassFrm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: messages.req_email,
                    email: messages.invalid_email
                }
            }
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

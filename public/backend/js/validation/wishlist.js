/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#frmWish").validate({
            rules: {
                type_id: {
                    required: true,
                },
                email: {
                    required: true,
                    email:true
                },
                phone: {
                    required: true,
                    number:true,
                    minlength:10,
                    maxlength:10
                },
                areas_of_interest: {
                    required: true
                }
            },
            messages: {
                type_id: {
                    required: "User Type is required",
                },
                email: {
                    required: "Email is required",
                    email: "Please enter valid Email"
                },
                phone: {
                    required: "Phone number is required",
                    number: "Please enter number only",
                    minlength: "Please enter valid phone number.",
                    maxlength: "You are not allow to enter maximum 10 digits."
                },
                areas_of_interest: {
                    required: "Area of Interest is required"
                }
            }
        });
        
        //allow only number....
        $(".numcls").keypress(function (evt) {
            var iKeyCode = (evt.which) ? evt.which : evt.keyCode
            if (iKeyCode = 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
                return false;

            return true;
        });

        /*End*/
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

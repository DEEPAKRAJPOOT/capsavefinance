/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        /*validation for Login page */
        $("#updateProfileForm").validate({
            rules: {
                first_name: {
                    required: true,
                    RegisterRegex: true,
                    maxlength: 50

                },
                last_name: {
                    required: true,
                    RegisterRegex: true,
                    maxlength: 50
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
                zip_code: {
                    required: true,
                },
               
            },
            messages: {
                first_name: {
                    required: messages.req_first_name,
                    RegisterRegex: messages.invalid_first_name,
                    maxlength: messages.first_name_max_length
                },
                last_name: {
                    required: messages.req_last_name,
                    RegisterRegex: messages.invalid_last_name,
                    maxlength: messages.last_name_max_length
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
                zip_code: {
                    required: 'ZipCode is Required',
                },
                
            }
        });

        /*End*/

        //Preview profile pic
        $("#user_photo").change(function () {
            readURL(this);
        });

        //Function to show image before upload

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#wizardPicturePreview11').attr('src', e.target.result).fadeIn('slow');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('#country_id').change(function(){
            if(this.value!=''){
                $('#country_id').removeClass('error');
                $('#country_id-error').remove();
            }
        })
        
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
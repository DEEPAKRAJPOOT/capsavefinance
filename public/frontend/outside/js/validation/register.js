/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        
        /*validation for Login page */
        $("#registerForm").validate({
            rules: {
                first_name: {
                    required: true,
                    lettersonly:true,
                    maxlength: 50

                },
                middle_name: {
                    maxlength: 50,
                    lettersonly:true,
                },
                last_name: {
                    required: true,
                    maxlength: 50,
                    lettersonly:true,
                },
                email: {
                    required: true,
                    pattern:/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
                    maxlength: 100

                },
                dob: {
                   validDOB : true, 
                   required :true,
                },
                phone: {
                    minlength: 10,
                    maxlength: 10,
                    phone_number : true,
                },
                country_id: {
                    required: true,
                    number: true
                },
               
            },
            messages: {
                first_name: {
                    required: messages.req_first_name,
                    lettersonly: messages.invalid_first_name,
                    maxlength: messages.first_name_max_length,
                },
                middle_name: {
                    maxlength: messages.middle_name_max_length,
                    lettersonly: messages.invalid_middle_name,
                },
                last_name: {
                    required: messages.req_last_name,
                    lettersonly: messages.invalid_last_name,
                    maxlength: messages.last_name_max_length
                },
                email: {
                    required: messages.req_email,
                    pattern: messages.invalid_email,
                    maxlength: messages.email_max_length
                },
                
                dob: {
                    validDOB: messages.invalid_age,
                    required: messages.req_dob,
                },
                 
                phone: {
                    minlength: messages.phone_minlength,
                    maxlength: messages.phone_maxlength,
                    number: messages.invalid_phone,
                    phone_number:messages.positive_phone_no,
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

        
        
        $.validator.addMethod(
        "validDOB",
        function(value, element){              
            var from = value.split("/"); // DD MM YYYY

            var year = from[2];
            var month = from[1];
            var day = from[0];
            var age = 18;

            var mydate = new Date();
            mydate.setFullYear(year, month-1, day);

            var currdate = new Date();
            var setDate = new Date();

            setDate.setFullYear(mydate.getFullYear() + age, month-1, day);

            if ((currdate - setDate) > 0){
                return true;
            }else{
                return false;
            }
        },
         messages.invalid_age
    );
        
        //allow only number....
        $(".numcls").keypress(function (evt) {
            var iKeyCode = (evt.which) ? evt.which : evt.keyCode
         if (iKeyCode = 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
             return false;

         return true;
       });


        //validate phone no
        $.validator.addMethod('phone_number',
        function (value) { 
                 return Number(value) > 0;
             }, messages.positive_phone_no

        );



        });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

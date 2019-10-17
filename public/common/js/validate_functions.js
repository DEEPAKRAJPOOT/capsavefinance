/* global messages */

try {
    jQuery(document).ready(function ($) {

        //Methods

        jQuery.validator.addMethod("CharacterOnly", function (value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
        });

        jQuery.validator.addMethod("NumberOnly", function (value, element) {
            return this.optional(element) || /^[0-9\-\s]+$/i.test(value);
        });

        $.validator.addMethod("EmailValidate", function (value, element) {
            //return this.optional(element) || /^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$/i.test(value);
            return this.optional(element) || /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value);
            
        });

        $.validator.addMethod("notEqualTo", function (value, element, param) {
            return this.optional(element) || value != $(param).val();
        });

        $.validator.addMethod("RegisterRegex", function (value, element) {
            return this.optional(element) || /^[a-z" "]+$/i.test(value);
        });

        /**
         * User name validation regex
         */

        $.validator.addMethod("UsernameRegex", function (value, element) {
            return this.optional(element) || /^[a-z0-9]+$/i.test(value);

        });


        $.validator.addMethod("UsernameNumRegex", function (value, element) {
            var totCount = value.replace(/[^0-9]/g, "").length;

            if (totCount > 7) {
                return false;
            } else {
                return true;
            }
        });
        //End

        //Password policy

        jQuery.validator.addMethod("pwcheck", function (value, element) {
            return /[\@\?\#\$\(\)\_\'\,\-\.\/\:\\\`]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[A-Z]/.test(value);
        }, "Your Password does not match our requirements");

        jQuery.validator.addMethod("cpwcheck", function (value, element) {
            if (/(.)\1\1\1\1\1\1\1\1/.test(value)) {
                return false;
            } else {
                return true;
            }

        }, "Your Password does not match our requirements");



        jQuery.validator.addMethod("loanAmt", function (value) {
            if (value !== '') {
                if (value >= 10000 && value <= 100000) {
                    return true;
                } else {
                    return false;
                }
            }
        }, 'Requested Loan Amount should be a minimum of $10,000.00 and maximum of $100,000.');

        //end

        /**
         * For format of phone numbers
         */
       $(".phonenumber").on('input', function () {
            formatPhone(this);
            if (!$(this).val()) {
                $(this).parent('.form-group').removeClass('datafor');
            } else {
                $(this).parent('.form-group').addClass('datafor');
            }
        });

        $(".rmdatafor").on('input', function () {
            if (!$(this).val()) {
                $(this).parent('.form-group').removeClass('datafor');
            } else {
                $(this).parent('.form-group').addClass('datafor');
            }
        });


        formatPhone = function (obj) {
            var numbers = obj.value.replace(/\D/g, ''),
                    char = {
                        0: '(',
                        3: ')',
                        6: '-'
                    };
            obj.value = '';
            for (var i = 0; i < numbers.length; i++) {
                obj.value += (char[i] || '') + numbers[i];
            }
        };
        $(".phonenumber").each(function (index) {
            formatPhone(this);
        });
        
        jQuery.validator.addMethod("phonenumber", function (value) {
            if (value) {
                return /^[\(][\d]{3}[\)][\d]{3}[\-][\d]{4}$/.test(value) && value != '(000)000-0000';
            } else {
                return true;
            }
        });
        //End      



    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
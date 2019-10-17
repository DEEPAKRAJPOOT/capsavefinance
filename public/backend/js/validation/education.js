/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        
        $.fn.hideAndshowBtn = () => {
            var defaults = {
                minLen: 5
            }
            var len = $('.clonedEducationInfo').length;
            if (defaults.minLen <= len) {
                $('.add-more-education').hide();
            } else {
                $('.add-more-education').show();
            }
        }
         $.fn.hideAndshowBtn();
        //Add more educxation

        $('.add-more-education').on('click', function () {
            var len = $('.clonedEducationInfo').length;
            var cloned = $('.clonedEducationInfo').first().clone(true);
            var lastRepeatingGroup = $('.clonedEducationInfo').last();
            cloned.attr('id', 'clonedEducationInfo' + len);

            cloned.find('input').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });
            cloned.find('select').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });

            cloned.find('textarea').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });
            cloned.find('.primary_id').remove();
            cloned.find('textarea').val('');
            cloned.find("input:text").val("");
            cloned.find('select').val('');
            cloned.find('.deleteEducationInfo').show();
            cloned.find('label').removeClass('error');
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');          
            $(this).hideAndshowBtn();
        });


        /**
         * Delete added Educations.
         */

        $(document).on('click', '.deleteEducationInfo', function () {

            $(this).parents('.clonedEducationInfo').remove();
            $(".clonedEducationInfo").each(function (index) {
                $(this).find("input").each(function () {
                    var lastChar = this.id.match(/\d+/);
                    this.name = this.name.replace('[' + lastChar + ']', '[' + index + ']');
                    this.id = this.id.replace(lastChar, index);
                });
                $(this).find("select").each(function () {
                    var lastChar = this.id.match(/\d+/);
                    this.name = this.name.replace('[' + lastChar + ']', '[' + index + ']');
                    this.id = this.id.replace('' + lastChar + '', '' + index + '');
                });
                $(this).find("textarea").each(function () {
                    var lastChar = this.id.match(/\d+/);
                    this.name = this.name.replace('[' + lastChar + ']', '[' + index + ']');
                    this.id = this.id.replace('' + lastChar + '', '' + index + '');
                });
               
               
              
               $(this).hideAndshowBtn();
               var len = $('.clonedEducationInfo').length;
               if(len == 1){
                   $('.deleteEducationInfo').css("display", "none");
               }
            });
        });

        /*End*/
        
        //form validation
        $('form#ProUpdateEducation').on('submit', function (event) {
            $('.input_university').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "University is required",
                            }
                        });
            });
            $('.input_course').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Course is required",
                            }
                        });
            });
            $('.input_from_year').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Dates Attended from year is required",
                            }
                        });
            });
            $('.input_to_year').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Dates Attended to year is required",
                            }
                        });
            });
            $('.input_remarks').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Remarks is required",
                            }
                        });
            });
        });
        $("#ProUpdateEducation").validate();

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

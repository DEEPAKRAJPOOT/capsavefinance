/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        $('#other_0').hide();
        //Validate form

        $('.skill > option:selected').each(function (e) {
            if ($(this).text() == 'Other') {
                $('input#other_' + e).show();
            } else {
                $('input#other_' + e).hide();
            }
        });


        //Add more Social Media Link

        $('.add-socialmedia').on('click', function () {
            var len = $('.clonedclonedSocialmedias').length;
            var cloned = $('.clonedclonedSocialmedias').first().clone(true);
            var lastRepeatingGroup = $('.clonedclonedSocialmedias').last();
            cloned.attr('id', 'clonedclonedSocialmedias' + len);

            cloned.find('input').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('' + lastChar + '', '' + len + '');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');

            });
            cloned.find('select').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');

                $("label[for='" + this.id + "']").remove();
            });

            cloned.find('input[type=text]').val('');
            cloned.find('input[type=text]').val('');
            cloned.find('select').val('');
            cloned.find('select').attr('data', len);
            cloned.find('.deleteSkillbtn').show();
            cloned.find('.error').next().remove();
            cloned.find('label').removeClass('error');
            cloned.find('select').removeClass("error");
            if (messages.is_scout == 1) {
                $(".clsRequired").addClass("required");
            }
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            var Add_len = Number(len + 1);
            if (Add_len == messages.social_media_form_limit) {
                $('.add-socialmedia').hide();
            } else {
                $('.add-socialmedia').show();
            }
        });


        /**
         * Delete added Social Media Link.
         */

        $(document).on('click', '.deleteSkill', function () {
            $(this).parents('.clonedclonedSocialmedias').remove();

            $(".clonedclonedSocialmedias").each(function (index) {
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
                var len = $('.clonedclonedSocialmedias').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.social_media_form_limit) {
                    $('.add-socialmedia').show();
                } else {
                    $('.add-socialmedia').hide();
                }
            });
        });




        //Add more Documents

        /* $('.add-Documents').on('click', function () {
         var len = $('.clonedDocuments').length;
         var cloned = $('.clonedDocuments').first().clone(true);
         var lastRepeatingGroup = $('.clonedDocuments').last();
         cloned.attr('id', 'clonedDocuments' + len);
         
         cloned.find('input').each(function (index) {
         var lastChar = this.id.match(/\d+/);
         this.name = this.name.replace('' + lastChar + '', '' + len + '');
         this.id = this.id.replace('' + lastChar + '', '' + len + '');
         
         });
         cloned.find('select').each(function (index) {
         var lastChar = this.id.match(/\d+/);
         this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
         this.id = this.id.replace('' + lastChar + '', '' + len + '');
         
         $("label[for='" + this.id + "']").remove();
         });
         
         cloned.find('input[type=text]').val('');
         cloned.find('input[type=text]').val('');
         cloned.find('select').val('');
         cloned.find('select').attr('data',len);
         cloned.find('.deleteDocumentbtn').show();
         cloned.find('.error').next().remove();               
         cloned.find('label').removeClass('error');
         cloned.find('select').removeClass("error");
         if(messages.is_scout == 1){
         $(".clsRequired").addClass("required"); 
         }
         cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
         var Add_len = Number(len + 1);
         if (Add_len == messages.document_form_limit) {
         $('.add-Documents').hide();
         } else {
         $('.add-Documents').show();
         }
         });*/

        var template = $('#childInfo .trainingperiod:first').clone(),
                trainingPeriodCount = 0;

        var addChildDetail = function () {
            trainingPeriodCount++;
            var Add_len = parseInt(trainingPeriodCount + 1);

            if (Add_len == messages.social_media_form_limit) {
                $('.add-child').hide();
            } else {
                $('.add-child').show();
            }
            var lastRepeatingGroup = $('.trainingperiod').last();
            var trainingPeriod = template.clone().find(':input').each(function () {
                var newId = this.id.substring(0, this.id.length - 1) + trainingPeriodCount;
                this.id = newId; // update id and name (assume the same)
            }).end() // back to .trainingperiod
                    .attr('id', 'TrainingPeriod' + trainingPeriodCount) // update attendee id
                    .insertAfter(lastRepeatingGroup); // add to container


            $('#TrainingPeriod' + trainingPeriodCount).find('input[id^="issuance_date"]').datepicker({ dateFormat: 'yy-mm-dd', maxDate: new Date(), changeMonth: true, changeYear: true });
            $('#TrainingPeriod' + trainingPeriodCount).find('input[id^="expiry_date"]').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });

            trainingPeriod.find('.deleteDocumentbtn').show();

        };


         $('.add-Documents').on('click',addChildDetail);

        $('input[id^="issuance_date"]').click(function () {
            inputName = $(this).attr('id');
            number = inputName.substr(inputName.length - 3); // get [number]
        }).datepicker({
            dateFormat: 'dd/mm/yy',
        });
        
        
        $('input[id^="expiry_date"]').click(function () {
            inputName = $(this).attr('id');
            number = inputName.substr(inputName.length - 3); // get [number]
        }).datepicker({
            dateFormat: 'dd/mm/yy',
        });

        /**
         * Delete added Documents.
         */
        
        

        $(document).on('click', '.deleteDocumentbtn', function () {
           /* $(this).parents('.clonedDocuments').remove();

            $(".clonedDocuments").each(function (index) {
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
                var len = $('.clonedDocuments').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.document_form_limit) {
                    $('.add-Documents').show();
                } else {
                    $('.add-Documents').hide();
                }
            });*/
            $(this).parents('.trainingperiod').remove();

            $(".trainingperiod").each(function (index) {
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
                var len = $('.trainingperiod').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.social_media_form_limit) {
                    $('.add-child').show();
                } else {
                    $('.add-child').hide();
                }
            });
        });




        /*End*/
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

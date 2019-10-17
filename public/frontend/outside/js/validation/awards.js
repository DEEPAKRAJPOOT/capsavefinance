/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        //Validation
        $('#awardForm').validate();
        $(".submitAwards").click(function () {
            /*if(messages.is_scout == 1){
            $(".clsRequired").addClass("required");
            }*/
            if(messages.is_scout == 0 || messages.is_scout == 1){
               $('.is_required').each(function (index) {
                var lastChar =  $(this).val();
               if(lastChar != ""){
                   $(".clsRequired").addClass("required"); 
               }
            }); 
            }
        });
  
        
            
      $('.is_required').on('keyup keypress blur change', function(e) {
            if(messages.is_scout == 0 || messages.is_scout == 1){
                $currentVal = $(this).val();
                if($currentVal != ''){
                    $(".clsRequired").addClass("required"); 
                   
                }else{
                    $(".clsRequired").removeClass("required");   
                    
                }
            }
        });
        //Add moe educxation

        $('.add-awards').on('click', function () {
            var len = $('.clonedAwards').length;
            var cloned = $('.clonedAwards').first().clone(true);
            var lastRepeatingGroup = $('.clonedAwards').last();
            cloned.attr('id', 'clonedAwards' + len);

            cloned.find('input').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });
            cloned.find('textarea').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });

            cloned.find('input[type=text]').val('');
            cloned.find('textarea').val('');
            cloned.find('select').val('');
            cloned.find('.deleteAwardsbtn').show();
            cloned.find('.error').next().remove();
            cloned.find('input').removeClass("error");
            cloned.find('label').removeClass('error');
            /*if(messages.is_scout == 1){
               $(".clsRequired").addClass("required");
            }*/
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            var Add_len = Number(len + 1);
            if (Add_len == messages.award_form_limit) {
                $('.add-awards').hide();
            } else {
                $('.add-awards').show();
            }
        });


        /**
         * Delete added products.
         */

        $(document).on('click', '.deleteAwards', function () {

            $(this).parents('.clonedAwards').remove();

            $(".clonedAwards").each(function (index) {
                $(this).find("input").each(function () {
                    var lastChar = this.id.match(/\d+/);
                    this.name = this.name.replace('[' + lastChar + ']', '[' + index + ']');
                    this.id = this.id.replace(lastChar, index);
                });
                $(this).find("textarea").each(function () {
                    var lastChar = this.id.match(/\d+/);
                    this.name = this.name.replace('[' + lastChar + ']', '[' + index + ']');
                    this.id = this.id.replace('' + lastChar + '', '' + index + '');
                });
                var len = $('.clonedAwards').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.award_form_limit) {
                    $('.add-awards').show();
                } else {
                    $('.add-awards').hide();
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

/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        $('#researchForm').validate();
        $(".submitResearch").click(function () {
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
        
        
        if(messages.is_scout == 0 || messages.is_scout == 1)
        {
            $('.submitResearch').val('Skip');
        }
        
      $('.is_required').on('keyup keypress blur change', function(e) {
          i=0; 
          if(messages.is_scout == 0 || messages.is_scout == 1){
                $currentVal = $(this).val();
                if($currentVal != ''){
                    $(".clsRequired").addClass("required"); 
                   
                }else{
                    $(".clsRequired").removeClass("required");   
                    
                }
                
                $('.is_required').each(function (index) {
                    var lastChar =  $(this).val();
                    if(lastChar != ""){
                        $('.submitResearch').val('Next');
                        i=1;
                    }
                });
                
              if(i == 0){
                $('.submitResearch').val('Skip');  
              } 
            }
        });
        
        //Add more research

        $('.add-reasearch').on('click', function () {
            var len = $('.clonedResearch').length;
            var cloned = $('.clonedResearch').first().clone(true);
            var lastRepeatingGroup = $('.clonedResearch').last();
            cloned.attr('id', 'clonedResearch' + len);

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
            cloned.find('.fileslabel').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
            });

            cloned.find('input[type=text]').val('');
            cloned.find('select').val('');
            cloned.find('.deleteResearchbtn').show();
            cloned.find('.error').next().remove();  
            cloned.find('input').removeClass("error");
            cloned.find('label').removeClass('error');
            cloned.find('.fileslabel').text('Attach File ( one attachment at a time )');
             if(messages.is_scout == 1){
                    $(".clsRequired").addClass("required"); 
                }
                cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            var Add_len = Number(len + 1);
            if (Add_len == messages.research_form_limit) {
                $('.add-reasearch').hide();
            } else {
                $('.add-reasearch').show();
            }
        });


        /**
         * Delete added products.
         */

        $(document).on('click', '.deleteResearch', function () {
            $('.submitResearch').val('Next');
            $(this).parents('.clonedResearch').remove();

            $(".clonedResearch").each(function (index) {
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
                var len = $('.clonedResearch').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.research_form_limit) {
                    $('.add-reasearch').show();
                } else {
                    $('.add-reasearch').hide();
                }
            });
        });
        
         //Preview profile pic
        $(".attach_photo").change(function (e) {
          $lasChar =  $(this).attr('id').slice(-1);
                var fileName = e. target. files[0]. name;
                 $('#picName'+$lasChar).text(fileName);
        });

        //Function to show image before upload

        

        /*End*/
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

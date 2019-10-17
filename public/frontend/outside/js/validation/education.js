/* global messages, message */

try {
    jQuery(document).ready(function ($) {  
       
        
         $('#updateProfileForm').validate();
        $(".mysubmit").click(function () {
            if(messages.is_scout == 1){
                 $(".clsRequired").addClass("required");  
            }
           
        });
        
        if(messages.is_scout == 0)
        {
            $('.submitEducation').val('Skip');
        }
        
      $('.is_required').on('keyup keypress blur change', function(e) {
          var i = 0;  
          if(messages.is_scout == 0){
                $currentVal = $(this).val();
                if($currentVal != ''){
                    $(".clsRequired").addClass("required"); 
                   
                }else{
                    $(".clsRequired").removeClass("required");   
                    
                }
                
                $('.is_required').each(function (index) {
                    var lastChar =  $(this).val();
                    if(lastChar != ""){
                        $('.submitEducation').val('Next');
                        i=1;
                    }
                });
                
              if(i == 0){
                $('.submitEducation').val('Skip');  
              }  
                
                
                
            }
        });
       
       $.validator.addMethod("greaterThan",
        function (value, element, param) {
          var $otherElement = $(param);
          return parseInt(value, 10) > parseInt($otherElement.val(), 10);
        },'From Date can not be greater than To Date');
    
        $(document).on('change','.select2Cls',function () {    
        var thisParents = $(this).parents('.education');
          $( "#"+thisParents.find('.toSelect').attr('id') ).rules( "add", {greaterThan: '#'+thisParents.find('.fromSelect').attr('id')});
        });

        $('#educationForm').validate();
        $(".submitEducation").click(function () {
            if(messages.is_scout == 1){
                 $(".clsRequired").addClass("required");  
            }
            if(messages.is_scout == 0){
               $('.is_required').each(function (index) {
                var lastChar =  $(this).val();
               if(lastChar != ""){
                   $(".clsRequired").addClass("required"); 
               }
            }); 
            }
           

             
        });
        //Add more education
        $('.add-more-educaion').on('click', function () {
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
                $("label[for='" + this.id + "']").remove();
            });

            cloned.find('textarea').each(function (index) {
                var lastChar = this.id.match(/\d+/);
                this.name = this.name.replace('[' + lastChar + ']', '[' + len + ']');
                this.id = this.id.replace('' + lastChar + '', '' + len + '');
                $("label[for='" + this.id + "']").remove();
            });

            cloned.find('input[type=text]').val('');
            cloned.find('textarea').val('');
            cloned.find('select').val('');
            cloned.find('.deleteEducationInfo').show();            
            cloned.find('.error').next().remove(); 
            cloned.find('label').removeClass('error');   
            cloned.find('input[type=text]').removeClass("error");   
            cloned.find('select').removeClass("error");   
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            if(messages.is_scout == 1){
            $(".clsRequired").addClass("required");   
            }
            var Add_len = Number(len + 1);
            if (Add_len == messages.education_form_limit) {
                $('.add-more-educaion').hide();
            } else {
                $('.add-more-educaion').show();
            }
        });


        /**
         * Delete added products.
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
                var len = $('.clonedEducationInfo').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.education_form_limit) {
                    $('.add-more-educaion').show();
                } else {
                    $('.add-more-educaion').hide();
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

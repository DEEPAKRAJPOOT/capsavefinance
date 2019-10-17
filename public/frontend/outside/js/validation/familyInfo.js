/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        if($('input[name="is_child"]:checked').length > 0){
            $(".is_child").hide();
        }else{
            $(".is_child").show();
        }
        
        $('input[name="is_child"]').on('click',function(){
            
            if($('input[name="is_child"]:checked').length > 0){
                $(".is_child").hide();
            }else{
                $(".is_child").show();
            }
        });
        
        //Add more Child

        $('.add-socialmedia').on('click', function () {
            var len = $('.clonedclonedSocialmedias').length;
            
            var cloned = $('.clonedclonedSocialmedias:first').clone(true);
            
            var lastRepeatingGroup = $('.clonedclonedSocialmedias').last();
            cloned.attr('id', 'clonedclonedSocialmedias' + len);
            
           // cloned.find("#child_dob"+len).removeClass('hasDatepicker').removeAttr('id').datepicker({dateFormat: 'yy-mm-dd'});

           
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
            
            //cloned.find('input.dobdatepicker').datepicker({dateFormat: 'yy-mm-dd'});
         
             
            
            cloned.find('input[type=text]').val('');
            cloned.find('input[type=text]').val('');
            cloned.find('select').val('');
            cloned.find('select').attr('data',len);
            cloned.find('.deleteSkillbtn').show();
            cloned.find('.error').next().remove();               
            cloned.find('label').removeClass('error');
            cloned.find('select').removeClass("error");
             cloned.find('#child_dob'+len).datepicker({dateFormat: 'yy-mm-dd'});
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            $('#clonedSocialmedias' + len).find('input[id^="child_dob"]').datepicker({dateFormat: 'yy-mm-dd'});
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
        
        

        /*End*/
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

$(function() {
    var inputName = '';
    var number = '';
    
    var template = $('#childInfo .trainingperiod:first').clone(),
        trainingPeriodCount = 0;

    var addChildDetail = function(){
        trainingPeriodCount++;
        var Add_len = parseInt(trainingPeriodCount + 1);
       
        if(Add_len == messages.social_media_form_limit) {
                $('.add-child').hide();
        } else {
                $('.add-child').show();
        }
        var lastRepeatingGroup = $('.trainingperiod').last();
        var trainingPeriod = template.clone().find(':input').each(function(){
            var newId = this.id.substring(0, this.id.length-1) + trainingPeriodCount;
            this.id = newId; // update id and name (assume the same)
        }).end() // back to .trainingperiod
        .attr('id', 'TrainingPeriod' + trainingPeriodCount) // update attendee id
        .insertAfter(lastRepeatingGroup); // add to container

        
        $('#TrainingPeriod' + trainingPeriodCount).find('input[id^="child_dob"]').datepicker({dateFormat: 'yy-mm-dd'});
        
        trainingPeriod.find('.deleteChildbtnbck').show(); 
        trainingPeriod.find('.lblname').value('Child'+trainingPeriodCount); 
        
       
        
    };
    

    $('.add-child').click(addChildDetail); // attach event

    $('input[id^="child_dob"]').click(function() {
        inputName = $(this).attr('id');
        number = inputName.substr(inputName.length - 3); // get [number]
    }).datepicker({
        dateFormat: 'dd/mm/yy',
        
    });
    
    
    $(document).on('click','.deleteFamily', function () {
        $(this).parents('.trainingperiod').remove();
        trainingPeriodCount--;
    });
    
     $(document).on('click', '.deleteFamily', function () {
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
    
    
});

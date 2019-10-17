/* global messages, message */

try {
    jQuery(document).ready(function ($) {

       
        //Add more Child

        $('.add-socialmedia').on('click', function () {
            var id  = $(this).attr('data');
           
            var len = $('#childInfo_'+id+' .clonedclonedSocialmedias-'+id).length;

            var cloned = $('#childInfo_'+id+' .clonedclonedSocialmedias-'+id+':first').clone(true);
            
            var lastRepeatingGroup = $('#childInfo_'+id+' .clonedclonedSocialmedias-'+id).last();
            cloned.attr('id', 'clonedclonedSocialmedias' + len);
            
            cloned.find('input').each(function (index){
               // var lastChar = this.id.match(/\d+/);
                //this.name = this.name;///this.name.replace('[' + lastChar + ']', '[' + len + ']');
                var strChar =this.id.split('_');
                this.name = strChar[0]+'_'+len;
                this.id = strChar[0]+'_'+len;//this.id = this.id.replace('' + lastChar + '', '' + len + ''); 
   
            });
            
            cloned.find('select').each(function (index){
                //var lastChar = this.id.match(/\d+/);
               // this.name = this.name;//this.name.replace('[' + lastChar + ']', '[' + len + ']');
                var strChar =this.id.split('_');
                this.name = strChar[0]+'_'+len;
                this.id = strChar[0]+'_'+len;//this.id = this.id.replace('' + lastChar + '', '' + len + '');
                
                $("label[for='" + this.id + "']").remove();
            });
            
            cloned.find('span.text-danger').each(function (index){
                //var lastChar = this.id.match(/\d+/);
               // this.name = this.name;//this.name.replace('[' + lastChar + ']', '[' + len + ']');
                var strChar =this.id.split('_');
                this.id = strChar[0]+'_'+len;//this.id = this.id.replace('' + lastChar + '', '' + len + '');
                
            });

            cloned.find('input[type=text]').val('');
            cloned.find('input[type=text]').val('');
            cloned.find('select').val('');
            cloned.find('select').attr('data',len);
            cloned.find('.deleteSkillbtn').show();
            cloned.find('.error').next().remove();               
            cloned.find('label').removeClass('error');
            cloned.find('select').removeClass("error");
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            
            var Add_len = Number(len + 1);
            if(Add_len == messages.social_media_form_limit){
                $('.add-socialmedia').attr('data-row',Add_len);
                $('input[name=rows'+id+']').val(Add_len)
                $('.add-socialmedia').hide();
            }else{
                $('.add-socialmedia').attr('data-row',Add_len);
                $('input[name=rows'+id+']').val(Add_len)
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
        

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}


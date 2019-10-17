/* global messages, message */
//date_attended_from_year
//date_attended_to_year
try {
    jQuery(document).ready(function ($) {

        $("#shareholderFormAjax").validate({
            rules: {   
               
            },
            messages: {
               

            },
            submitHandler: function (form) {

                var form = $('#shareholderFormAjax')[0];
                var formData = new FormData(form);
                $('.text-danger').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': messages.token
                    }
                });
                
                $.ajax({
                    url:messages.shareholder_save_ajax,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function (response) {
                        var result  =   JSON.parse(response);
                        //console.log(response);
                        if(result['status']=="error"){
                            //console.log(result['message'][0]);
                            //console.log(result['messagekey'][0]);
                           // $('#msgBlockError').removeClass('hide');
                           // $('#msgBlockSuccess').addClass('hide')
                           // var strError ='';
                          // strError=strError+'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            
        
                            
                           // strError    =   strError +'<ul>';
                            //for(var i=0;i<result['message'].length;i++){
                                //$("#"+result['messagekey'][i]).html(result['message'][i]);
                                //strError    =   strError+'<li>'+result['message'][i]+'</li>';
                            //}
                           // strError    =   strError+'</ul></div>';
                            //$('#msgBlockError').html(strError);
                            //(function(){ $('#msgBlockError').addClass('hide'); },10000);
                            var errorMsg    =   result['message'];
                            var errorKey    =   result['messagekey'];
                            for(var i=0;i< errorMsg.length;i++){
                                $("#"+errorKey[i]).html(errorMsg[i]);
                            }
                        }else{
                            
                            window.location.replace(result['redirect']);
                        }
                   
                    }
                });

                return false;
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
        
        /* Skill Block js*/
        
        
        $('#other_0').hide(); 
        //Validate form
        
       /* $('.skill > option:selected').each(function(e) {
            if($(this).text() == 'Other') {
                $('input#other_'+e).show();  
            } else {
                $('input#other_'+e).hide();  
            }
        });*/
       
        $("#skillFormAjax").validate({
            rules: {
                 'skill_id[]': {
                    required: true,
                },  
            },
            messages: {
                'skill_id[]': {
                    required: "The Skill is required",
                },
            },
            submitHandler: function (form) {                              
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': messages.token
                    }
                });
                
                $.ajax({
                    url:messages.skill_save_ajax,
                    type: "POST",
                    data: $('#skillFormAjax').serialize(),
                    success: function (response) {
                        var result  =   JSON.parse(response);
                        
                        if(result['status']=="error"){
                            //console.log(result['message'][0]);
                            $('#msgBlockError').removeClass('hide');
                            $('#msgBlockSuccess').addClass('hide')
                            var strError ='';
                           strError=strError+'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            
        
                            
                            strError    =   strError +'<ul>';
                            for(var i=0;i<result['message'].length;i++){
                                strError    =   strError+'<li>'+result['message'][i]+'</li>';
                            }
                            strError    =   strError+'</ul></div>';
                            $('#msgBlockError').html(strError);
                            setTimeout(function(){ $('#msgBlockError').addClass('hide'); },10000);
                        }else{
                            $(".btn-next").trigger('click');
                            //console.log(result['message']);
                            $('#msgBlockSuccess').removeClass('hide');
                            $('#msgBlockError').addClass('hide');
                            var strSuccess =''; 
                            strSuccess  =   strSuccess+'<div class="alert bg-success base-reverse alert-dismissible fade in" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>';
                            strSuccess  =   strSuccess+''+result['message'];
                            strSuccess  =   strSuccess+'</div>';
            
                            $('#msgBlockSuccess').html(strSuccess);
                            setTimeout(function(){ $('#msgBlockSuccess').addClass('hide'); },10000);
                        }
                   
                    }
                });

                return false;
            }
        });
        
        /* Show and Hide Other Skill option */
        $('.skill').change(function(){
           
            var th=$(this);
            var id =$(this).attr('data');
            
            if($("#skill_id"+id+" option:selected").text() == 'Other') {
                $('input#other_'+id).show(); 
                $('input#other_'+id).rules( "add",{
                    required: true,
                    messages: {
                        required: "Other Skill is required",
                    }
                });
            }else{
                $('input#other_'+id).rules("remove");
                $('input#other_'+id).hide();
                
            }
        });
        
        

        /* Add more skills*/

        $('.add-skills').on('click', function () {
            var len = $('.clonedSkills').length;
            var cloned = $('.clonedSkills').first().clone(true);
            var lastRepeatingGroup = $('.clonedSkills').last();
            cloned.attr('id', 'clonedSkills' + len);

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
            cloned.find('input[type=text]').hide();
            cloned.find('select').val('');
            cloned.find('select').attr('data',len);
            cloned.find('.deleteSkillbtn').show();
            cloned.find('.error').next().remove();               
            cloned.find('label').removeClass('error');
            cloned.find('select').removeClass("error");
             if(messages.is_scout == 1){
               $(".clsRequired").addClass("required"); 
             }
            cloned.insertAfter(lastRepeatingGroup).addClass('new_formTwo');
            var Add_len = Number(len + 1);
            if (Add_len == messages.skill_form_limit) {
                $('.add-skills').hide();
            } else {
                $('.add-skills').show();
            }
        });


        /**
         * Delete added skills.
         */

        $(document).on('click', '.deleteSkill', function () {
            $(this).parents('.clonedSkills').remove();

            $(".clonedSkills").each(function (index) {
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
                var len = $('.clonedSkills').length;

                var Add_len = Number(index + 1);
                if (Add_len < messages.skill_form_limit) {
                    $('.add-skills').show();
                } else {
                    $('.add-skills').hide();
                }
            });
        });

        /*End*/
        
        
      /* Research  & Publication */ 
      
         /* Validation and submit form*/
          $("#researchFormAjax").validate({
            rules: {   
               'title[]': {
                    required: true,
                },
                'journal_magazine[]': {
                    required: true,
                },
                'publication_month[]': {
                    required: true,
                },
                'publication_year[]': {
                    required: true,
                }
            },
            messages: {
                'title[]': {
                    required: "The Title is required",
                },
                'journal_magazine[]': {
                    required: "The Journal/Magazine is required",
                },
                'publication_month[]': {
                    required: "The Publication Month is required",
                },
                'publication_year[]': {
                    required: "The Publication Year is required",
                }

            },
            submitHandler: function (form) {                             
              
                var form = $('#researchFormAjax')[0];
                var formData = new FormData(form);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': messages.token
                    }
                });
                
                $.ajax({
                    url:messages.research_save_ajax,
                    type: "POST",
                    data: formData,//$('#researchFormAjax').serialize(),
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function (response) {
                        var result  =   JSON.parse(response);
                        
                        if(result['status']=="error"){
                            //console.log(result['message'][0]);
                            $('#msgBlockError').removeClass('hide');
                            $('#msgBlockSuccess').addClass('hide')
                            var strError ='';
                           strError=strError+'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            
        
                            
                            strError    =   strError +'<ul>';
                            for(var i=0;i<result['message'].length;i++){
                                strError    =   strError+'<li>'+result['message'][i]+'</li>';
                            }
                            strError    =   strError+'</ul></div>';
                            $('#msgBlockError').html(strError);
                            setTimeout(function(){ $('#msgBlockError').addClass('hide'); },10000);
                        }else{
                            $(".btn-next").trigger('click');
                            //console.log(result['message']);
                            $('#msgBlockSuccess').removeClass('hide');
                            $('#msgBlockError').addClass('hide');
                            var strSuccess =''; 
                            strSuccess  =   strSuccess+'<div class="alert bg-success base-reverse alert-dismissible fade in" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>';
                            strSuccess  =   strSuccess+''+result['message'];
                            strSuccess  =   strSuccess+'</div>';
            
                            $('#msgBlockSuccess').html(strSuccess);
                            setTimeout(function(){ $('#msgBlockSuccess').addClass('hide'); },10000);
                        }
                   
                    }
                });

                return false;
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

        /* Awards*/
        
         /* Validation and submit form*/
          /*$("#awardFormAajx").validate({
            rules: {   
               'title[]': {
                    required: true,
                },
                'description[]': {
                    required: true,
                }
            },
            messages: {
                'title[]': {
                    required: "The Title is required",
                },
                'description[]': {
                    required: "The Brief Description is required",
                }
            },
            submitHandler: function (form) {                             
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': messages.token
                    }
                });
                
                $.ajax({
                    url:messages.award_save_ajax,
                    type: "POST",
                    data: $('#awardFormAajx').serialize(),
                    success: function (response) {
                        var result  =   JSON.parse(response);
                        
                        if(result['status']=="error"){
                            //console.log(result['message'][0]);
                            $('#msgBlockError').removeClass('hide');
                            $('#msgBlockSuccess').addClass('hide')
                            var strError ='';
                           strError=strError+'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            
        
                            
                            strError    =   strError +'<ul>';
                            for(var i=0;i<result['message'].length;i++){
                                strError    =   strError+'<li>'+result['message'][i]+'</li>';
                            }
                            strError    =   strError+'</ul></div>';
                            $('#msgBlockError').html(strError);
                            setTimeout(function(){ $('#msgBlockError').addClass('hide'); },10000);
                        }else{
                            $(".btn-next").trigger('click');
                            //console.log(result['message']);
                            $('#msgBlockSuccess').removeClass('hide');
                            $('#msgBlockError').addClass('hide');
                            var strSuccess =''; 
                            strSuccess  =   strSuccess+'<div class="alert bg-success base-reverse alert-dismissible fade in" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>';
                            strSuccess  =   strSuccess+''+result['message'];
                            strSuccess  =   strSuccess+'</div>';
            
                            $('#msgBlockSuccess').html(strSuccess);
                            setTimeout(function(){ 
                                $('#msgBlockSuccess').addClass('hide');
                                window.loction.href=result['rediredUrl'];
                            },3000);
                        }
                   
                    }
                });

                return false;
            }
        });*/
        
        $("#awardFormAajx").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': messages.token
                    }
                });
                
                $.ajax({
                    url:messages.award_save_ajax,
                    type: "POST",
                    data: $('#awardFormAajx').serialize(),
                    success: function (response) {
                        var result  =   JSON.parse(response);
                        
                        if(result['status']=="error"){
                            //console.log(result['message'][0]);
                            $('#msgBlockError').removeClass('hide');
                            $('#msgBlockSuccess').addClass('hide')
                            var strError ='';
                           strError=strError+'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            
        
                            
                            strError    =   strError +'<ul>';
                            for(var i=0;i<result['message'].length;i++){
                                strError    =   strError+'<li>'+result['message'][i]+'</li>';
                            }
                            strError    =   strError+'</ul></div>';
                            $('#msgBlockError').html(strError);
                            setTimeout(function(){ $('#msgBlockError').addClass('hide'); },10000);
                        }else{
                            $(".btn-next").trigger('click');
                            //console.log(result['message']);
                            $('#msgBlockSuccess').removeClass('hide');
                            $('#msgBlockError').addClass('hide');
                            var strSuccess =''; 
                            strSuccess  =   strSuccess+'<div class="alert bg-success base-reverse alert-dismissible fade in" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>';
                            strSuccess  =   strSuccess+''+result['message'];
                            strSuccess  =   strSuccess+'</div>';
            
                            $('#msgBlockSuccess').html(strSuccess);
                            setTimeout(function(){ 
                                $('#msgBlockSuccess').addClass('hide');
                               window.location.href = result['rediredUrl'];
                            },3000);
                             
                        }
                   
                    }
                });

            
        });
        
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

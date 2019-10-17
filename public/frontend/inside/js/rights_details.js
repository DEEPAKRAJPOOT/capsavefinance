/* global messages, message */

try {
    jQuery(document).ready(function ($) {


      
        $(".mysubmit").click(function () {
             i = 0;  
             $('.mycheckbox').each(function (index) {
                    if($(this).prop("checked") == true){
                        i=1;
                    }
            });
            
            if(i==1){
               $(".clsRequired").removeClass("required");   
            }else{
                $(".form-errors").html("Any one Checkbox must be completed before you submit the form.");
               $(".clsRequired").addClass("required"); 
            }
          
        });
                $(".radiobtn").click(function(){
                    $('.mycheckbox').prop('checked', false);
            var radioValue = $("input[name='claim']:checked").val();
            if(radioValue == 2){
               $('.div1').css('display','none');
               $('.div2').css('display','block');
            }else{
                $('.div1').css('display','block');
               $('.div2').css('display','none');
            }

        });
        
        $(document).on('click','#all_of_these',function () {
               $(".form-errors").html("");
            var thisChecked = $(this).prop("checked");
            $('.otherChk').each( function () {
                $(this).prop('checked',thisChecked);
                
            });
          
        });
        
        $(document).on('click','.otherChk',function () {
            $(".form-errors").html("");
            var allChk = $('.otherChk').length; 
            var checkLen = $('.otherChk:checked').length; 
            if(allChk == checkLen ) {
                 $('#all_of_these').prop('checked',true);
             }else {
                 $('#all_of_these').prop('checked',false);
             }
            
        });
        
            $(document).on('click','.mycheckbox',function () {  
                 $(".form-errors").html("");
              });
            
        
          $('#validRightFrom').validate({ignore: ".ignore",
        showErrors: function(errorMap, errorList) {
        //$(".form-errors").html("Any one Checkbox must be completed before you submit the form.");
    }});

        //end popup

        $(document).on('change', '.button-browse :file', function () {
            var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

            input.trigger('fileselect', [numFiles, label, input]);
        });

        $('.button-browse :file').on('fileselect', function (event, numFiles, label, input) {
            var val = numFiles > 1 ? numFiles + ' files selected' : label;

            input.parent('.button-browse').next(':text').val(val);
        });
        $(".numcls").keypress(function (evt) {
            var iKeyCode = (evt.which) ? evt.which : evt.keyCode
            if (iKeyCode = 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
                return false;

            return true;
        });


        $('#comment_form').on('submit', function (event) {
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url: messages.save_comments,
                method: "post",
                data: form_data,
                dataType: "JSON",
                success: function (data) {
                    if (data.error != '') {
                        $('#comment_form')[0].reset();
                        //$('#comment_message').html(data.error);
                        loadComments();
                    }
                }
            });

        });

        loadComments();

        function loadComments() {
            var right_id = $('#right_id').val();
            $.ajax({
                url: messages.fetch_comments,
                method: "post",
                data: {right_id: right_id, _token: messages.csrf_token},
                success: function (data) {
                    $('#display_comment').html(data);
                    $('#comment_id').val('');
                    $('#my-loading').hide();
                }
            });
        }

        $(document).on('click', '.reply', function () {
            var comment_id = $(this).attr("id");
            $('#comment_id').val(comment_id);
            $('#right_comment').focus();
           
        });

    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

/* global messages, message */

try {
    jQuery(document).ready(function ($) {
        $('.doLikeUnlike').click(function () {
            var arr = $(this).attr('id').split('~');
            var right_id = arr[0];
            var right_user_id = arr[1];
            doLike(right_id, right_user_id, $(this));

        });
        $('.conn').click(function () {
            var toUserID = $('#from_user_id').val();
            $('.refres').css('display','block');
                    var to_user_id = toUserID;
                    var token = messages._token;
                    $.ajax({
                        url: messages.set_connection,
                        method: "POST",
                        dataType: "html",
                        data: {_token: token, to_user_id: to_user_id,},
                        success: function (data) {
                            $('.refres').css('display','none');
                            $('#connection_request').hide();
                            $('#removeconnection_request').show();
                            
                        }
                    });
        });
        
        
        
        
        $('.connupdate').click(function () {
            $('.refres').css('display','block');
            var connection_responce = $(this).val();
            var dataval = $(this).attr('data');
            if(dataval) {
                var connection_id = $('#connection_id_'+dataval).val();
            } else {
                var connection_id = $('#connection_id').val();
            }
            
            var token = messages._token;
            $.ajax({
                url: messages.set_accept_connection,
                method: "POST",
                dataType: "html",
                data: {_token: token, connection_id: connection_id,connection_responce: connection_responce,},
                success: function (data) {
                    $('.refres').css('display','none');
                    if(connection_responce == 2){ 
                        $('#connection_request_accept_'+dataval).hide();
                        $('#connection_request_decline_'+dataval).hide();
                        $('#removeconnection_request_'+dataval).show();
                        
                    }
                    if(connection_responce == 3){ 
                        $('#connection_request_accept_'+dataval).hide();
                        $('#connection_request_decline_'+dataval).hide();
                        $('#removeconnection_request_'+dataval).hide();
                    }
                    
                    if(connection_responce == 5){ 
                        $('#connection_request_accept_'+dataval).hide();
                        $('#connection_request_decline_'+dataval).hide();
                        $('#removeconnection_request_'+dataval).hide();
                    }
                }
            });
        });
        
      
        
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

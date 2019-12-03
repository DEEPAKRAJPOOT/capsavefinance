/* check pan card verify before upload pan*/


function uploadFile(uploadId, ownerId, docId)
{
    $('.isloader').show();
    var biz_id  = $('#biz_id').val();
    var app_id  = $('#app_id').val();
    if(docId == 2) { 
          var file  = $("#panfile"+uploadId)[0].files[0];
    }
    else if(docId == 31) { 
          var file  = $("#dlfile"+uploadId)[0].files[0];
    }
    else if(docId == 30) { 
         var file  = $("#voterfile"+uploadId)[0].files[0];
    }
    else if(docId == 32) { 
         var file  = $("#passportfile"+uploadId)[0].files[0];
    }
    else if(docId == 22) { 
        var file  = $("#photofile"+uploadId)[0].files[0];
    }
   
    var extension = file.name.split('.').pop().toLowerCase();
    var datafile = new FormData();
    
    datafile.append('_token', messages.token );
    datafile.append('owner_id', ownerId);
    datafile.append('biz_id', biz_id);
    datafile.append('app_id', app_id);
    datafile.append('doc_id', docId);
    datafile.append('doc_file', file);
    
    
    console.log(messages.promoter_document_save);
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.promoter_document_save,
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
        enctype: 'multipart/form-data',
 
        success: function(r){
           $(".isloader").hide();
           
           if(r.status==1)
           {
            if(docId == 2) { 
                  $("#pandown"+uploadId).css({'display':'inline'});
                  $("#pandown"+uploadId).attr('href',r.result.file_path);
            }
            else if(docId == 31) { 
                 $("#dldown"+uploadId).css({'display':'inline'});
                 $("#dldown"+uploadId).attr('href',r.result.file_path);
            }
            else if(docId == 30) { 
                 $("#voterdown"+uploadId).css({'display':'inline'});
                $("#voterdown"+uploadId).attr('href',r.result.file_path);
            }
            else if(docId == 32) { 
                 $("#passdown"+uploadId).css({'display':'inline'});
                $("#passdown"+uploadId).attr('href',r.result.file_path);
            }
            else if(docId == 22) { 
                 $("#photodown"+uploadId).css({'display':'inline'});
                $("#photodown"+uploadId).attr('href',r.result.file_path);
            }

             
           }
           else
           {
               
               alert('Something wrong! Please try again');
           }
          
            //obj = result.result.directors;
            //var count = 0;
//            alert(r);
        }
    });
}




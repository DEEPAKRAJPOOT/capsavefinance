/* check pan card verify before upload pan*/

function uploadFile(uploadId, ownerId, docId, docTypeName = null)
{
    $('.isloader').show();
    var id_number = '';
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
     else if(docId == 34) { 
        var file  = $("#aadharfile"+uploadId)[0].files[0];
    }
    else if(docId == 37) { 
        var file  = $("#electricityfile"+uploadId)[0].files[0];
    }
    else if(docId == 38) { 
        var file  = $("#telephonefile"+uploadId)[0].files[0];
    }
    else if(docId == 77) { 
        var file  = $("#ckycfile"+uploadId)[0].files[0];
        var id_number  = $("#ckycNumber"+uploadId).val();
        if(id_number!='') {
            var letterNumber = /^[0-9a-zA-Z]+$/;
            if((!id_number.match(letterNumber))) {
              alert('CKYC allow only Alphanumeric'); 
               $(".isloader").hide();
              return false;
            }
        }
    }

    var extension = file.name.split('.').pop().toLowerCase();
    var datafile = new FormData();
    
    datafile.append('_token', messages.token );
    datafile.append('owner_id', ownerId);
    datafile.append('doc_id', docId);
    datafile.append('doc_file', file);
    datafile.append('doc_id_no', id_number);
    datafile.append('doc_type_name', docTypeName);
    
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.ucic_promoter_document_save,
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
                $("#pandown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 31) { 
                $("#dldown"+uploadId).css({'display':'inline'});
                $("#dldown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 30) { 
                $("#voterdown"+uploadId).css({'display':'inline'});
                $("#voterdown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 32) { 
                $("#passdown"+uploadId).css({'display':'inline'});
                $("#passdown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 22) { 
                $("#photodown"+uploadId).css({'display':'inline'});
                $("#photodown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 34) { 
                $("#aadhardown"+uploadId).css({'display':'inline'});
                $("#aadhardown"+uploadId).attr('href',r.file_path);
            }
             else if(docId == 37) { 
                $("#electricitydown"+uploadId).css({'display':'inline'});
                $("#electricitydown"+uploadId).attr('href',r.file_path);
            }
             else if(docId == 38) { 
                $("#telephonedown"+uploadId).css({'display':'inline'});
                $("#telephonedown"+uploadId).attr('href',r.file_path);
            }             
           }
           else {               
               alert('Something wrong! Please try again');
           }
          
            location.reload();
        }
    });
}

// delete options for doc file
function deleteFile(uploadId, ownerId, file_id, docId, docTypeName = null)
{
    $('.isloader').show();
    var file_id;
   
    var datafile = new FormData();
    
    datafile.append('_token', messages.token );
    datafile.append('owner_id', ownerId);
    datafile.append('doc_id', docId);
    datafile.append('file_id', file_id);
    datafile.append('doc_type_name', docTypeName);
    
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.ucic_promoter_document_delete,
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
 
        success: function(r){
           $(".isloader").hide();
           
           if(r.status==1)
           {
            if(docId == 2) { 
                $("#pandown"+uploadId).css({'display':'inline'});
                $("#pandown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 31) { 
                $("#dldown"+uploadId).css({'display':'inline'});
                $("#dldown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 30) { 
                $("#voterdown"+uploadId).css({'display':'inline'});
                $("#voterdown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 32) { 
                $("#passdown"+uploadId).css({'display':'inline'});
                $("#passdown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 22) { 
                $("#photodown"+uploadId).css({'display':'inline'});
                $("#photodown"+uploadId).attr('href',r.file_path);
            }
            else if(docId == 34) { 
                $("#aadhardown"+uploadId).css({'display':'inline'});
                $("#aadhardown"+uploadId).attr('href',r.file_path);
            }
             else if(docId == 37) { 
                $("#electricitydown"+uploadId).css({'display':'inline'});
                $("#electricitydown"+uploadId).attr('href',r.file_path);
            }
             else if(docId == 38) { 
                $("#telephonedown"+uploadId).css({'display':'inline'});
                $("#telephonedown"+uploadId).attr('href',r.file_path);
            }             
           }
          
            location.reload();
        }
    });
}
/* check pan card verify before upload pan*/


function uploadFile(uploadId)
{
    var biz_id  = $('#biz_id').val();
    var app_id  = $('#app_id').val();
    var file  = $("#panfile"+uploadId)[0].files[0];
    var extension = file.name.split('.').pop().toLowerCase();
    var datafile = new FormData();
    
    datafile.append('_token', messages.token );
    datafile.append('biz_id', biz_id);
    datafile.append('app_id', app_id);
    datafile.append('doc_file', file);
    
    
    console.log(datafile);
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : '/application/promoter-document-save',
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
        enctype: 'multipart/form-data',
 
        success: function(r){
           $(".isloader").hide();
            //obj = result.result.directors;
            //var count = 0;
            alert(r);
        }
    });
}




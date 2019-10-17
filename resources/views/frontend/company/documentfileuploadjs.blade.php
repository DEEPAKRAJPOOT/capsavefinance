<script>

$.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
    var fileIdCounter = 0;
    //$("#files0")
    this.closest(".files").change(function (evt) {
        var output = [];
        
        for (var i = 0; i < evt.target.files.length; i++) {
            fileIdCounter++;
            var file = evt.target.files[i];
            //var fileId = sectionIdentifier + fileIdCounter;
            var fileId = sectionIdentifier;
            var lastChar = fileId[fileId.length -1];
            filesToUpload.push({
                id:$('#pics'+lastChar).attr('data-id'),
                file: Object.assign(file , { picsId : $('#pics'+lastChar).attr('data-id')})
            });
//console.log(file);
            var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

            output.push("<li><strong>", escape(file.name), "</strong> - ", removeLink, "</li> ");
        };

       // console.log(file);

        $(this).children(".fileList")
            .append(output.join(""));

        //reset the input to null - nice little chrome bug!
        evt.target.value = null;
    });

    $(this).on("click", ".removeFile", function (e) {
        e.preventDefault();

        var fileId = $(this).parent().children("a").data("fileid");

        // loop through the files array and check if the name of that file matches FileName
        // and get the index of the match
        for (var i = 0; i < filesToUpload.length; ++i) {
            if (filesToUpload[i].id === fileId)
                filesToUpload.splice(i, 1);
        }

        $(this).parent().remove();
    });

    this.clear = function () {
        for (var i = 0; i < filesToUpload.length; ++i) {
            if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                filesToUpload.splice(i, 1);
        }

        $(this).children(".fileList").empty();
    }

    return this;
};




(function () {
	
    var filesToUpload = [];
    var fileDataId = [];
     var filesToUploadArray = [];
    let gval = $("#gval").val();
   
    for (k = 0; k <=gval;  k++) {
  	var filesUploader = $("#files"+k).fileUploader(filesToUpload, "files"+k);
    //let filId = $("#pics"+k).attr('data-id');
    //fileDataId.push(filId);
}
   
   

   $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 
    
    $("#uploadBtn").click(function () {
                
        var baseurl="{{url('')}}";
    	
      //  e.preventDefault();

    $('#uploadBtn').attr("disabled", true);
        var formData = new FormData();
       
        for (var i = 0, len = filesToUpload.length; i < len; i++) {
       
            formData.append("files["+filesToUpload[i].id+"][]", filesToUpload[i].file);
            // formData.append("id[]", filesToUpload[i].id);
        }

    //ajax for corporate


        $.ajax({
            url: baseurl+'/profile/documents',
            data: formData,
            method: "POST",
             processData: false,
             contentType: false,
            success: function (data) {
                
            if(data=='success'){
               let message="Documents uploaded successfully !";
               $('#msg').text(message); 
               setTimeout(function(){location.reload()},5000); 
             } else {

                $('#msg').show(data);  
             }
               
            },
            error: function (data) {
                alert("ERROR - " + data.responseText);
            }
        });
    });

//ajax for indivisual

   $("#indivisualuploadBtn").click(function () {
                
          
           var baseurl="{{url('')}}";
        
      //  e.preventDefault();
      $('#indivisualuploadBtn').attr("disabled", true);
     

        
        var formData = new FormData();
       
        for (var i = 0, len = filesToUpload.length; i < len; i++) {
       
            formData.append("files["+filesToUpload[i].id+"][]", filesToUpload[i].file);
            // formData.append("id[]", filesToUpload[i].id);
        }

            


        $.ajax({
            url: globlvar.save_doc_url,
            
            data: formData,
            method: "POST",
             processData: false,
             contentType: false,
            success: function (data) {
                
            var result=JSON.parse(data); 
                console.log(result);
                $('#uploadBtn').attr("disabled",false);
                if(result['status']=="success"){
                 
                   window.location.replace(result['redirect']);
                }else{
                  window.location.replace(result['redirect']);
                }            
               
               
            },
            error: function (data) {
                alert("ERROR - " + data.responseText);
            }
        });
    });


})()

</script>
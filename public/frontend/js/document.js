/* 
 * documents working all kind of jquery
 * To change validation and form data
 */

    $(document).ready(function () {
        $('#documentForm').validate({ // initialize the plugin
            rules: {
                'doc_file[]': {
                    required: true,
                    extension: "jpg,png,pdf,doc,dox"
                }
            },
            messages: {
                'doc_file[]': {
                    required: "Please select file",
                    extension:"Please select jpg,png,pdf,doc,dox type format only.",
                }
            }
        });

        $('#documentForm').validate();

        $("#savedocument").click(function(){
            if($('#documentForm').valid()){
                $("#savedocument").disabled = true;
            }  
        });            

    });

    $('.getFileName').change(function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
    });
    
    $('.openModal').click(function(e) {
        var docId = $(this).attr('data-id');
        $('#myModal').modal('show');
        if(docId == 4) {
            $('input[name=docId]').val(docId);
            $('input[name=doc_id]').val(docId);
            $('select[name=doc_name]').parent('div').show();
            $('select[name=finc_year]').parent('div').hide();
            $('select[name=gst_month]').parent('div').hide();
            $('select[name=gst_year]').parent('div').hide();
        } else if (docId == 5) {
            $('input[name=docId]').val(docId);
            $('input[name=doc_id]').val(docId);
            $('select[name=doc_name]').parent('div').hide();
            $('select[name=finc_year]').parent('div').show();
            $('select[name=gst_month]').parent('div').hide();
            $('select[name=gst_year]').parent('div').hide();
            
        } else {
            $('input[name=docId]').val(docId);
            $('input[name=doc_id]').val(docId);
            $('select[name=doc_name]').parent('div').hide();
            $('select[name=finc_year]').parent('div').hide();
            $('select[name=gst_month]').parent('div').show();
            $('select[name=gst_year]').parent('div').show();
        }
        
    });
    $('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });
    
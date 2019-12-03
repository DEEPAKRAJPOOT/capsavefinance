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
        $('#password_file_div').hide();
        $('#is_not_for_gst').show();
        $('input[name=docId]').val(docId);
        $('input[name=doc_id]').val(docId);
        $('select[name=doc_name]').parent('div').hide();
        $('select[name=finc_year]').parent('div').hide();
        $('select[name=gst_month]').parent('div').hide();
        $('select[name=gst_year]').parent('div').hide();
        if(docId == 4) {
            $('select[name=doc_name]').parent('div').show();
        } else if (docId == 5) {
            $('select[name=finc_year]').parent('div').show();
        } else {
            $('#is_not_for_gst').hide();
            $('select[name=gst_month]').parent('div').show();
            $('select[name=gst_year]').parent('div').show();
        }
        
    });
    $('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });

    $(document).on('click','#pullgst_rep', function () {
        var gst_no = $(this).data('id');
        $('#modal_pullgst').modal('show');
    })

    $(document).on('click','input[name="is_password"]', function() {
        $('#password_file_div').hide();
       if ($(this).is(':checked') && $(this).val() == 'yes') {
        $('#password_file_div').show();
       }
    })
    
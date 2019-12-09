/* 
 * documents working all kind of jquery
 * To change validation and form data
 */
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');

    $(document).ready(function () {
        $('#documentForm').validate({ // initialize the plugin
            
            rules: {
                'doc_file[]': {
                    required: true,
                    extension: "jpg,png,pdf,doc,dox",
                    filesize : 200000000,
                },
                'file_bank_id' : {
                    required : true,
                },
                'finc_year' : {
                    required : true,
                },
                'gst_month' : {
                    required : true,
                },
                'gst_year' : {
                    required : true,
                },
                'pwd_txt' : {
                    required : function() {
                        if ($('input[name="is_pwd_protected"]').is(':checked') && $('input[name="is_pwd_protected"]:checked').val() == '1') {
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
            },
            messages: {
                'doc_file[]': {
                    required: "Please select file",
                    extension:"Please select jpg,png,pdf,doc,dox type format only.",
                    filesize:"maximum size for upload 20 MB.",
                },
                'file_bank_id': {
                    required: "Please select bank name.",
                },
                'finc_year': {
                    required: "Please select finance year.",
                },
                'gst_month': {
                    required: "Please select gst month.",
                },
                'gst_year': {
                    required: "Please select gst year.",
                }
            }
        });

        $('#documentForm').validate();

        $("#savedocument").click(function(){
            if($('#documentForm').valid()){
                $('form#documentForm').submit();
                $("#savedocument").attr("disabled","disabled");
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
        $('select[name=file_bank_id]').parent('div').hide();
        $('select[name=finc_year]').parent('div').hide();
        $('select[name=gst_month]').parent('div').hide();
        $('select[name=gst_year]').parent('div').hide();
        if (docId != 6 && $('input[name="is_pwd_protected"]').is(':checked') && $('input[name="is_pwd_protected"]:checked').val() == '1') {
            $('#password_file_div').show();
        }

        if(docId == 4) {
            $('select[name=file_bank_id]').parent('div').show();
        } else if (docId == 5) {
            $('select[name=finc_year]').parent('div').show();
        } else if (docId == 1 || docId == 11) {            
            $('#is_not_for_gst').hide();
            $('#is_required_addl_info').hide();       
        }        
        else {            
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

    $(document).on('click','input[name="is_pwd_protected"]', function() {
        $('#password_file_div').hide();
       if ($(this).is(':checked') && $(this).val() == '1') {
        $('#password_file_div').show();
       }
    })
    
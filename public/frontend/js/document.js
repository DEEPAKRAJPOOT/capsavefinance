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
                    extension: "jpg,png,pdf,doc,dox,xls,xlsx",
                    filesize : 200000000,
                },
                'file_bank_id' : {
                    required : true,
                },
                'facility' : {
                    required : true,
                },
                'sanctionlimitfixed' : {
                    required : function() {
                        if ($('input[name="facility"]').val() != 'NONE') {
                            return true;
                        }else{
                            return false;
                        }
                    }
                },
                'sanctionlimitvariableamount' : {
                    required : function() {
                        var facility_val = $('#facility').val();
                        if ((facility_val == 'CC' || facility_val == 'OD') && $('input[name="sanctionlimitfixed"]').is(':checked') && $('input[name="sanctionlimitfixed"]:checked').val() == '0') {
                            return true;
                        }else{
                            return false;
                        }
                    }
                },
                'drawingpowervariableamount' : {
                    required : function() {
                        if ($('#facility').val() == 'CC') {
                            return true;
                        }else{
                            return false;
                        }
                    }
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
                'bank_month' : {
                    required : true,
                },
                'bank_year' : {
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
                    extension:"Please select jpg,png,pdf,doc,dox,xls,xlsx type format only.",
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
    
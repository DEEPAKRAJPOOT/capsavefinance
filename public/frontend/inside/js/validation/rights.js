/* global messages, message */

try {
    jQuery(document).ready(function ($) {

        $('.select2Cls').select2();



        $("#rightsForm").validate({
            errorClass: 'error help-inline',
            rules: {
                title: {
                    required: true
                },
                type_id: {
                    required: true,
                    number: true
                },
                inventor: {
                    required: true
                },
                assignee: {
                    required: false
                },
                cluster_id: {
                    required: true,
                    number: true
                },
                date: {
                    required: true
                },
                description: {
                    required: true
                },
                exclusive: {
                    required: true
                },
                num_of_month_2: {
                    required: function (element) {
                        if ($("#num_of_month_2").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                },
                num_of_month_3: {
                    required: function (element) {
                        if ($("#num_of_month_3").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                },
                non_exclusive_purchase_price: {
                    required: function (element) {
                        if ($("#non_exclusive_price").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                },
                exclusive_purchase_price: {
                    required: function (element) {
                        if ($("exclusive_price").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                },
                is_exclusive_purchase:{
                    required: function() {
                        if ($("#non_exclusive").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                        
                    }
                },
                is_non_exclusive_purchase:{
                    required: function() {
                        if ($("#exclusive").is(':checked')) {
                            return false;
                        } else {
                            return true;
                        }
                        
                    }
                },
                
            },
            

            messages: {

                type_id: {

                    numeric: messages.req_right_type_numeric
                },

                cluster_id: {
                    numeric: messages.req_right_cluster_numeric

                },

            },
            errorPlacement: function (error, element) {


                if (element.attr("id") == "attachments0") {
                    $("#attachments0Error").html(error);
                } else {
                    var placement = $(element).data('error');
                    if (placement) {
                        $(placement).append(error)
                    } else {
                        error.insertAfter(element);
                    }
                }

            }
        });

        $(".my-radiobtn").click(function () {
            var d = $(this).is(':checked');
            var iid = $(this).attr('id');
            if (d) {
                if (iid == 'exclusive') {
                    $('#exp').show();
                } else {
                    $('#non_exp').show();
                }
            }else{
                if (iid == 'exclusive') {
                    $('#exp').hide();
                    $('#exclusive_price').val("");
                } else {
                    $('#non_exp').hide();
                    $('#non_exclusive_price').val("");
                    
                }
            }

        });

        $(".submitRights").click(function () {

            $('.classRequired').each(function (index) {
                $(this).rules("add", {
                    required: false
                });
            });
            $('.clsRequired').each(function (index) {
                $(this).rules("add", {
                    required: true
                });
            });


        });

        $('.save-right-info').click(function () {
            $("#rightsForm").validate();
            $("#rightsForm").append("<input type='hidden' name='draft' class='draft' id='draft' value='1'>");

            $('.clsRequired').each(function (index) {
                $(this).rules("add", {
                    required: false
                });
            });

            $('.classRequired').each(function (index) {
                $(this).rules("add", {
                    required: true
                });
            });

            $("#rightsForm").submit();
        });


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

        $('.add-slab').click(function () {
            var str_html = '';
            str_html = str_html + '<div class="row github-add">';
            str_html = str_html + '<div class="col-md-12"><div class="file-browse-add"><span class="button button-browse">Select File <input name="attachements[]" type="file" /></span><input type="text" name="org_attachment_name[]" class="form-control" placeholder="" readonly></div></div>';
            str_html = str_html + '<div class="remove"><i class="fa fa-trash" aria-hidden="true"></i></div></div></div>';
            $('.github-add:last').after(str_html);

            $('.button-browse :file').on('fileselect', function (event, numFiles, label, input) {
                var val = numFiles > 1 ? numFiles + ' files selected' : label;

                input.parent('.button-browse').next(':text').val(val);
            });

        });

        $('.github-outer').on('click', '.remove', function () {
            $(this).parent().remove();
        });

        $("input[name$='exclusive']").click(function () {
            var test = $(this).val();
            $("input[name=num_of_month_" + test + "]").val('')
            $("div.desc").hide();
            $("#Exclusive_" + test).show();
            $("#Exclusive_" + test).show();
        });
        $('.close-radio').click(function () {
            $("num_of_month_" + test).value('');
            $("div.desc").hide();
        });


        //allow only number....
        $(".numcls").keypress(function (evt) {
            var iKeyCode = (evt.which) ? evt.which : evt.keyCode
            if (iKeyCode = 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
                return false;

            return true;
        });

//        $("input[name$='subscription']").click(function () {
//            var test = $(this).val();
//
//            $("div.desc").hide();
//            $("#Exclusive" + test).show();
//        });
//        $('.close-radio').click(function () {
//            $("div.desc").hide();
//        });

        //Preview profile pic
        $("#Upload-Thumbnail").change(function () {
            readURL(this);
        });

        //Function to show image before upload

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#OpenImgUpload').attr('src', e.target.result).fadeIn('slow');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

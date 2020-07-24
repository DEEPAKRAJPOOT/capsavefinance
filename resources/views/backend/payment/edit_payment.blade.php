@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="paymentForm" style="width: 100%" method="POST" action="{{ Route('update_payment') }}" enctype="multipart/form-data" target="_top">
        @csrf
        <input type="hidden" name="payment_id" value="{{ $data->payment_id }}">
        <input type="hidden" name="biz_id" value="{{ $data->biz_id }}">
        <div class="col-md-4 tds_certificate">
            <div class="form-group">
                <label for="txtCreditPeriod">TDS Certificate No <span class="error_message_label">*</span></label>
                <input type="text" id="tds_certificate_no" name="tds_certificate_no" class="form-control" value="{{ $data->tds_certificate_no }}">
            </div>
        </div>
        <div class="col-md-4 tds_certificate">
            <div class="custom-file upload-btn-cls mb-3 mt-4">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file" multiple="">
                <label class="custom-file-label" for="customFile">Choose Certificate File</label>
            </div>
        </div>
        <div class="modal-body text-left">
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                      <label for="email">Comment <span class="error_message_label">*</span></label>
                      <textarea type="text" name="description" value="" class="form-control" tabindex="1" placeholder="Enter comment here ." >{{ $data->description }}</textarea>
                   </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success float-right btn-sm" id="savepayment" >Submit</button>  
        </div>
    </form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
	$.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');

    $(document).ready(function () {
        $('#paymentForm').validate({ // initialize the plugin
            
            rules: {
                'tds_certificate_no' : {
                    required : true,
                },
                'description' : {
                    required : true,
                }
            },
            messages: {
                'tds_certificate_no': {
                    required: "Please input TDS Certificate No.",
                },
                'description': {
                    required: "Please input comment.",
                }
            }
        });

        $('#paymentForm').validate();

        $("#savepayment").click(function(){
            if($('#paymentForm').valid()){
                $('form#paymentForm').submit();
                $("#savepayment").attr("disabled","disabled");
            }  
        });            

    });

    $(document).on('change', '.getFileName', function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
    })
    
    
    $(document).on('change', '.getFileName', function(e){
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });

</script>
@endsection
@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="paymentForm" style="width: 100%" method="POST" action="{{ Route('update_payment') }}" enctype="multipart/form-data" target="_top">
        @csrf
        <input type="hidden" name="payment_id" id="payment_id" value="{{ $data->payment_id }}">
        <input type="hidden" name="biz_id" value="{{ $data->biz_id }}">
        @if($data->action_type == 1 && $data->payment_type == 2)
        <div class="col-md-4 cheque_no mb-4">
            <div class="form-group">
                <label for="txtCreditPeriod">Cheque No<span class="error_message_label">*</span></label>
                <input type="text" id="cheque_no" name="cheque_no" class="form-control" value="{{ $data->cheque_no }}">
            </div>
        </div>
        <div class="col-md-4 form-group cheque_no">
            <div class="form-group">
                <label for="txtCreditPeriod">Upload cheque<span class="error_message_label">*</span></label>
                <div class="custom-file upload-btn-cls mt-0">
                    <input type="file" class="custom-file-input getFileName cheque" id="cheque" name="cheque" multiple="">
                    <label class="custom-file-label" for="customFile">Upload cheque</label>
                </div>
            </div>
        </div>
        @endif
        @if($data->action_type == 3)
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
        @endif
        
        @if($data->is_settled == 0)
        <div class="col-md-4">
            <div class="form-group">
                <label for="txtCreditPeriod">Transaction Date <span class="error_message_label">*</span></label>
                <input type="text" id="date_of_payment" name="date_of_payment" class="form-control datepicker-dis-fdate" value="{{ \Carbon\Carbon::parse($data->date_of_payment)->format('d/m/Y') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="txtCreditPeriod">Transaction Amount <span class="error_message_label">*</span></label>
                <input type="text" id="amount" name="amount" class="form-control formatCurrency" value="{{ $data->amount }}">
            </div>
        </div>
        @endif

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

    var messages = {
        unique_tds_certificate_no:"{{URL::route('check_unique_tds_certificate_no')}}",
        token: "{{ csrf_token() }}",
        id: "{{ isset($data->payment_id) ? 'yes'  : '' }}",
    };

	$.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');

    $.validator.addMethod("uniqueTdsCertificate",
        function(value, element, params) {
            var result = true;
            var data = {tds_certificate_no : value, _token: messages.token};
            if (params.payment_id) {
                data['payment_id'] = params.payment_id;
            }
            console.log(data);
            $.ajax({
                type:"POST",
                async: false,
                url: messages.unique_tds_certificate_no, // script to validate in server side
                data: data,
                success: function(data) {                        
                    result = (data.status == 1) ? false : true;
                }
            });                
            return result;                
        },'Please enter another TDS Certificate No.'
    );

    $(document).ready(function () {
        $('#paymentForm').validate({ // initialize the plugin
            
            rules: {
                'tds_certificate_no' : {
                    required : true,
                    uniqueTdsCertificate: {
                        payment_id: (messages.id != '') ? $("#payment_id").val() : null
                    }
                },
                'description' : {
                    required : true,
                },
                'cheque_no' : {
                    required: true,
                    minlength: 6,
                    maxlength: 6
                },
                'cheque': {
                    required: true,
                },
                'date_of_payment': {
                    required: true,
                },
                'amount': {
                    required: true,
                }
            },
            messages: {
                'tds_certificate_no': {
                    required: "Please input TDS Certificate No.",
                },
                'description': {
                    required: "Please input comment.",
                },
                'cheque_no' : {
                    required: "Please input Cheque No."
                },
                'cheque' : {
                    required: "Please upload the cheque"
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
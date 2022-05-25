@extends('layouts.backend.admin_popup_layout')
@section('content')
<form id="adhocForm" style="width: 100%" method="POST" action="{{ Route('save_adhoc_limit') }}" enctype="multipart/form-data" target="_top">
    <!-- Modal body -->
    @csrf
    <input type="hidden" name="prgm_offer_id" value="{{ request()->get('prgm_offer_id') }}">

    <div class="modal-body text-left">
        <div class="row bank_divs">
            <div class="col-6">
                <div class="form-group">
                    <label for="txtCreditPeriod"><b>Start Date </b><span class="error_message_label">*</span> </label>
                    <input type="text" id="start_date" name="start_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="txtCreditPeriod"><b>End Date </b><span class="error_message_label">*</span> </label>
                    <input type="text" id="end_date" name="end_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate" required="">
                </div>
            </div>
        </div>
        <div class="row bank_divs">
            <div class="col-6">
                <div class="form-group">
                    <label for="drawingpowervariableamount">Adhoc Interest Rate <span class="error_message_label">*</span></label>
                    <input type="text" class="form-control number_format" name="adhoc_interest_rate" id="adhoc_interest_rate" value="{{ $offer->adhoc_interest_rate }}" maxlength="15" placeholder="Adhoc Interest Rate" required=""  readonly="readonly">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="drawingpowervariableamount">Adhoc Limit <span class="error_message_label">*</span></label>
                    <input type="text" class="form-control number_format" name="adhoc_limit" id="adhoc_limit" value="" maxlength="15" placeholder="Enter Adhoc Limit" required="">
                </div>
            </div>
        </div>
        <div class="row bank_divs">
            <div class="col-6">
                <div class="form-group">
                    <label for="drawingpowervariableamount">Upload Document <span class="error_message_label">*</span></label>
                    <div class="custom-file">
                        <label for="email">Upload Document</label>
                        <input type="file" class="custom-file-input" id="customFile" name="doc_file" accept="image/jpeg, image/jpg, image/png, application/pdf">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                        <span id="msgFile" class="text-success"></span>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success float-right btn-sm" id="saveAdhocLimit" >Submit</button>  
    </div>
</form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
    
   $(document).ready(function(){
        var start_date = "{{ $data->start_date }}";
        var end_date = "{{ $data->end_date }}";
        $('#start_date').datetimepicker('setStartDate',  start_date);
        $('#start_date').datetimepicker('setEndDate',  end_date);
        $('#end_date').datetimepicker('setStartDate',  start_date);
        $('#end_date').datetimepicker('setEndDate',  end_date);

        $('#adhocForm').validate({ // initialize the plugin
            
            rules: {
                'start_date' : {
                    required : true,
                },
                'end_date' : {
                    required : true,
                },
                'adhoc_limit' : {
                    required : true,
                },
                'doc_file' : {
                    required : true,
                }
            },
            messages: {
                
                'start_date': {
                    required: "Please input start date.",
                },
                'end_date': {
                    required: "Please input end date.",
                },
                'adhoc_limit': {
                    required: "Please input limit amount.",
                },
                'doc_file': {
                    required: "Please upload document file.",
                }
            }
        });

        $('#adhocForm').validate();

        $("#saveAdhocLimit").click(function(){
            if($('#adhocForm').valid()){
                $('form#adhocForm').submit();
                $("#saveAdhocLimit").attr("disabled","disabled");
            }  
        });            

        $('input[type="file"]'). change(function(e){
            $("#customFile-error").hide();
            var fileName = e. target. files[0]. name;
            $("#msgFile").html('The file "' + fileName + '" has been selected.' );
        });
    });
</script>
@endsection
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
                    <label for="drawingpowervariableamount">Adhoc Limit <span class="error_message_label">*</span></label>
                    <input type="text" class="form-control number_format" name="adhoc_limit" id="adhoc_limit" value="" maxlength="15" placeholder="Enter Adhoc Limit" required="">
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
        $('#start_date').datetimepicker('setStartDate',  start_date);
        var end_date = "{{ $data->end_date }}";
        $('#end_date').datetimepicker('setStartDate',  end_date);

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

    });
</script>
@endsection
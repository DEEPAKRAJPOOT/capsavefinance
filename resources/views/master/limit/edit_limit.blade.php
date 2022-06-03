@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="addressForm" name="addressForm" method="POST" action="{{ route('save_borrowe_limit') }}" target="_top">
        @csrf

        <input type="hidden" class="form-control" id="id" name="id" maxlength="5" value="{{$limitData->limit_id}}">
        <div class="row">
        <div class="form-group col-6">
                <label for="single_limit">Single Borrower Limit</label>
                <input type="text" class="form-control" id="single_limit" name="single_limit" value="{{$limitData->single_limit}}" placeholder="Single Borrower Limit">
            </div>
            <div class="form-group col-6">
                <label for="multiple_limit">Multiple Borrower Limit<span class="mandatory">*</span></label>
                <input type="text" name="multiple_limit" id="multiple_limit" class="form-control" placeholder="Multiple Borrower Limit" value="{{$limitData->multiple_limit}}">
            </div>
        </div>
        <div class="row">
        <div class="form-group col-6">
                <label for="tax_from">Start Date <span class="mandatory">*</span></label>
                <input type="text" name="start_date" id="start_date" readonly="readonly" class="form-control" value="{{$limitData->start_date}}">
            </div>
            <div class="form-group col-6">
                <label for="address_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option disabled selected>Select</option>
                    <option value="1" {{$limitData->is_active == 1 ? 'selected' : ''}}>Active</option>
                    <option value="0" {{$limitData->is_active == 0 ? 'selected' : ''}}>In-Active</option>

                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group col-md-12 mb-0 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="add_gst" id="add_address" value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
    $(document).ready(function() {

         
        $('#addressForm').validate({ // initialize the plugin
            rules: {
                'single_limit': {
                    required: true,
                    number: true,
                },
                'multiple_limit': {
                    required: true,
                    number: true,
                },
                'start_date': {
                    required: true,
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'single_limit': {
                    required: "Please enter single borrower limit",
                },
                'multiple_limit': {
                    required: "Please enter multiple borrower limit",
                },
                'start_date': {
                    required: "Please select limit start date",
                },
                'is_active': {
                    required: "Please select limit Status",
                },
            }
        });
    });
</script>

<script>
       var lastLimitDate = "{{$limitData->start_date}}";
        if(lastLimitDate === ''){
            lastLimitDate = new Date();
        }
        $("#start_date").datetimepicker({
            format: 'dd/mm/yyyy',
            pickerPosition: 'bottom-right',
            autoclose: true,
            minView: 2,
            startDate: lastLimitDate
        });
        
        $("#end_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2,
            startDate: new Date()
        });

</script>

@endsection
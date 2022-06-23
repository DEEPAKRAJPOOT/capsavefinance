@extends('layouts.backend.admin_popup_layout')
@section('content')
@php
    $limitExpirationDate = date('Y-m-d', strtotime('+1 years -1 day',strtotime($data[0]->start_date)));
    $readInDays = config('lms.SHOW_EDIT_REVIEW_DATE_BUTTON_IN_DAYS').' days';
    $endDate = $data[0]->end_date;
    $editReviewDate = date('Y-m-d', strtotime('+'.$readInDays,strtotime($endDate)));
    $reviewDate = (isset($AppLimitReview) && $AppLimitReview->review_date)?date('d/m/Y', strtotime($AppLimitReview->review_date)):date('d/m/Y', strtotime($endDate));
    $commentTxt = (isset($AppLimitReview) && $AppLimitReview->comment_txt)?$AppLimitReview->comment_txt:'';
@endphp
<form id="reviewDateForm" method="POST" action="{{ Route('update_review_date') }}" enctype="multipart/form-data"
    target="_top">
    <!-- Modal body -->
    @csrf
    <input type="hidden" name="app_limit_id" value="{{ request()->get('app_limit_id') }}">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"><b>Review Date </b><span class="error_message_label">*</span> </label>
            <input type="text" id="review_date" name="review_date" readonly="readonly"
                class="form-control review_date datepicker-dis-fdate" required="" value="{{ $reviewDate }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-file upload-btn-cls mb-3 mt-2">
                <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="drawingpowervariableamount">Comment </label>
            <textarea name="comment_txt" id="comment_txt" value="" class="form-control" tabindex="1"
                placeholder="Add Comment">{{ $commentTxt }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-success float-right btn-sm" id="saveReviewDate">Submit</button>
</form>

@endsection

@section('jscript')
<script src="{{ asset('common/js/additional-methods.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var start_date = "{{ $editReviewDate }}";
        var end_date = "{{ ( isset($data[0]) && $data[0]->limit_expiration_date == '') ? $limitExpirationDate : $data[0]->limit_expiration_date }}";
        $('#review_date').datetimepicker('setStartDate', start_date);
        $('#review_date').datetimepicker('setEndDate', end_date);
        $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param) }, 'File size must be less than {0}' );
        $('#reviewDateForm').validate({ // initialize the plugin
            rules: {
                'review_date': {
                    required: true
                }, 
                'doc_file': {
                    required: false,
                    extension: "jpg,jpeg,png,pdf,doc,dox,xls,xlsx",
                    filesize : 200000000,
                }
            }
            , messages: {
                'review_date': {
                    required: "Please input review date."
                }, 
                'doc_file': {
                    extension:"Please select jpg,png,pdf,doc,dox,xls,xlsx type format only.",
                    filesize:"maximum size for upload 20 MB.",
                }
            }
        });

        $('#reviewDateForm').validate();

        $("#saveReviewDate").click(function() {
            if ($('#reviewDateForm').valid()) {
                $('form#reviewDateForm').submit();
                $("#saveReviewDate").attr("disabled", "disabled");
            }
        });

        $('.getFileName').change(function(){
            $(this).parent('div').children('.custom-file-label').html('Choose file');
        });
        
        $('.getFileName').change(function(e) {
            var fileName = e.target.files[0].name;
            $(this).parent('div').children('.custom-file-label').html(fileName);
        });

    });

</script>
@endsection
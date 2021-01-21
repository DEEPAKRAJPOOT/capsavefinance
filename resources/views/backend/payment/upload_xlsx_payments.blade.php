@extends('layouts.popup_layout')
@section('content')
<form id="documentForm" style="width: 100%" method="POST" action="{{ Route('import_excel_payments') }}" enctype="multipart/form-data" target="_top">
    @csrf
    <div class="modal-body text-left">
        <div class="custom-file upload-btn-cls mb-3 mt-2">
            <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file" >
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
    </div>
</form>
 
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp

@if($operation_status == config('common.YES'))
    <script>
    try {
        var p = window.parent;
        p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
        p.jQuery('#importNachExcelResponse').modal('hide');
        p.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }

    </script>
@endif
<script type="text/javascript">
    
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');

    $(document).ready(function () {
        $('#documentForm').validate({ // initialize the plugin
            
            rules: {
                'doc_file': {
                    required: true,
                    extension: "xls,xlsx",
                    filesize : 200000000,
                }
            },
            messages: {
                'doc_file': {
                    required: "Please select file",
                    extension:"Only xls is allowed.",
                    filesize:"maximum size for upload 20 MB.",
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
</script>
@endsection
@extends('layouts.backend.admin_popup_layout')

@section('content')
<div class="modal-body text-left">
    {!!
    Form::open(
    array(
    'route' => 'upload_sanction_letter',
    'name' => 'frm_upload_sanction_letter',
    'autocomplete' => 'off', 
    'id' => 'frmUploadSanctionLetter',
    'target' => '_top',
    'enctype'=>'multipart/form-data'
    )
    )
    !!}
    <div class="row">
        <div class="col-md-12">
            <div class="custom-file upload-btn-cls mb-3 mt-2">
                <label for="email">Upload Document</label>
                <input type="file" class="custom-file-input" id="customFile{{$docId}}" name="doc_file" multiple="">
                <label class="custom-file-label" for="customFile{{$docId}}">Choose file</label>
                <span class="fileUpload"></span>
            </div>
            <button type="submit" class="btn btn-primary float-right">Submit</button>  
        </div>
    </div>
    {!! Form::hidden('app_id', $appId) !!}
    {!! Form::hidden('biz_id', $bizId) !!}
    {!! Form::hidden('doc_id', $docId) !!}
    {!! Form::hidden('offer_id', $offerId) !!}
    
    {!!
    Form::close()
    !!}
</div>
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
    jQuery(document).ready(function ($) {    
        $('#frmUploadSanctionLetter').validate({
            rules: {
                doc_file: {
                   required: true,
                   accept: "application/pdf"
                }
            },
            messages: {
            }
        }); 
    });            
</script>
@endsection            

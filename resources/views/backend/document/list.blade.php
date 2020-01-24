@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
.upload-btn-wrapper input[type=file] {
    font-size: inherit;
    width: 63px;
    position: absolute;
    margin-left: 92px;
}
.setupload-btn > .error {
  position: absolute;
  top: -3px;
}
</style>
@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
<!-- partial -->
<div class="content-wrapper">
    <div class="card mt-4">
        <div class="card-body">

            <div class="form-heading pb-3 d-flex pr-0">
                <h2>Document
                    <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX )</small>
                </h2>
            </div>


            
                
        @foreach($requiredDocs as $product)
            <div class="card card-color mb-0">
                <div class="card-header" style="background: #398864;">
                    <a class="card-title ">
                        <b>{{ $product['productInfo']->product_name }}</b>
                    </a>

                </div>
            </div>
            <div class="row ">
                <div id="accordion" class="accordion d-table col-sm-12">
                @if($product['documents']->count() > 0)
                    @foreach($product['documents'] as $key=>$data)
                    <div class="card card-color mb-0">
                        <div class="card-header" data-toggle="collapse" href="#collapse{{ $data->app_doc_id }}">
                            <a class="card-title ">
                                <b>{{ $data->ppDocument->doc_name }}</b>
                            </a>

                        </div>
                        <div class="action-btn">
                            <div class="upload-btn-wrapper setupload-btn pos">
                                @if(request()->get('view_only'))
                                <button class="btn upload-btn openModal" data-id="{{ $data->doc_id }}">Upload</button>
                                @endif
                                
                            </div>
                        </div>
                        <div id="collapse{{ $data->app_doc_id }}" class="card-body collapse p-0 show" data-parent="#accordion">

                            <table class="table  overview-table" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                    <tr>
                                        @if($data->doc_id == '4')
                                        <td width="20%"><b>Bank</b></td>
                                        @endif
                                        @if($data->doc_id == '5')
                                        <td width="20%"><b>Finance Year</b></td>
                                        @endif
                                        @if($data->doc_id == '6')
                                        <td width="20%"><b>GST Month-Year</b></td>
                                        @endif
                                        <td width="20%"><b>File Name </b></td>
                                        <td width="20%"><b>Upload On </b></td>
                                        @if($data->doc_id == '35' || $data->doc_id == '36')
                                        <td width="20%"><b>Comment </b></td>
                                        @endif
                                        <td width="20%">Download</td>
                                        <td align="center" width="20%">Action</td>
                                    </tr>
                                    @foreach($documentData[$data->ppDocument->doc_name] as $value)
                                    <tr>
                                        @if($data->doc_id == '4')
                                        <td width="20%">{{ $value->doc_name }}</td>
                                        @endif
                                        @if($data->doc_id == '5')
                                        <td width="20%">{{ $value->finc_year }}</td>
                                        @endif
                                        @if($data->doc_id == '6')
                                        <td width="20%">{{ ($value->gst_month != '') ? date('M',$value->gst_month) : '' }}-{{ ($value->gst_year != '') ? $value->gst_year : '' }}</td>
                                        @endif
                                        <td width="20%"> {{ (isset($value->userFile->file_name)) ? $value->userFile->file_name : ''}} </td>
                                        <td width="20%"> {{ (isset($value->created_at)) ? date('d-m-Y', strtotime($value->created_at)) : ''}} </td>
                                        @if($data->doc_id == '35' || $data->doc_id == '36')
                                        <td width="20%"> {{ (isset($value->comment)) ? $value->comment : ''}} </td>
                                        @endif
                                        <td width="20%"><a title="Download Document" href="{{ Storage::url($value->userFile->file_path) }}" download="{{ $value->userFile->file_name }}"><i class="fa fa-download"></i></a></td>
                                        <td align="center" width="20%">
                                            @if(request()->get('view_only'))
                                            <a title="Delete Document" onclick="return confirm('Are you sure you want to delete this file?')" href="{{ Route('document_delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o error"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                    @endforeach
                @endif
                </div>
            </div>
            @endforeach
        </div>
        <a data-toggle="modal" data-target="#ppUploadDocument" data-url ="{{route('pp_upload_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openUploadDocument" style="display: none;"><i class="fa fa-plus"></i>Show Upload Document</a>
        <input type="hidden" name="uploadDocId" id="uploadDocId" value="" >
            
    </div>
    
</div>

{!!Helpers::makeIframePopup('ppUploadDocument','Upload Document', 'modal-md')!!}

@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
<script>
    var messages = {
        
    };
    
    $('.openModal').on('click', function(){
        $('#uploadDocId').val($(this).attr('data-id'));
        $('#openUploadDocument').trigger('click');
    });
</script> 
@endsection
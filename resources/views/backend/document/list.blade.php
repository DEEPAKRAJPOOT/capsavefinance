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
.card-title {
    font-size: 0.9rem;
    line-height: 1.375rem;
}
.tag {
    font-size: 0.8rem;
    line-height: 1.375rem;
    color: #28a745;
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
                    <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX,XLS,XLSX )</small>
                    <label> (Atleast one document must be uploaded.)</label>
                </h2>
            </div>
            @if($docFlag == 0)
                 <div class="card card-color mb-0">
                    <div class="card-header">
                        <a class="card-title ">
                            No pre-offer or pre/post stage document found.
                        </a>

                    </div>
                </div>
            @else 
            @foreach($requiredDocs as $product)
            <div class="card card-color mb-0">
                <div class="card-header" style="background: #398864;color: white;">
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
                                <b>{{ $data->ppDocument->doc_name }} </b>
                                <span class="tag"> ( {{ $docTypes[$data->ppDocument->doc_type_id] }} ) </span>
                            </a>

                        </div>
                        <div class="action-btn">
                            <div class="upload-btn-wrapper setupload-btn pos">
                                @if( (request()->get('view_only') && in_array($data->ppDocument->doc_type_id, [2,3])) || ($data->ppDocument->doc_type_id == 4) )
                                @can('pp_upload_document')
                                <button class="btn upload-btn openModal" data-id="{{ $data->doc_id }}">Upload</button>
                                @endcan
                                @endif
                                
                            </div>
                        </div>
                        <div id="collapse{{ $data->app_doc_id }}" class="card-body collapse p-0 show" data-parent="#accordion">
                            <table class="table  overview-table" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                    <tr>
                                        <td width="20%"><b>File Name </b></td>
                                        <td width="20%"><b>Upload On </b></td>
                                        <td width="20%"><b>Comment </b></td>
                                        <td width="20%">Download</td>
                                        <td align="center" width="20%">Action</td>
                                    </tr>

                                    @if (isset($documentData[$data->ppDocument->doc_name]))
                                    @foreach($documentData[$data->ppDocument->doc_name] as $value)
                                    <tr>
                                        <td width="20%"> {{ (isset($value->userFile->file_name)) ? $value->userFile->file_name : ''}} </td>
                                        <td width="20%"> {{ (isset($value->created_at)) ? date('d-m-Y', strtotime($value->created_at)) : ''}} </td>
                                        <td width="20%"> {{ (isset($value->comment)) ? $value->comment : ''}} </td>
                                        <td width="20%">
                                        @can('download_storage_file')
                                        <a title="Download Document" href="{{ route('download_storage_file', ['file_id' => $value->userFile->file_id ]) }}" ><i class="fa fa-download"></i></a>
                                        @endcan
                                        </td>
                                        <td align="center" width="20%">
                                        @can('view_upload_file')
                                        <a title="View Document" target="_blank" href="{{ route('view_prepost_documents', ['file_id' => $value->userFile->file_id ]) }}" class="float-left"><i class="fa fa-eye"></i></a>
                                        @endcan
                                            @if( (request()->get('view_only') && in_array($data->ppDocument->doc_type_id, [2,3])) || ($data->ppDocument->doc_type_id == 4) )
                                            @can('document_delete')
                                            <a title="Delete Document" onclick="return confirm('Are you sure you want to delete this file?')" href="{{ route('document_delete', ['app_doc_file_id' => $value->app_doc_file_id, 'app_id' => request()->get('app_id')]) }}" ><i class="fa fa-times-circle-o error"></i></a>
                                            @endcan
                                            @can('pp_edit_upload_document')
                                            <a title="Edit Comment" data-toggle="modal" data-target="#EdituploadDocument" data-url ="{{route('pp_edit_upload_document', ['app_doc_file_id' => $value->app_doc_file_id, 'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="float-right" ><i class="fa fa-edit"></i></a>
                                            @endcan
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>

                    @endforeach
                @else
                 <div class="card card-color mb-0">
                    <div class="card-header">
                        <a class="card-title ">
                            No Document Required.
                        </a>

                    </div>
                </div>
                @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
        @can('pp_upload_document')
        <a data-toggle="modal" data-target="#ppUploadDocument" data-url ="{{route('pp_upload_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openUploadDocument" style="display: none;"><i class="fa fa-plus"></i>Show Upload Document</a>
        @endcan
        <input type="hidden" name="uploadDocId" id="uploadDocId" value="" >
            
    </div>
    
</div>

{!!Helpers::makeIframePopup('ppUploadDocument','Upload Document', 'modal-md')!!}
{!!Helpers::makeIframePopup('EdituploadDocument','Edit Document', 'modal-md')!!}

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
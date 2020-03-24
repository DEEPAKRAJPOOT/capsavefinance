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
@if(is_null($edit))
@include('layouts.backend.partials.admin-subnav')
@endif
<!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        @can('company_details')
		<li>
			<a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" >Business Information</a>
		</li>
        @endcan 
        @can('promoter_details')
		<li>
			<a href="{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Management Information</a>
		</li>
        @endcan 
        @can('documents')
		<li>
			<a href="{{ route('documents', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Documents</a>
		</li>
        @endcan        
	</ul>
    <div class="card mt-4">
        <div class="card-body">

            <div class="form-heading pb-3 d-flex pr-0">
                <h2>Document
                    <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX,XLS,XLSX )</small>
                </h2>
            </div>


            <div class="row ">
                @if($requiredDocs->count() > 0)
                <div id="accordion" class="accordion d-table col-sm-12">
                    @foreach($requiredDocs as $key=>$data)
                    <div class="card card-color mb-0">
                        <div class="card-header" data-toggle="collapse" href="#collapse{{ $data->app_doc_id }}">
                            <a class="card-title ">
                                <b>{{ $data->document->doc_name }}</b>
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

                            <table class="table  overview-table" id="documentTable" cellpadding="0" cellspacing="0" border="1">
                                <tbody>
                                    <tr>
                                        @if($data->doc_id == '4')
                                        <td width="20%"><b>Bank</b></td>
                                        <td width="20%"><b>Month-Year</b></td>
                                        @endif
                                        @if($data->doc_id == '5')
                                        <td width="20%"><b>Finance Year</b></td>
                                        @endif
                                        @if($data->doc_id == '6')
                                        <td width="20%"><b>GST Month-Year</b></td>
                                        @endif
                                        <td width="20%"><b>File Name </b></td>
                                        
                                        <td width="20%"><b>Uploaded On </b></td>

                                        @if($data->doc_id == '35' || $data->doc_id == '36')
                                        <td width="20%"><b>Comment </b></td>
                                        @endif
                                        <td width="20%">Download</td>
                                        <td align="center" width="20%">Action</td>
                                    </tr>
                                    @foreach($documentData[$data->document->doc_name] as $value)
                                    <tr>
                                        @if($data->doc_id == '4')
                                        <td width="20%">{{ $value->doc_name }}</td>
                                        <td width="20%">{{ sprintf('%02d', $value->gst_month) . '-'. $value->gst_year}}</td>
                                        @endif
                                        @if($data->doc_id == '5')
                                        <td width="20%">{{ $value->finc_year }}</td>
                                        @endif
                                        @if($data->doc_id == '6')
                                        <td width="20%" name="dateRow">{{ ($value->gst_month != '') ? date('M',$value->gst_month) : '' }}-{{ ($value->gst_year != '') ? $value->gst_year : '' }}</td>
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

                    <div class="d-flex btn-section ">
                        <div class="col-md-4 ml-auto text-right">
                            <form method="POST" action="{{ Route('application_save') }}">
                                @csrf
                                <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
                                <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">                                    
                                <!--<input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'promoter-details'">-->
                                @if(request()->get('view_only'))
                                <input type="submit" value="Submit" class="btn btn-success btn-sm">
                                @endif
                            </form>
                        </div>
                    </div>

                </div>
                @endif
                <a data-toggle="modal" data-target="#uploadDocument" data-url ="{{route('show_upload_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openUploadDocument" style="display: none;"><i class="fa fa-plus"></i>Show Upload Document</a>
                <input type="hidden" name="uploadDocId" id="uploadDocId" value="" >
            </div>
        </div>
    </div>
    
</div>

{!!Helpers::makeIframePopup('uploadDocument','Upload Document', 'modal-md')!!}

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
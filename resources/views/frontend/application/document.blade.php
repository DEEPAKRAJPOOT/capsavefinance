@extends('layouts.guest')
@section('content')


<div class="step-form pt-5">

    <div class="container">
        <ul id="progressbar">
            <li class="active">
                <div class="count-heading">Business Information </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('frontend/assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('frontend/assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="active">
                <div class="count-heading">Management Details </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('frontend/assets/images/kyc.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('frontend/assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="count-active">
                <div class="count-heading">Documents</div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('frontend/assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('frontend/assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="container">
        <div class="mt-4 ">
            <div class="form-design">
                <div class="card-body">
            <div class="col-md-12">

                @if(session()->has('message'))
                <p class="alert alert-info">{{ Session::get('message') }}</p>
                @endif

                @foreach($errors->all() as $error)
                <span class="text-danger error">{{ $error }}</span>
                @endforeach
                <div class="form-heading pb-3 d-flex pr-0">
                    <h2>Document
                        <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX )</small>
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
                                    <button class="btn upload-btn openModal"  data-id="{{ $data->doc_id }}">Upload</button>
                                    <!--<input type="file" name="myfile">-->
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
                                            <td width="20%"><b>GST Month - Year</b></td>
                                            @endif
                                            <td width="20%"><b>Upload On </b></td>
                                            <td width="20%">Download</td>
                                            <td align="center" width="20%">Action</td>
                                        </tr>
                                        @foreach($documentData[$data->document->doc_name] as $value)
                                        <tr>
                                            @if($data->doc_id == '4')
                                            <td width="20%">{{ $value->doc_name }}</td>
                                            @endif
                                            @if($data->doc_id == '5')
                                            <td width="20%">{{ $value->finc_year }}</td>
                                            @endif
                                            @if($data->doc_id == '6')
                                            <td width="20%">{{ date('M', $value->gst_month) }} - {{ $value->gst_year }}</td>
                                            @endif
                                            <td width="20%"> {{ date('d-m-Y', strtotime($value->created_at))}} </td>
                                            <td width="20%"><a title="Download Document"  href="{{ Storage::url($value->userFile->file_path) }}" download><i class="fa fa-download"></i></a></td>
                                            <td align="center" width="20%">
                                                <a title="Delete Document" href="{{ Route('document-delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o error"></i></a>
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
                                <form method="POST" action="{{ Route('front_application_save') }}">
                                    @csrf
                                    <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
                                    <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">                                    
                                    <!--<input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'promoter-details'">-->
                                    <input type="submit" value="Submit" class="btn btn-success btn-sm">
                                </form>
                            </div>
                        </div>

                    </div>
                    @endif
                     <a data-toggle="modal" data-target="#uploadDocument" data-url ="{{route('front_show_upload_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openUploadDocument" style="display: none;"><i class="fa fa-plus"></i>Show Upload Document</a>
                    <input type="hidden" name="uploadDocId" id="uploadDocId" value="" >
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{!!Helpers::makeIframePopup('uploadDocument','Upload Document', 'modal-md')!!}
    
@endsection

@section('scripts')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js?v=1') }}"></script>
<script>
    var messages = {
        
    };
    
    $('.openModal').on('click', function(){
        $('#uploadDocId').val($(this).attr('data-id'));
        $('#openUploadDocument').trigger('click');
    });
</script> 
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        $(".trigger").click(function(){
            if($(this).hasClass("minus")){
                $(this).removeClass("minus"); 
            }
            else {
                $(this).addClass("minus");   
            }
            $(this).parents("tr").next(".dpr").slideToggle();
        });

    });
    $(document).ready(function(){
            $('input[type="file"]').change(function(e){
                var fileName = e.target.files[0].name;
                    $(".fileUpload").text(fileName);
                //alert('The file "' + fileName +  '" has been selected.');
            });
    });
</script>
@endsection

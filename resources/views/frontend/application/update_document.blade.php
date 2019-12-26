@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('business_information_open', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Business Information</a>
        </li>
        <li>
            <a href="{{ route('promoter-detail', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'edit' => 1]) }}">Management Details</a>
        </li>
        <li>
            <a href="{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'edit' => 1]) }}"  class="active">Documents</a>
        </li>
    </ul>
    <div class="card mt-4">
        <div class="card-body">
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
                                @if($data->doc_id == '6')
                                    @if(file_exists(public_path("storage/user/".$appId.'_'.$gst_no.".pdf")))
                                    <a href="javascript:void(0)" class="badge badge-info font12">GST Pulled</a>
                                    @else
                                    <button class="btn upload-btn pullGST" id="pullgst_rep" data-id="{{ $gst_no }}">PULL GST</button>
                                    @endif
                                    
                                @endif
                                <button class="btn upload-btn openModal" data-id="{{ $data->doc_id }}">Upload</button>
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
                                    @foreach($documentData[$data->document->doc_name] as $value)
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
                                            <a title="Delete Document" onclick="return confirm('Are you sure you want to delete this file?')" href="{{ Route('document-delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o error"></i></a>
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


<div class="modal show" id="modal_pullgst" style="display: none;">
   <div class="modal-dialog">
      <div class="modal-content pb-3">
      <div id="pullMsg"></div>
         <!-- Modal Header -->
         <div class="modal-header">
           GST Report (<strong> {{$gst_no}}</strong>)
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <form id="gstform" method="POST" enctype="multipart/form-data" novalidate="novalidate">
            @csrf
            <input type="hidden" id="biz_gst_number" value="{{$gst_no}}">
            <div class="modal-body text-left">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="email">GST Username</label>
                         <input type="text" id="biz_gst_username" class="form-control" placeholder="Enter GST Username">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="biz_gst_password">GST Password</label>
                         <input type="password" id="biz_gst_password" class="form-control" placeholder="Enter GST Password">
                     </div>
                  </div>
               </div>
               <button type="button" class="btn btn-success float-right  btn-sm" id="fetchdetails">Fetch Detail</button>  
            </div>
         </form>
      </div>
   </div>
</div>

{!!Helpers::makeIframePopup('uploadDocument','Upload Document', 'modal-md')!!}

@endsection
@section('jscript')
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
<script type="text/javascript">
   appurl = '{{URL::route("gstAnalysis") }}';
   _token = "{{ csrf_token() }}";
   appId  = "{{ $appId }}";
</script>
<script>
    $(document).on('click', '#fetchdetails',function () {
        let gst_no   = $('#biz_gst_number').val();
        let gst_usr  = $('#biz_gst_username').val();
        let gst_pass = $('#biz_gst_password').val();
        data = {_token,gst_no,gst_usr,gst_pass,appId};
        $.ajax({
             url  : appurl,
             type :'POST',
             data : data,
             beforeSend: function() {
               $(".isloader").show();
             },
             dataType : 'json',
             success:function(result) {
                console.log(result);
                let mclass = result['status'] ? 'success' : 'danger';
                var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
                $("#pullMsg").html(html);
                if (mclass == 'success') {
                    setTimeout(function(){ location.reload() }, 3000);
                }
             },
             error:function(error) {
                var html = '<div class="alert-danger alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>Some error occured. Please try again later.</div>';
                $("#pullMsg").html(html);
             },
             complete: function() {
                $(".isloader").hide();
             },
        })
    })
</script>
@endsection
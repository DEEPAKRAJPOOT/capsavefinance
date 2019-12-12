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
                                <button class="btn upload-btn openModal" data-id="{{ $data->doc_id }}">Upload</button>
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
                                        <td width="20%"><b>GST Month-Year</b></td>
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
                                        <td width="20%">{{ ($value->gst_month != '') ? date('M',$value->gst_month) : '' }}-{{ ($value->gst_year != '') ? $value->gst_year : '' }}</td>
                                        @endif
                                        <td width="20%"> {{ (isset($value->created_at)) ? date('d-m-Y', strtotime($value->created_at)) : ''}} </td>
                                        <td width="20%"><a title="Download Document" href="{{ Storage::url($value->userFile->file_path) }}" download><i class="fa fa-download"></i></a></td>
                                        <td align="center" width="20%">
                                            <a title="Delete Document" href="{{ Route('document_delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o error"></i></a>
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
                                <input type="submit" value="Submit" class="btn btn-success btn-sm">
                            </form>
                        </div>
                    </div>

                </div>
                @endif
            </div>
        </div>
    </div>
    
</div>


     <!--modal-->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content pb-3">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close close-btns" data-dismiss="modal">&times;</button>
                </div>
                <form id="documentForm" method="POST" action="{{ Route('document_save') }}" enctype="multipart/form-data">
                    <!-- Modal body -->
                    @csrf
                    <input type="hidden" name="doc_id" id="doc_id" value="">
                    <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
                    <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">

                    <div class="modal-body text-left">
                        
                        <div id="is_required_addl_info">
                        <div class="form-group">
                            <label for="email">Select Bank Name</label>
                            <select class="form-control" name="file_bank_id">
                                <option disabled value="" selected>Select Bank Name</option>
                                @foreach($bankdata as $bank)
                                    <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                 @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Select Financial  Year</label>
                            <select class="form-control" name="finc_year">
                               <option value=''>Select Year</option>
                               @for($i=-10;$i<=0;$i++)
                                    <option>{{date('Y')+$i}}</option>
                               @endfor;
                            </select>
                         </div>
                        <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                  <label for="email">Select GST Month</label>
                                  <select class="form-control" name="gst_month">
                                     <option selected diabled value=''>Select Month</option>
                                     @for($i=1;$i<=12;$i++)
                                          <option value="{{$i}}">{{date('F', strtotime("2019-$i-01"))}}</option>
                                     @endfor
                                  </select>
                               </div>
                            </div>
                            <div class="col-md-6">
                               <div class="form-group">
                                  <label for="email">Select GST Year</label>
                                  <select class="form-control" name="gst_year">
                                     <option value=''>Select Year</option>
                                    @for($i=-10;$i<=0;$i++)
                                        <option>{{date('Y')+$i}}</option>
                                   @endfor;
                                  </select>
                               </div>
                            </div>
                         </div>
                        </div>
                        
                        <div class="custom-file upload-btn-cls mb-3 mt-2">
                            <input type="file" class="custom-file-input getFileName doc_file" id="doc_file" name="doc_file[]" multiple="">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <div class="row" id="is_not_for_gst">
                          <div class="col-md-6">
                             <label>Is Password Protected</label>
                             <div class="form-group">
                                <label for="is_password_y">
                                  <input type="radio" name="is_pwd_protected" id="is_password_y" value="1"> Yes
                                </label>
                                <label for="is_password_n">
                                  <input type="radio" name="is_pwd_protected" checked id="is_password_n" value="0"> No
                                </label>
                             </div>
                          </div>
                          <div class="col-md-6">
                             <label>Is Scanned</label>
                             <div class="form-group">
                                <label for="is_scanned_y">
                                  <input type="radio" name="is_scanned" id="is_scanned_y" value="1"> Yes
                                </label>
                                <label for="is_scanned_n">
                                  <input type="radio" name="is_scanned" checked id="is_scanned_n" value="0"> No
                                </label>
                             </div>
                          </div>
                        </div>
                        <div class="row" id="password_file_div">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pwd_txt">Enter File Password</label>
                                    <input type="password" placeholder="Enter File Password" class="form-control" name="pwd_txt" id="pwd_txt">
                                 </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success float-right btn-sm" id="savedocument" >Submit</button>  
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ url('frontend/js/document.js') }}"></script>
<script>

    var messages = {
        
    };
</script> 
@endsection
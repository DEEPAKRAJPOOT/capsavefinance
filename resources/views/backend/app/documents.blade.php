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
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Company Details</a>
        </li>
        <li>
            <a href="{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Promoter Details</a>
        </li>
        <li>
            <a href="{{ route('documents', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}"  class="active">Documents</a>
        </li>
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
                                <button class="btn upload-btn" data-toggle="modal" data-target="#myModal{{ $data->app_doc_id }}">Upload</button>
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
                                        <td width="20%">{{ date('M',$value->gst_month) }}-{{ $value->gst_year }}</td>
                                        @endif
                                        <td width="20%"> {{ date('d-m-Y', strtotime($value->created_at))}} </td>
                                        <td width="20%"><a title="Download Document" href="{{ Storage::url($value->userFile->file_path) }}" download><i class="fa fa-download"></i></a></td>
                                        <td align="center" width="20%">
                                            <a title="Delete Document" href="{{ Route('document-delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o error"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <!--modal-->
                    <div class="modal" id="myModal{{ $data->app_doc_id }}">
                        <div class="modal-dialog">
                            <div class="modal-content pb-3">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <button type="button" class="close close-btns" data-dismiss="modal">&times;</button>
                                </div>
                                <form id="bank-document" method="POST" action="{{ Route('document_save') }}" enctype="multipart/form-data">
                                    <!-- Modal body -->
                                    @csrf
                                    <input type="hidden" name="dir" value="{{ $data->document->doc_name }}">
                                    <input type="hidden" name="doc_id" value="{{ $data->doc_id }}">
                                    <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
                                    <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">
                                    <div class="modal-body text-left">
                                        @if($data->doc_id == '4')
                                        <div class="form-group">
                                            <label for="email">Select Bank Name</label>
                                            <select class="form-control" name="doc_name">
                                                <option>Select Bank Name</option>
                                                <option>AXIS Bank</option>
                                                <option>ICICI Bank</option>
                                                <option>HDFC Bank</option>
                                            </select>
                                        </div>
                                        @endif
                                        @if($data->doc_id == '5')
                                        <div class="form-group">
                                            <label for="email">Select Financial  Year</label>
                                            <select class="form-control" name="finc_year">
                                               <option value=''>Select Year</option>
                                               <option>2009</option>
                                               <option>2010</option>
                                               <option>2011</option>
                                               <option>2012</option>
                                               <option>2013</option>
                                               <option>2014</option>
                                               <option>2015</option>
                                               <option>2016</option>
                                               <option>2017</option>
                                               <option>2018</option>
                                               <option>2019</option>
                                               <option>2020</option>
                                            </select>
                                         </div>
                                        @endif
                                        @if($data->doc_id == '6')
                                        <div class="row">
                                            <div class="col-md-6">
                                               <div class="form-group">
                                                  <label for="email">Select GST Month</label>
                                                  <select class="form-control" name="gst_month">
                                                     <option selected value=''>Select Month</option>
                                                     <option  value='1'>Janaury</option>
                                                     <option value='2'>February</option>
                                                     <option value='3'>March</option>
                                                     <option value='4'>April</option>
                                                     <option value='5'>May</option>
                                                     <option value='6'>June</option>
                                                     <option value='7'>July</option>
                                                     <option value='8'>August</option>
                                                     <option value='9'>September</option>
                                                     <option value='10'>October</option>
                                                     <option value='11'>November</option>
                                                     <option value='12'>December</option>
                                                  </select>
                                               </div>
                                            </div>
                                            <div class="col-md-6">
                                               <div class="form-group">
                                                  <label for="email">Select GST Year</label>
                                                  <select class="form-control" name="gst_year">
                                                     <option value=''>Select Year</option>
                                                     <option>2009</option>
                                                     <option>2010</option>
                                                     <option>2011</option>
                                                     <option>2012</option>
                                                     <option>2013</option>
                                                     <option>2014</option>
                                                     <option>2015</option>
                                                     <option>2016</option>
                                                     <option>2017</option>
                                                     <option>2018</option>
                                                     <option>2019</option>
                                                     <option>2020</option>
                                                  </select>
                                               </div>
                                            </div>
                                         </div>
                                        @endif
                                        <div class="custom-file upload-btn-cls mb-3 mt-2">
                                            <input type="file" class="custom-file-input getFileName" id="customFile{{$data->doc_id}}" name="doc_file[]" multiple="">
                                            <label class="custom-file-label" for="customFile{{$data->doc_id}}">Choose file</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right" id="submitDocument">Submit</button>  
                                    </div>
                                </form>
                            </div>
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
                                <input type="submit" value="Submit" class="btn btn-primary ">
                            </form>
                        </div>
                    </div>

                </div>
                @endif
            </div>
        </div>
    </div>
    
</div>


@endsection
@section('jscript')
<script type="text/javascript">
    
    $('.getFileName').change(function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
    });
    $('.getFileName').change(function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });
    
    $(document).ready(function () {
        $('#bank-document').validate({ // initialize the plugin
            rules: {
                getFileName: {
                required: true,
                extension: "jpg,png,PDF,DOC,DOCX"
                },
                assigned_anchor: {
                    required: true,
                }
            },
            messages: {
              anchor_lead: {
              required: "Please select file",
              extension:"Please select only csv format",
              }
            }
          });

        $('form#bank-document').validate();

        $("#saveAnch").click(function(){
            if($('form#bank-document').valid()){                
                $("#submitDocument").attr("disabled","disabled");
            }  
        });            
   
    });
</script>
@endsection
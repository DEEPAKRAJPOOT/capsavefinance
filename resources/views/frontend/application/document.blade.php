@extends('layouts.app')
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
                <div class="count-heading"> Promoter Details </div>
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
                                    <button class="btn" data-toggle="modal" data-target="#myModal{{ $data->app_doc_id }}">Upload</button>
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
                                        <div class="modal" id="confirm">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        <h4 class="modal-title">Delete Confirmation</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you, want to delete?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-primary" id="delete-btn">Delete</button>
                                                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                    <form id="bank-document" method="POST" action="{{ Route('document-save') }}" enctype="multipart/form-data">
                                        <!-- Modal body -->
                                        @csrf
                                        <input type="hidden" name="dir" value="{{ $data->document->doc_name }}">
                                        <input type="hidden" name="docId" value="{{ $data->doc_id }}">
                                        <input type="hidden" name="bizId" value="{{ request()->get('biz_id') }}">
                                        <input type="hidden" name="appId" value="{{ request()->get('app_id') }}">
                                        <div class="modal-body text-left">
                                            @if($data->doc_id == '4')
                                            <div class="form-group">
                                                <label for="email">Select Bank Name</label>
                                                <select class="form-control" name="doc_name">
                                                    <option>Select Bank Name</option>
                                                    <option>HDFC Bank</option>
                                                    <option>ICICI Bank</option>
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
                                                <label for="email">Upload Document</label>
                                                <input type="file" class="custom-file-input" id="customFile{{$data->doc_id}}" name="doc_file[]" multiple="">
                                                <label class="custom-file-label" for="customFile{{$data->doc_id}}">Choose file</label>
                                                <span class="fileUpload"></span>
                                            </div>
                                            <button type="submit" class="btn btn-primary float-right">Submit</button>  
                                        </div>
                                    </form>
                                </div>
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
                                    <input type="submit" value="Submit" class="btn btn-primary">
                                </form>
                            </div>
                        </div>

                    </div>
                    @endif
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>    
        @endsection

        @section('scripts')
        <script src="{{ url('frontend/assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/popper.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/perfect-scrollbar.jquery.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/jsgrid.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/hoverable-collapse.js') }}"></script>
        <script src="{{ url('frontend/assets/js/misc.js') }}"></script>

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
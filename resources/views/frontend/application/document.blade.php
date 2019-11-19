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
                <div class="count-heading">KYC</div>
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
        <div class="mt-4">
            <div class="col-md-12 form-design ">

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
                        @foreach($requiredDocs as $data)
                        <div class="card card-color mb-0">
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse{{ $data->app_doc_id }}">
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
                            <div id="collapse{{ $data->app_doc_id }}" class="card-body collapse p-0" data-parent="#accordion">

                                <table class="table  overview-table" cellpadding="0" cellspacing="0" border="1">
                                    <tbody>
                                        <tr>
                                            <td width="20%"><b>{{ ($data->doc_id == '4') ? 'Bank' : 'Document Name' }}</b></td>
                                            <td width="20%"><b>Upload On </b></td>
                                            <td width="20%">Download</td>
                                            <td align="center" width="20%">Action</td>
                                        </tr>
                                        @foreach($documentData[$data->document->doc_name] as $value)
                                        <tr>
                                            <td width="20%">{{ $value->doc_name }}</td>
                                            <td width="20%"> {{ date('d-m-Y', strtotime($value->created_at))}} </td>
                                            <td width="20%"><a href="{{ Storage::url($value->userFile->file_path) }}" download><i class="fa fa-download"></i></a></td>
                                            <td align="center" width="20%">
                                                <a class="mr-2" href="{{ Route('document-view') }}"><i class="fa fa-eye"></i></a>
                                                <a href="{{ Route('document-delete', $value->app_doc_file_id) }}" ><i class="fa fa-times-circle-o"></i></a>
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
                                        <input type="hidden" name="appId" value="{{ $data->app_id }}">
                                        <div class="modal-body text-left">
                                            @if($data->doc_id == '4')
                                            <div class="form-group">
                                                <label for="email">Select Bank Name</label>
                                                <select class="form-control" id="sel1" name="doc_name">
                                                    <option>Select Bank Name</option>
                                                    <option>HDFC Bank</option>
                                                    <option>ICICI Bank</option>
                                                </select>
                                            </div>
                                            @else
                                            <div class="form-group">
                                                <label for="email">Document Name</label>
                                                <input class="form-control" type="text" name="doc_name" value="" placeholder="Enter Document Name" >
                                            </div>
                                            @endif
                                            <div class="custom-file upload-btn-cls mb-3 mt-2">
                                                <label for="email">Upload Document</label>
                                                <input type="file" class="custom-file-input" id="customFile" name="bank_docs[]" multiple="">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
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
                                <form method="POST" action="{{ Route('application_save') }}">
                                    @csrf
                                    <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'promoter-details'">
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
        @endsection

        @section('scripts')
        <script src="{{ url('frontend/assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/popper.min.js') }}"></script>
        <script src="{{ url('frontend/assets/js/bootstrap.min.js') }}"></script>
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
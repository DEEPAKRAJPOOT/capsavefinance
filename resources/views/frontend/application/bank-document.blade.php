@extends('layouts.guest')
@section('content')


<div class="step-form pt-5">

    <div class="container">
        <ul id="progressbar">
            <li class="active">
                <div class="count-heading">Business Information </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="active">
                <div class="count-heading"> Promoter Details </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/kyc.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="count-active">
                <div class="count-heading">Bank Statement </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li>
                <div class="count-heading"> GST  Statement </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/buyers.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li>
                <div class="count-heading"> Financial  Statement </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/logistics.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="container">
        <div class="mt-4">
            <div class="col-md-12 form-design ">
                @foreach($errors->all() as $error)
                <span class="text-danger error">{{ $error }}</span>
                @endforeach
                <div class="form-heading pb-3 d-flex pr-0">
                    <h2>Document
                        <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX )</small>
                    </h2>
                </div>


                <div class="row ">
                    <div id="accordion" class="accordion d-table col-sm-12">
                        @foreach($requiredDocs as $data)
                        <div class="card card-color mb-0">
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse1">
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
                            <div id="collapse1" class="card-body collapse p-0" data-parent="#accordion">

                                <table class="table  overview-table" cellpadding="0" cellspacing="0" border="1">
                                    <tbody>
                                        <tr>
                                            <td width="20%"><b>Document Name</b></td>
                                            <td width="20%"><b>Bank</b></td>
                                            <td width="20%"><b>Upload On </b></td>
                                            <td width="20%">Download</td>
                                            <td align="center" width="20%">Action</td>
                                        </tr>
                                        <tr>
                                            <td width="20%">Pan Card</td>
                                            <td width="20%">ICICI Bank</td>
                                            <td width="20%">Tue, Nov 12, 2019, 2:56 AM</td>
                                            <td width="20%"><a href="#"><i class="fa fa-download"></i></a></td>
                                            <td align="center" width="20%"><a class="mr-2" href="#"><i class="fa fa-eye"></i></a>
                                                <a href=""><i class="fa fa-times-circle-o"></i></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>




                            </div>
                        </div>
                        
                        <!--modal-->
                        <div class="modal" id="myModal{{ $data->app_doc_id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <button type="button" class="close close-btns" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form id="bank-document" method="POST" action="{{ Route('bank-document-save') }}" enctype="multipart/form-data">
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
                                            <div class="form-group">
                                                <label for="email">Document ID No</label>
                                                <input class="form-control" type="text" name="doc_id_no" value="" placeholder="Enter Document ID Number" >
                                            </div>
                                            <div class="custom-file mb-3 mt-2">
                                                <label for="email">Upload Document</label>
                                                <input type="file" class="custom-file-input" id="customFile" name="bank_docs[]" multiple="">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
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
                                <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'promoter-details.php'">
                                <input type="submit" value="Save and Continue" class="btn btn-primary">
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
                                    $(document).ready(function () {
                                        $('[data-toggle="tooltip"]').tooltip();

                                        $(".trigger").click(function () {

                                            if ($(this).hasClass("minus")) {

                                                $(this).removeClass("minus");

                                            } else {
                                                $(this).addClass("minus");

                                            }


                                            //$(".trigger").removeClass("minus");
                                            //$(this).addClass("minus");

                                            $(this).parents("tr").next(".dpr").slideToggle();


                                        });
                                    });
                </script>
                <script>
                    var count = 2;
                    function FileDetails(clicked_id) {
                        // GET THE FILE INPUT.
                        var fi = document.getElementById('file_' + clicked_id);
                        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
                        if (fi.files.length > 0) {

                            // THE TOTAL FILE COUNT.
                            var x = 'filePath_' + clicked_id;
                            //var x = document.getElementById(id);alert(id);
                            document.getElementById(x).innerHTML = '';

                            // RUN A LOOP TO CHECK EACH SELECTED FILE.
                            for (var i = 0; i <= fi.files.length - 1; i++) {

                                var fname = fi.files.item(i).name; // THE NAME OF THE FILE.
                                var fsize = fi.files.item(i).size; // THE SIZE OF THE FILE.
                                // SHOW THE EXTRACTED DETAILS OF THE FILE.
                                document.getElementById(x).innerHTML =
                                        '<div class="file-name"> ' +
                                        fname + '' + '<button type="button"  class="close-file"> x' + '</button>' + '</div>';
                            }
                        } else {
                            alert('Please select a file.');
                        }
                    }

                    $(document).ready(function () {
                        $('#add-bank-block').on('click', function () {
                            let html_block = '<div class="col-md-12 doc-block">\
                                                    <div class="justify-content-center align-items-baseline d-flex">\
                                                        <label class="mb-0"><span class="file-icon"><img src="{{ url("backend/signup-assets/images/contractdocs.svg") }}"> </span> Document Name </label>\
                                                        <div class="select-bank ml-auto col-md-3">\
                                                            <select class="form-control">\
                                                                <option>Select bank</option>\
                                                                <option>HDFC</option>\
                                                                <option>ICICI</option>\
                                                            </select>\
                                                        </div>\
                                                        <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">\
                                                            <div id="filePath_' + count + '" class="filePath mt-0"></div>\
                                                            <div class="file-browse">\
                                                                <button class="btn-upload btn btn-sm" type="button"> <i class="fa fa-upload"></i> </button>\
                                                                <input type="file" name="bank_docs[]" id="file_' + count + '" dir="' + count + '" onchange="FileDetails(' + count + ')">\
                                                                <button class="btn custom-btn btn-sm delete-btn" type="button"> <i class="fa fa-trash delete-block"></i> </button>\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                    <hr>\
                                                </div>';
                            $('.bank-document-div').append(html_block);
                            count++;
                        });

                        $(document).on('click', '.delete-block', function () {
                            $(this).closest('div.doc-block').remove();
                        });

                        $(document).on('click', '.close-file', function () {
                            $(this).parent('div').remove();
                        });
                    })
                </script>
                @endsection
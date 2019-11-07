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
            <li class="active">
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
            <li class="count-active">
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
            <div class="form-heading pb-3 d-flex pr-0">
                <h2>GST  Statement
                    <small> ( Maximum file upload size : 2 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX )</small>
                </h2>
            </div>
            <div class="col-md-12 form-design ">
                <div id="reg-box">
                    <form>
                        <div class=" form-fields">
                            <div class="form-sections">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 ">
                                            <h3> GST Statements</h3>
                                            <p class="sameas text-danger"><button class="btn add-btn" type="button" id="add-bank-block"> <i class="fa fa-plus"></i> Add More</button></p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="fil-uploaddiv" style="display: block;">
                                                <div class="row bank-document-div">
                                                    <div class="col-md-12 doc-block">
                                                        <div class="justify-content-center align-items-baseline d-flex">
                                                            <label class="mb-0"><span class="file-icon"><img src="{{ url('backend/signup-assets/images/contractdocs.svg') }}"> </span> Document Name </label>
                                                            <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">
                                                                <div id="filePath_1" class="filePath mt-0"></div>
                                                                <div class="file-browse">
                                                                    <button class="btn-upload btn-sm" type="button"> <i class="fa fa-upload"></i></button>
                                                                    <input type="file" name="gst_docs[]" id="file_1" dir="1" onchange="FileDetails(1)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex btn-section ">
                                <div class="col-md-4 ml-auto text-right">
                                    <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'bank-statement.php'">
                                    <input type="submit" value="Save and Continue" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
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

        $(document).ready(function(){
            $('#add-bank-block').on('click', function(){
                let html_block = '<div class="col-md-12 doc-block">\
                                    <div class="justify-content-center align-items-baseline d-flex">\
                                        <label class="mb-0"><span class="file-icon"><img src="{{ url("backend/signup-assets/images/contractdocs.svg") }}"> </span> Document Name </label>\
                                        <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">\
                                            <div id="filePath_'+count+'" class="filePath mt-0"></div>\
                                            <div class="file-browse">\
                                                <button class="btn-upload btn btn-sm" type="button"> <i class="fa fa-upload"></i> </button>\
                                                <input type="file" name="gst_docs[]" id="file_'+count+'" dir="'+count+'" onchange="FileDetails('+count+')">\
                                                <button class="btn custom-btn btn-sm delete-btn" type="button"> <i class="fa fa-trash delete-block"></i> </button>\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <hr>\
                                </div>';
                $('.bank-document-div').append(html_block);
                count++;
            });

            $(document).on('click', '.delete-block', function(){
                $(this).closest('div.doc-block').remove();
            });

            $(document).on('click', '.close-file', function(){
                $(this).parent('div').remove();
            });
        })
</script>
@endsection
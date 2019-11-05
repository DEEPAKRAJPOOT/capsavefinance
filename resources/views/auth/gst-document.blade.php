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
                    <small> ( Maximum file upload size : 32 MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX )</small>
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
                                            <p class="sameas text-danger"><button class="btn add-btn" type="button"> <i class="fa fa-plus"></i> Add More</button></p>
                                        </div>

                                        <div class="col-md-12">
                                            <div id="uploadsection3" class="fil-uploaddiv" style="display: block;">
                                                <div class="row ">
                                                    <div class="col-md-12">

                                                        <div class="justify-content-center align-items-baseline d-flex">
                                                            <label class="mb-0"><span class="file-icon"><img src="{{ url('backend/signup-assets/images/contractdocs.svg') }}"> </span> Document Name </label>




                                                            <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">
                                                                <div id="filePath_1" class="filePath mt-0"></div>
                                                                <div class="file-browse">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-upload"></i></button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">


                                                                </div>
                                                            </div>

                                                        </div>

                                                        <hr>
                                                    </div>

                                                    <div class="col-md-12">

                                                        <div class="justify-content-center align-items-baseline d-flex">
                                                            <label class="mb-0"><span class="file-icon"><img src="{{ url('backend/signup-assets/images/contractdocs.svg') }}"../> </span> Document Name </label>




                                                            <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">
                                                                <div id="filePath_1" class="filePath mt-0"></div>
                                                                <div class="file-browse">
                                                                    <button class="btn-upload btn btn-sm" type="button"> <i class="fa fa-upload"></i> </button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                    <button class="btn custom-btn btn-sm delete-btn" type="button"> <i class="fa fa-trash"></i> </button>

                                                                </div>
                                                            </div>

                                                        </div>

                                                        <hr>
                                                    </div>
                                                    <div class="col-md-12">

                                                        <div class="justify-content-center align-items-baseline d-flex">
                                                            <label class="mb-0"><span class="file-icon"><img src="{{ url('backend/signup-assets/images/contractdocs.svg') }}"> </span> Document Name </label>




                                                            <div class="ml-auto col-md-4 text-right d-flex justify-content-end align-items-center">
                                                                <div id="filePath_1" class="filePath mt-0"></div>
                                                                <div class="file-browse">
                                                                    <button class="btn-upload btn btn-sm" type="button"> <i class="fa fa-upload"></i> </button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                    <button class="btn custom-btn btn-sm delete-btn" type="button"> <i class="fa fa-trash"></i> </button>

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
                                    <input type="button" value="Save and Continue" class="btn btn-primary" onclick="window.location.href = 'financial-statement.php'">
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection
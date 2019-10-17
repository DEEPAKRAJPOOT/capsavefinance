@extends('layouts.app')

@section('content')
<!--Search Section-->
<section>
    <div class="banner-section">
        <div class="search-box">
            <!--Grid column-->
            @include('layouts.global_search')            
            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    <a href="{{route('add_right')}}">
                        <img src="{{ asset('frontend/inside/images/add-right.png') }}" alt="add-right" class="img-fluid">
                        <span>Add Rights</span>
                    </a>
                </div>
            </div>
            <!--Grid column-->
        </div>
    </div>
</section>
<!--End-->
<!-- SECTION -->
<section>
    <div class="container mt--5 mb-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card box-shadow-div">
                    <div class="card-header">
                        <strong>Publish Right</strong>
                    </div>
                    <form action="{{route('add_right')}}"  autocomplete="off" enctype="multipart/form-data" method="POST" class="input-group" name="rightsForm" id="rightsForm">
                        {{ csrf_field() }}
                        <input type="hidden" name="tmp_right_id" id="tmp_right_id" value="{{ (@$rightData['tmp_right_id']) ? @$rightData['tmp_right_id'] : ''  }}">
                        <input type="hidden" name="tmp_number" id="tmp_number" value="{{ (@$rightData['number']) ? @$rightData['number'] : ''  }}">
                        <div class="card-body  pb-0">
                            <div class="row">
                                <div class="col-md-6 mr-auto mt-5">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <input type="text" value="{{(isset($draftedRight->title) && !empty($draftedRight->title)) ? $draftedRight->title : (old('title') ? old('title') : (@$rightData['title'])?@$rightData['title']:'')}}" name="title" id="title" value="" placeholder="Right's Title*" class="form-control classRequired clsRequired">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="Upload-Thumbnail justify-content-end text-right">
                                        <div class="up-img mb-1">
                                             
                                            
                                            
                                            <img id="OpenImgUpload" src="{{ (@$rightData['thumbnail'])?route('view_inside_register_profile',['folder'=> 'rightsThumbnailsTemp/'.$rightData['user_id'], 'file' => encrypt("60X60_".$rightData['thumbnail'])]) :asset('frontend/inside/images/upload-img.png')}}" width="130" >
                                        </div>
                                        <p class="refresh  text-right">
                                            <input class ="myUploadpopup" id="Upload-Thumbnail" name="thumbnail"  accept=".png, .jpg" type="file">
                                            <input type="hidden" name="tmp_thumbnail" value="{{@$rightData['thumbnail']}}">
                                            <label for="Upload-Thumbnail"><strong>Upload Thumbnail</strong></label>
                                            <i class="fa-refre"></i>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row m-0">
                                        <div class="col-md-5">
                                            <div class="form-group">
<!--                                                <input type="text" value="{{(isset($draftedRight->number) && !empty($draftedRight->number)) ? $draftedRight->number : (old('number') ? old('number') : '')}}" name="number" id="number" class="form-control clsRequired" placeholder="Right Number*">-->
                                                {!!
                                            Form::select('type_id',
                                            [''=>'Type of Right*'] + Helpers::getRightTypeDropDown()->toArray(),
                                            (isset($draftedRight->type_id) && !empty($draftedRight->type_id)) ? $draftedRight->type_id : (old('type_id') ? old('type_id') : (@$rightData['type_id'])?@$rightData['type_id']:''),
                                            array('id' => 'type_id',
                                            'class'=>'form-control select2Cls clsRequired'))
                                            !!} 
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <input type="text" value="{{(isset($draftedRight->inventor) && !empty($draftedRight->inventor)) ? $draftedRight->inventor : (old('inventor') ? old('inventor') : (@$rightData['inventor'])?@$rightData['inventor']:'')}}" name="inventor" id="inventor" placeholder="Innovator*" class="form-control clsRequired">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row m-0">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <input type="text" value="{{(isset($draftedRight->assignee) && !empty($draftedRight->assignee)) ? $draftedRight->assignee : (old('assignee') ? old('assignee') : (@$rightData['assignee'])?$rightData['assignee']:'')}}" name="assignee" id="assignee" class="form-control" placeholder="Co-Innovators">

                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                {!!
                                                Form::select('cluster_id',
                                                [''=>'Choose Group'] + Helpers::getSkillsDropDown()->toArray(),
                                                (isset($draftedRight->cluster_id) && !empty($draftedRight->cluster_id)) ? $draftedRight->cluster_id : (old('cluster_id') ? old('cluster_id') : (@$rightData['cluster_id'])?@$rightData['cluster_id']:''),
                                                array('id' => 'cluster_id',
                                                'class'=>'form-control clsRequired select2Cls'))
                                                !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row m-0">
                                        <div class="col-md-5" style="padding-right:20px;">
                                            <div class="form-group">
                                                <input type="text" value="{{(isset($draftedRight->date) && !empty($draftedRight->date)) ? Helpers::getDateByFormat($draftedRight->date,
                        'Y-m-d', 'm/d/Y') : (old('date') ? old('date') :  (@$rightData['date'])?Helpers::getDateByFormat(@$rightData['date'],'Y-m-d','d/m/Y') :'')}}" name="date" id="date" class="form-control datepicker clsRequired" placeholder="Date of Right*">
                                            <span class="tooltip-1 year-tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                <p>Right's creation date</p></span>
                                            </div>
                                        </div>
                                        <div class="col-md-7" style="padding-right:20px;">
                                            <div class="form-group">
                                                <input type="text" value="{{(isset($draftedRight->expiry_date) && !empty($draftedRight->expiry_date)) ? Helpers::getDateByFormat($draftedRight->expiry_date,
                        'Y-m-d', 'm/d/Y') : (old('expiry_date') ? old('expiry_date') : (@$rightData['expiry_date'])?Helpers::getDateByFormat(@$rightData['expiry_date'],'Y-m-d','d/m/Y') :'')}}" name="expiry_date" id="expiry_date" class="form-control datepicker" placeholder="Expiry Date of Right">
                                            
                                                            <span class="tooltip-1 year-tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                <p>Right's expiration date</p></span>
                                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row m-0">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                               

                                                {!!
                                                Form::select('right_for_id',
                                                [''=>'This Right is for'] + Helpers::getRightForDropDown()->toArray(),
                                                (isset($draftedRight->right_for_id) && !empty($draftedRight->right_for_id)) ? $draftedRight->right_for_id : (old('right_for_id') ? old('right_for_id') : (@$rightData['right_for_id'])?@$rightData['right_for_id']:''),
                                                array('id' => 'right_for_id',
                                                'class'=>'form-control clsRequired select2Cls'))
                                                !!}
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <input type="text" name="url" id="url" value="{{ (isset($draftedRight->url) && !empty($draftedRight->url)) ? $draftedRight->url : (old('url') ? old('url') : (@$rightData['url'])?@$rightData['url']:'') }}" placeholder="Hyperlink to the Right, if available" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class=" ">
                                        <div class="col-md-12 editer-section">
                                            <div class="form-group">
                                                <textarea placeholder="Description*" class="form-control clsRequired" name="description" id="description" rows="3" cols="80">{{ (isset($draftedRight->description) && !empty($draftedRight->description)) ? $draftedRight->description : (@$rightData['description'])?@$rightData['description']:''  }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12  ">
                                            <div class="form-group">
                                                <input type="text" name="keywords" id="keywords" data-role="tagsinput" value="{{ (isset($draftedRight->keywords) && !empty($draftedRight->keywords)) ? $draftedRight->keywords : (old('keywords') ? old('keywords') : (@$rightData['keywords'])?@$rightData['keywords']:'' ) }}" class="form-control" placeholder="keywords">
                                                <input type="hidden" name="right_id" id="right_id" value="{{ (isset($draftedRight->id) && !empty($draftedRight->id)) ? $draftedRight->id : ''  }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 github-outer">
                                            <div class="row github-add">
<!--                                                <div class="col-md-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-github" aria-hidden="true"></i></span>
                                                        </div>
                                                        <input type="text" id="git_url" name="git_url" class="form-control" placeholder="https://github.com" aria-label="Username" aria-describedby="basic-addon1">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 text-center pt-2 pb-2"><strong>OR</strong></div>-->
                                                <div class="col-md-12">
                                                    <div class="file-browse-add">
                                                        <span class="button button-browse">
                                                            Select File <input name="attachements[]" id="attachments0" type="file"  @if(@$rightData['attachment_file_name']=='') required @endif />
                                                        </span>
                                                       
                                                        <input type="text" name="org_attachment_name[]" class="form-control" placeholder="{{@$rightData['attachment_file_name']}}" readonly>
                                                        <input type="hidden" name="tmp_attachement" value="{{@$rightData['attachement']}}">
                                                        <input type="hidden" name="tmp_attachment_file_name" value="{{@$rightData['attachment_file_name']}}">
                                                    </div>
                                                     <label id="attachments0Error" class="error" for="attachments0"></label>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <div class="col-md-12 mb-3">
                                            <button type="button" class="add-slab btn btn-x-md btn-info"><i class="fa fa-plus" aria-hidden="true"></i> Add More</button>
                                        </div>-->
                                        @if($membershipDetail['membership']=='2' && $membershipDetail['is_membership']=='1')
                                        <div class="col-md-12">
                                            <h6><strong>Subscription</strong></h6>
                                            <div class="row mb-2">
                                                <div class="col-md-12 mb-2">
                                                    <label class="control-label"><strong>You have a Premium Retailer Subscription that will be expired on {{ date('d/m/Y',strtotime($membershipDetail['membership_expired']))}}</strong></label>
                                                </div>
                                            </div>
                                        </div>
                                        @else

                                        <div class="col-md-12">

                                            <h6 class="subscription mb-3"><strong>Select Subscription</strong></h6>
                                            <div class="row mb-2">

                                                <!-- Default unchecked -->
                                                <div class="col-md-12 mb-2">
                                                    <div class="custom-control custom-radio radio-bg ">
                                                        <input type="radio" class="custom-control-input" id="FreeHolder" name="exclusive"  value="1" @if(@$rightData['subscription']==1) checked @endif>
                                                        <label class="custom-control-label" for="FreeHolder">Basic
                                                            <span class="tooltip-1"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                <p>Upload your Right with a $1 deposit which will be refunded once this Right is validated</p></span>
                                                        </label>
                                                    </div>
                                                    <div id="Exclusive_1" class="desc cl1" style="display: none;">
                                                    </div>
                                                </div>
                                                <!-- Default checked -->

                                             <!-- <div class="col-md-12 mb-2">
                                                    <div class="custom-control custom-radio radio-bg">
                                                        <input type="radio" class="custom-control-input" id="PremiumRetailer" name="exclusive"  value="2" @if(@$rightData['subscription']==2) checked @endif>
                                                        <label class="custom-control-label" for="PremiumRetailer">Pro.
                                                            <span class="tooltip-1"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                <p>Welcome! Upload your Intellectual Property with $96/month as a subscription fee, with lots of benefit. Choose the time period for this offer.</p></span>
                                                        </label>
                                                    </div>

                                                    <div id="Exclusive_2" class="desc cl2" style="<?php //if(@$rightData['subscription']=='2'){echo 'display:block';}else{ echo 'display:none';}?>">
                                                        <input type="text" maxlength="3" name="num_of_month_2" id ="num_of_month_2" value="{{@$rightData['num_of_month']}}" class="form-control numcls" placeholder="Number of Month">
                                                    </div>
                                                </div>-->
                                                <!--<div class="col-md-12">
                                                    <div class="custom-control custom-radio radio-bg">
                                                        <input type="radio" class="custom-control-input" id="PremiumCorporate" name="exclusive"  value="3">
                                                        <label class="custom-control-label" for="PremiumCorporate">Enterprise
                                                            <span class="tooltip-1"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                <p>Welcome! Upload your group's Intellectual Property with $960/month as a subscription fee, with lots of benefit. Choose the time period for this offer.</p></span>
                                                        </label>
                                                    </div>
                                                    <div id="Exclusive_3" class="desc cl3" style="display: none;">
                                                        <input type="text" maxlength="3" name ="num_of_month_3" id="num_of_month_3" class="form-control numcls" placeholder="Number of Month">
                                                    </div>
                                                </div>-->
                                            </div>
                                        </div>
                                        @endif


                                        <div class="col-md-12  Trading">
                                            <div class="heading"><strong>Trading</strong></div>
                                            <div class="">
                                                <div class="row m-0">
                                                    <div class="col-md-6   left-us">
                                                        <div class="col01">
                                                            <div class="more-detail mr-auto mb-3" >
                                                                <div class="custom-control custom-radio radio-bg trade-radio">
                                                                    <input name="is_exclusive_purchase" value="1" @if(isset($draftedRight->trading_type) && !empty($draftedRight->trading_type) && $draftedRight->trading_type == 1) checked @elseif(old('trading_type') && old('trading_type') ==1) checked @elseif(@$rightData['is_exclusive_purchase']==1) checked @else '' @endif type="checkbox" class="mychecks my-radiobtn mygroup" id="exclusive">
                                                                           <label>Exclusive Licensing</label>
                                                                </div>
                                                            </div>
                                                            <span id="exp" style="<?php if(@$rightData['is_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"  ><strong><input class ="numcls" maxlength="10" type="text" id="exclusive_price" name="exclusive_purchase_price" value="{{@$rightData['exclusive_purchase_price']}}" placeholder="Price ($)" style="width:80px;"> /year</strong> </span>
                                                        </div>
<!--                                                        <p class="more-detail ques">For more detail please contact with us?</p>-->
                                                    </div>
                                                    <div class="col-md-6  right-us">
                                                        <div class="col01">
                                                            
                                                                <div class="more-detail mr-auto mb-3" >
                                                                    <div class="custom-control custom-radio radio-bg trade-radio">
                                                                        <input name="is_non_exclusive_purchase" value="1" @if(isset($draftedRight->trading_type) && !empty($draftedRight->trading_type) && $draftedRight->trading_type == 1) ? checked @elseif(old('trading_type') && old('trading_type') ==1) checked @elseif(@$rightData['is_non_exclusive_purchase']==1) checked @else ''@endif type="checkbox" class="mychecks my-radiobtn mygroup" id="non_exclusive">
                                                                               <label>Non-Exclusive Licensing</label>
                                                                    </div>
                                                                </div>
                                                            
                                                            <span id="non_exp" style="<?php if(@$rightData['is_non_exclusive_purchase']=='1'){echo 'display:block';}else{ echo 'display:none';}?>"><strong><input maxlength="10" class ="numcls" type="text" id="non_exclusive_price" name="non_exclusive_purchase_price" value="{{@$rightData['non_exclusive_purchase_price']}}" placeholder="Price ($)"  style="width:80px;"> /month</strong> </span>
                                                        </div>
<!--                                                        <p class="more-detail ques">For more detail please contact with us?</p>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" mt-4 mb-4">
                                        <div class="form-group row m-0">
                                            <div class="col-md-4 mr-auto"> <a href="" class="btn btn-x-md btn-default "> Cancel </a></div>
                                            <div class="col-md-5 text-right">
<!--                                                <input type="submit" name="btnDraft" id="btnDraft" value="Draft" class="btn btn-x-md btn-info save-right-info">-->
                                                <input type="submit" name="btnPost" id="btnPost" class="btn btn-x-md btn-primary submitRights" value="Submit">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- Card -->
            </div>
            @include('layouts.similar_rights')
        </div>
    </div>
    <!-- The Modal -->
</section>
<!-- / END SECTION -->
@endsection
@section('pageTitle')
Add Rights
@endsection
@section('additional_css')
<style>
    .error{color:red;}
    label#type_id-error {
    position: absolute;
    bottom: 20px;
    font-size: 12px;
}
label#cluster_id-error {
    font-size: 12px;
    position: absolute;
    bottom: 20px;
}
label.error{
    font-size: 12px;}
#exclusive-error {
    font-size: 12px;
    position: absolute;
    top: 20px;
    left: 0;
    width: 160px;
}
#attachments0-error {
    font-size: 12px;
    position: absolute;
    top: 35px;
    left: 5px;
    width: 160px;
}
</style>
<style>
    .user-head h4 {
    color: #3eb9ce;
    text-decoration: underline;
}
    .license {background: #fff;}
   .user-head {
    padding: 10px 15px;
    background: #fff;
    margin: 10px;
}
.user-head td {
    color: #666;
    font-size: 14px;
}
    
    </style>

<link rel="stylesheet" href="{{ asset('frontend/inside/css/bootstrap-tagsinput.css') }}">
@endsection
@section('jscript')
<script>
    var messages = {
        req_right_title: "{{ trans('error_messages.req_right_title') }}",
        req_right_type: "{{ trans('error_messages.req_right_type') }}",
        req_right_type_numeric: "{{ trans('error_messages.req_right_type_numeric') }}",

        req_right_number: "{{ trans('error_messages.req_right_number') }}",
        req_right_inventor: "{{ trans('error_messages.req_right_inventor') }}",
        req_right_assignee: "{{ trans('error_messages.req_right_assignee') }}",

        req_right_cluster: "{{ trans('error_messages.req_right_cluster') }}",
        req_right_cluster_numeric: "{{ trans('error_messages.req_right_cluster_numeric') }}",
        req_right_date: "{{ trans('error_messages.req_right_date') }}",
        req_right_description: "{{ trans('error_messages.req_right_description') }}"
    };

    $("#date").datepicker({
        maxDate: 0,
        onSelect: function (date) {
            var min = $(this).datepicker('getDate', '+1d');
            min.setDate(min.getDate()+1);
            $('#expiry_date').datepicker('option', {minDate: min});
        }
    })

    $('#OpenImgUpload').click(function () {
        $('.myUploadpopup').trigger('click');
    });

</script>
<script src="{{ asset('frontend/inside/js/validation/rights.js')}}"></script>  
<script src="{{ asset('frontend/inside/js/bootstrap-tagsinput.js')}}"></script>
@endsection

<?php $enable_download = false;?>
@extends('layouts.app')
@section('content')
<style type="text/css">
    .pullout{
        background: #e1f0eb;
        padding-top: 4px;
        padding-right: 3px;
        padding-bottom: 5px;
        padding-left: 10px;
        border-left: solid #adbdb8;
        margin: 20px 0;
        font-size: 16px;
        line-height: 28px;
    }
</style>
<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <div id="pullMsg"></div>
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow form-design">
                        <div class="form-fields">
                            <div class="form-sections">
                                <h3 class="mt-0 pullout">Pull GSTIN Detail</h3>

                                <!--<div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">GST Number
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_number" value="{{$gst_no}}" id="biz_gst_number" readonly class="form-control" tabindex="1" placeholder="Enter GST Number">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="txtEmail">GST USERNAME
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_username" value="" id="biz_gst_username" value="" class="form-control" tabindex="1" placeholder="Enter GST Username">
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="gst_otp_div" style="display: none">
                                        <div class="form-group">
                                            <label for="biz_gst_otp">GST OTP
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_otp" id="biz_gst_otp" value="" class="form-control" tabindex="3" placeholder="Enter GST OTP" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mt-25">
                                            <input type="submit" id="SendOtp" value="Generate OTP" class="btn btn-primary">
                                            <input type="submit" id="VerifyOtp" value="Validate OTP" class="btn btn-primary" style="display: none">
                                        </div>
                                    </div>
                                </div>-->
                                @if($all_gst_details->count() > 0)
                                @foreach($all_gst_details as $gst_detail)
                                 <div class="row gst_detail_div">
                                    <div class="col-md-3">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">GST Number
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_number" value="{{$gst_detail['pan_gst_hash']}}" readonly class="form-control biz_gst_number" tabindex="1" placeholder="Enter GST Number">
                                        </div>
                                    </div>
                                     @if(!file_exists(public_path("storage/user/".$appId.'_'.$gst_detail['pan_gst_hash'].".pdf")))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="txtEmail">GST USERNAME
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_username" value="" value="" class="form-control biz_gst_username" tabindex="1" placeholder="Enter GST Username">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="txtEmail">GST PASSWORD
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="password" name="biz_gst_password" value="" value="" class="form-control biz_gst_password" tabindex="3" placeholder="Enter GST Password">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mt-25">
                                            <input type="submit" value="Fetch Detail" class="fetchdetails btn btn-success btn-sm">
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-3">
                                        <div class="form-group mt-25">
                                            <a href="{{$enable_download ? (Storage::url('user/'.$appId.'_'.$gst_detail['pan_gst_hash'].'.pdf')) : 'javascript:void(0)'}}" class="badge badge-info mt-2 font12" download>GST report has been pulled for this GST number.</a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
   appurl = '{{URL::route("gstAnalysis") }}';
   send_otp_url = '{{URL::route("send_gst_otp") }}';
   verify_otp_url = '{{URL::route("verify_gst_otp") }}';
   _token = "{{ csrf_token() }}";
   appId  = "{{ $appId }}";
</script>
<script>
    $(document).on('click', '#VerifyOtp',function () {
        let gst_no   = $('#biz_gst_number').val();
        let gst_usr  = $('#biz_gst_username').val();
        let otp  = $('#biz_gst_otp').val();
        data = {_token,gst_no,gst_usr, appId, otp};
        $.ajax({
             url  : verify_otp_url,
             type :'POST',
             data : data,
             beforeSend: function() {
               $(".isloader").show();
             },
             dataType : 'json',
             success:function(result) {
                let mclass = result['status'] ? 'success' : 'danger';
                var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
                $("#pullMsg").html(html);
                $('#SendOtp').hide();
                result['status'] ? $('#VerifyOtp,#gst_otp_div').hide() : '';
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
<script>
    $(document).on('click', '#SendOtp',function () {
        let gst_no   = $('#biz_gst_number').val();
        let gst_usr  = $('#biz_gst_username').val();
        data = {_token,gst_no,gst_usr, appId};
        $.ajax({
             url  : send_otp_url,
             type :'POST',
             data : data,
             beforeSend: function() {
               $(".isloader").show();
             },
             dataType : 'json',
             success:function(result) {
                let mclass = result['status'] ? 'success' : 'danger';
                var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
                $("#pullMsg").html(html);
                $('#SendOtp').hide();
                $('#VerifyOtp').show();
                $('#gst_otp_div').show();
                $('#biz_gst_username').attr('readonly', 'readonly');
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
<script>
    $(document).on('click', '.fetchdetails',function () {
        $target_div  = $(this).closest('.gst_detail_div');
        let gst_no   = $target_div.find('.biz_gst_number').val();
        let gst_usr  = $target_div.find('.biz_gst_username').val();
        let gst_pass = $target_div.find('.biz_gst_password').val();
        data = {_token,gst_no,gst_usr,gst_pass, appId};
        $.ajax({
             url  : appurl,
             type :'POST',
             data : data,
             beforeSend: function() {
               $(".isloader").show();
             },
             dataType : 'json',
             success:function(result) {
                $('.error_msg').remove();
                let mclass = result['status'] ? 'success' : 'danger';
                var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
                $("#pullMsg").html(html);
                $target_div.append('<span class="ml-15 mt--10 error_msg text-'+ mclass +'">'+result['message']+'</span>');
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
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
    .pullgstdiv{
        background-color: #ece1e1;
        padding: 15px;
        margin-top: 5px;
    }
    .mt-30{
        margin-top: 30px;
    }
</style>
<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow form-design">
                        <div class="form-fields">
                            <div class="form-sections">
                                <h3 class="mt-0 pullout">Pull GSTIN Detail</h3>

                                @if($all_gst_details->count() > 0)
                                @foreach($all_gst_details as $key => $gst_detail)
                                <div class="pullgstdiv">
                                    <div id="pullmsg_{{$key}}" class="errors"></div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group password-input">
                                                <label for="txtPassword">GST Number 
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <input type="text" name="biz_gst_number_{{$key}}" value="{{$gst_detail['pan_gst_hash']}}" id="biz_gst_number_{{$key}}" readonly class="form-control" tabindex="1" placeholder="Enter GST Number">
                                            </div>
                                        </div>
                                    </div>
                                @if(!file_exists(public_path("storage/user/".$appId.'_'.$gst_detail['pan_gst_hash'].".pdf")))
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="txtEmail">GST USERNAME
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <input type="text" name="biz_gst_username_otp_{{$key}}" id="biz_gst_username_otp_{{$key}}" class="form-control" tabindex="1" placeholder="Enter GST Username">
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="gst_div_otp_{{$key}}" style="display: none">
                                            <div class="form-group">
                                                <label for="biz_gst_otp_{{$key}}">GST OTP
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <input type="text" name="biz_gst_otp_{{$key}}" id="biz_gst_otp_{{$key}}"  class="form-control" tabindex="3" placeholder="Enter GST OTP" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mt-30">
                                                <input type="submit" data-id="{{$key}}" id="sendotp_{{$key}}" value="Generate OTP" class="btn btn-success btn-sm sendotp">
                                                <input type="submit" data-id="{{$key}}" id="verifyotp_{{$key}}" value="Validate OTP" class="btn btn-success btn-sm verifyotp" style="display: none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="txtEmail">GST USERNAME
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <input type="text" name="biz_gst_username_login_{{$key}}" id="biz_gst_username_login_{{$key}}" class="form-control" tabindex="1" placeholder="Enter GST Username">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="txtEmail">GST PASSWORD
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <input type="password" name="biz_gst_password_login_{{$key}}" class="form-control" id="biz_gst_password_login_{{$key}}" tabindex="3" placeholder="Enter GST Password">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mt-30">
                                                <input type="submit" data-id="{{$key}}" value="Fetch Detail" class="fetchdetails btn btn-success btn-sm">
                                            </div>
                                        </div>
                                    </div>
                                 @else

                                    <div style="margin-top: -47px;text-align: center;">
                                        <a href="{{$enable_download ? (Storage::url('user/'.$appId.'_'.$gst_detail['pan_gst_hash'].'.pdf')) : 'javascript:void(0)'}}" class="badge badge-info font12" download>GST report has been pulled for this GST number.</a>
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
    $(document).on('click', '.verifyotp',function () {
        $('.errors').html('');
        let dataid = $(this).data('id');
        let gst_no   = $('#biz_gst_number_' + dataid).val();
        let gst_usr  = $('#biz_gst_username_otp_' + dataid).val();
        let otp  = $('#biz_gst_otp_' + dataid).val();

        if (!gst_no || !gst_usr || !otp) {
             $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-danger">All fields are required.</span>');
             return false;
        }

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
                $('.error_msg').remove();
                let mclass = result['status'] ? 'success' : 'danger';
                $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-'+ mclass +'">'+result['message']+'</span>');

                $('#sendotp_' + dataid).hide();
                if (result['status']) {
                     $('#verifyotp_' + dataid).hide();
                     $('#gst_div_otp_' + dataid).hide();
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
<script>
    $(document).on('click', '.sendotp',function () {
        $('.errors').html('');
        let dataid = $(this).data('id');
        let gst_no   = $('#biz_gst_number_' + dataid).val();
        let gst_usr  = $('#biz_gst_username_otp_' + dataid).val();
        if (!gst_no || !gst_usr) {
             $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-danger">GST username can\'t be empty.</span>');
             return false;
        }
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
                $('.error_msg').remove();
                let mclass = result['status'] ? 'success' : 'danger';
                $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-'+ mclass +'">'+result['message']+'</span>');
                if (result['status']) {
                    $('#sendotp_' + dataid).hide();
                    $('#verifyotp_' + dataid).show();
                    $('#gst_div_otp_' + dataid).show();
                    $('#biz_gst_username_otp_' + dataid).attr('readonly', 'readonly');
                }
             },
             error:function(error) {
                 $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-danger"> Some error occured. Please try again later.</span>');
             },
             complete: function() {
                $(".isloader").hide();
             },
        })
    })
</script>
<script>
    $(document).on('click', '.fetchdetails',function () {
        $('.errors').html('');
        let dataid = $(this).data('id');
        let gst_no   = $('#biz_gst_number_' + dataid).val();
        let gst_usr  = $('#biz_gst_username_login_' + dataid).val();
        let gst_pass  = $('#biz_gst_password_login_' + dataid).val();
         if (!gst_no || !gst_usr || !gst_pass) {
             $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-danger">All fields are required.</span>');
             return false;
        }
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
                $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-'+ mclass +'">'+result['message']+'</span>');
                if (mclass == 'success') {
                    setTimeout(function(){ location.reload() }, 3000);
                }
             },
             error:function(error) {
                $("#pullmsg_" + dataid).html('<span class="ml-15 mt--10 error_msg text-danger"> Some error occured. Please try again later.</span>');
             },
             complete: function() {
                $(".isloader").hide();
             },
        })
    })
</script>
@endsection
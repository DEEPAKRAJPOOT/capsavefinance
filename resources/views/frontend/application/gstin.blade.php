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
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group password-input">
                                            <label for="txtPassword">GST Number
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_number" value="" id="biz_gst_number" class="form-control" tabindex="1" placeholder="Enter GST Number">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtEmail">GST USERNAME
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="text" name="biz_gst_username" value="" id="biz_gst_username" value="" class="form-control" tabindex="1" placeholder="Enter GST Username">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtEmail">GST PASSWORD
                                                <span class="mandatory">*</span>
                                            </label>
                                            <input type="password" name="biz_gst_password" value="" id="biz_gst_password" value="" class="form-control" tabindex="3" placeholder="Enter GST Password">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex btn-section ">
                                    <div class="ml-auto text-right">
                                        <input type="submit" id="fetchdetails" value="Fetch Detail" class="btn btn-primary">
                                    </div>
                                </div>
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
   _token = "{{ csrf_token() }}";
</script>
<script>
    $(document).on('click', '#fetchdetails',function () {
        let gst_no   = $('#biz_gst_number').val();
        let gst_usr  = $('#biz_gst_username').val();
        let gst_pass = $('#biz_gst_password').val();
        data = {_token,gst_no,gst_usr,gst_pass};
        $.ajax({
             url  : appurl,
             type :'POST',
             data : data,
             beforeSend: function() {
               $(".isloader").show();
             },
             dataType : 'json',
             success:function(result) {
                console.log(result);
                let mclass = result['status'] ? 'success' : 'danger';
                var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
                $("#pullMsg").html(html);
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
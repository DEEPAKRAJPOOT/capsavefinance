@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <div class="card mt-4">
            <div class="card-body">
                <div class="data">
                    <h2 class="sub-title bg mb-4">Limit By Capsave</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
                    <form method="POST" action="{{route('save_limit_assessment')}}">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group INR">
                                    <label>Total Limit</label>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:27px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control form-control-sm" name="tot_limit_amt">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Product Type</label>
                                    <select class="form-control" name="product_id" id="product_id">
                                        <option value="">Select Product</option>
                                        <option value="1">Supply Chain</option>
                                        <option value="2">Term Loan</option>
                                        <option value="3">Leasing</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Anchor</label>
                                    <select class="form-control" name="anchor_id" id="anchor_id">
                                        <option value="">Select Anchor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Program</label>
                                    <select class="form-control" name="prgm_id" id="program_id">
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label>Select Limit</label>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:30px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control" name="limit_amt">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-success btn-sm float-right " type="submit">Submit</button>
                            </div>
                        </div>
                        </form>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive ps ps--theme_default mt-2" data-ps-id="7e2fdaa3-dcfc-2f99-49b8-8cca4056cf70">
                                    <table id="supplier-listing" class="table table-striped cell-border dataTable no-footer overview-table mb-0" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                            <th width="17%">Sr. No.</th>
                                            <th width="17%">Product Type</th>
                                            <th width="17%">Anchor</th>
                                            <th width="17%">Program</th>
                                            <th width="16%">Limit</th>
                                            <th width="16%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                                <div id="accordion" class="accordion">
                                <div class="card card-color mb-0">
                                    <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#collapseOne" aria-expanded="false">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i">
                                            <tbody>
                                                <tr role="row" class="odd">
                                                   <td width="17%">1.</td>
                                                   <td width="17%">Supply Chain</td>
                                                   <td width="17%">Anchor 1</td>
                                                   <td width="17%">Program 1</td>
                                                   <td width="16%">10,00,000</td>
                                                   <td width="16%"><button href="#" data-toggle="modal" data-target="#myModal" class="btn btn-success btn-sm">+ Add</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="collapseOne" class="card-body bdr pt-2 pb-2 collapse" data-parent="#accordion" style="">
                                        <ul class="row p-0 m-0">
                                            <li class="col-md-2">Loan Offer <br> <i class="fa fa-inr"></i> <b>10,000,00</b></li>
                                            <li class="col-md-2">Interest(%)  <br> <b>12%</b></li>
                                            <li class="col-md-2">Invoice Tenor(Days) <br> <b>30 Days</b></li>
                                            <li class="col-md-2">Margin(%) <br> <b>10</b></li>
                                            <li class="col-md-2">Processing Fee  <br><i class="fa fa-inr"></i><b>1000</b></li>
                                            <li class="col-md-2"><a href="#" data-toggle="modal" data-target="#myModal1"><br><i class="fa fa-edit"></i>Edit</a></li>
                                        </ul>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection
@section('jscript')
<script>
var messages = {
    "get_anchors_by_product" : "{{route('ajax_get_anchors_by_product')}}",  
    "get_programs_by_anchor" : "{{route('ajax_get_programs_by_anchor')}}",  
    "token" : "{{ csrf_token() }}"  
};

$(document).ready(function(){
    $('#product_id').on('change',function(){
        let product_id = $('#product_id').val();
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_anchors_by_product,
            'type':"POST",
            'data':{"_token" : messages.token, "product_id" : product_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                fillAnchors(res);
                $('#program_id').html('<option value="">Select Program</option>');
                $('.isloader').hide();
            }
        })
    });

    $('#anchor_id').on('change',function(){
        let anchor_id = $('#anchor_id').val();
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_programs_by_anchor,
            'type':"POST",
            'data':{"_token" : messages.token, "anchor_id" : anchor_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                fillPrograms(res);
                $('.isloader').hide();
            }
        })
    });
});

function fillAnchors(programs){
    let html = '<option value="">Select Anchor</option>';
    $.each(programs, function(i,program){
        if(program.anchors != null)
            html += '<option value="'+program.anchors.anchor_id+'">'+program.anchors.comp_name+'</option>';
    });
    $('#anchor_id').html(html);
}

function fillPrograms(programs){
    let html = '<option value="">Select Program</option>';
    $.each(programs, function(i,program){
        if(program.prgm_name != null)
            html += '<option value="'+program.prgm_id+'">'+program.prgm_name+'</option>';
    });
    $('#program_id').html(html);
}
</script>
@endsection

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
                        <input type="hidden" name="app_limit_id" value="{{$limitData->app_limit_id}}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group INR">
                                    <label>Total Limit</label>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:27px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control form-control-sm" name="tot_limit_amt" value="{{$limitData->tot_limit_amt}}" disabled>
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
                                <div class="table-responsive ps ps--theme_default mt-2">
                                    <table id="supplier-listing" class="table table-striped cell-border  overview-table mb-0" cellspacing="0" width="100%">
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

                                @forelse($prgmLimitData as $key=>$prgmLimit)
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#collapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="17%">{{($key+1)}}</td>
                                                       <td width="17%">{{$prgmLimit->program->product->product_name}}</td>
                                                       <td width="17%">{{$prgmLimit->anchor->comp_name}}</td>
                                                       <td width="17%">{{$prgmLimit->program->prgm_name}}</td>
                                                       <td width="16%">{{$prgmLimit->limit_amt}}</td>
                                                       <td width="16%"><button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Edit Limit</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="collapse{{$key+1}}" class="card-body bdr pt-2 pb-2 collapse">
                                            <ul class="row p-0 m-0">
                                            @if($prgmLimit->offer)
                                                <li class="col-md-2">Loan Offer <br> <i class="fa fa-inr"></i> <b>{{$prgmLimit->offer->prgm_limit_amt}}</b></li>
                                                <li class="col-md-2">Interest(%)  <br> <b>{{$prgmLimit->offer->interest_rate}}</b></li>
                                                <li class="col-md-2">Invoice Tenor(Days) <br> <b>{{$prgmLimit->offer->tenor}}</b></li>
                                                <li class="col-md-2">Margin(%) <br> <b>{{$prgmLimit->offer->margin}}</b></li>
                                                <li class="col-md-2">Processing Fee  <br><i class="fa fa-inr"></i><b>{{$prgmLimit->offer->processing_fee}}</b></li>
                                                <li class="col-md-2"><button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Update</button></li>
                                            @else
                                                <li class="col-md-10">No Record found</li>
                                                <li class="col-md-2"><button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Add</button></li>
                                            @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed">
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="100%" style="text-align: center;" colspan="6">No record found</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <a data-toggle="modal" data-target="#limitOfferFrame" data-url ="" data-height="700px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openOfferModal" style="display: none;"><i class="fa fa-plus"></i>Add Offer</a>
                            <a data-toggle="modal" data-target="#editLimitFrame" data-url ="" data-height="350px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openLimitModal" style="display: none;"><i class="fa fa-plus"></i>Edit Limit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

{!!Helpers::makeIframePopup('limitOfferFrame','Add Offer', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editLimitFrame','Edit Limit', 'modal-lg')!!}

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

    $('.add-offer').on('click', function(){
        let data_url = $(this).data('url');
        $('#openOfferModal').attr('data-url', data_url);
        $('#openOfferModal').trigger('click');
    });

    $('.edit-limit').on('click', function(){
        let data_url = $(this).data('url');
        $('#openLimitModal').attr('data-url', data_url);
        $('#openLimitModal').trigger('click');
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

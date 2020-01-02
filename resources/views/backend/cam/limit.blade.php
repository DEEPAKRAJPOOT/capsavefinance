@extends('layouts.backend.admin_popup_layout')
@section('content')

  <form method="POST" style="width:100%;" action="{{route('update_limit')}}" target="_top">
    @csrf
    <input type="hidden" value="{{request()->get('app_id')}}" name="app_id">
    <input type="hidden" value="{{request()->get('biz_id')}}" name="biz_id">
    <input type="hidden" value="{{request()->get('app_prgm_limit_id')}}" name="app_prgm_limit_id">
    
    <div class="row">
    <div class="col-md-12">
      <div class="form-group row">
        <label for="txtPassword" class="col-md-4"><b>Product Type:</b></label> 
        <div class="col-md-8">
        <input type="text" name="prgm_limit_amt" class="form-control" value="{{isset($limitData->program->product)? $limitData->program->product->product_name : ''}}" disabled>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row">
        <label for="txtPassword" class="col-md-4"><b>Anchor:</b></label> 
        <div class="col-md-8">
        <input type="text" name="anchor_id" class="form-control" value="{{isset($limitData->anchor)? $limitData->anchor->comp_name : ''}}" disabled>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row">
        <label for="txtPassword" class="col-md-4"><b>Program:</b></label> 
        <div class="col-md-8">
        <input type="text" name="prgm_id" class="form-control" value="{{isset($limitData->prgm_id)? $limitData->program->prgm_name : ''}}" disabled>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="form-group row INR">
        <label for="txtPassword" class="col-md-4"><b>Limit:</b></label>
        <div class="col-md-8">
        <a href="javascript:void(0);" class="verify-owner-no" style="top:2px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
        <input type="text" name="limit_amt" class="form-control" value="{{isset($limitData->limit_amt)? $limitData->limit_amt: ''}}" placeholder="Limit amount">
        </div>
      </div>
    </div>

    </div>
    <div class="row">
      <div class="col-md-12">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
      </div>
    </div>   
  </form>
 
@endsection

@section('jscript')

@endsection
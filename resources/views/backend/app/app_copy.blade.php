@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
          <div class="row">                
               <div class="col-12">
                <form action="{{route('copy_app')}}" method="POST">
                    @csrf
                    <label for="txtCreditPeriod">Please select
                      <span class="mandatory">*</span>
                    </label>
                   <br>
                   <select id="is_active" required="required" class="form-control" name="sel_assign_role">
                     <option value="">Please select</option>
                     <option value="1"> Limit Enhancement</option>
                     <option value="2"> Adhoc Facility</option>
                 </select>
                   </br></br>
                <div class="col-12">
                  <input type="hidden" class="btn btn-success" name="user_id" value="{{$res['user_id']}}">  
                  <input type="hidden" class="btn btn-success" name="biz_id" value="{{$res['biz_id']}}">
                  <input type="hidden" class="btn btn-success" name="app_id" value="{{$res['app_id']}}">   
                  <input type="submit" class="btn btn-success" name="submit" value="submit">   
                </div>
             </form>  
            </div>
                               
        </div>


@endsection
@section('jscript')
@endsection
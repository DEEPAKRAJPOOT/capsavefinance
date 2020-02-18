@extends('layouts.backend.admin_popup_layout')
@section('content')
  <div class="modal-body text-left">
     <form id="shareColenderForm" name="shareColenderForm" method="POST" action="{{route('save_share_to_colender')}}">
        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
        <input type="hidden" name="app_prgm_limit_id" value="{{request()->get('app_prgm_limit_id')}}">
        @csrf
        <div class="row">
           <div class="col-12">
              <div class="form-group">
                 <label for="txtCreditPeriod">Co-lender Name
                 <span class="mandatory">*</span>
                 </label>
                 <select name="co_lender_id" id="co_lender_id" class="form-control">
                    <option value="">Select Co-lender</option>
                    @foreach($coLenders as $key=>$coLender)
                      <option value="{{$coLender->co_lender_id}}" {{(old('capsave_percent') == $coLender->co_lender_id)? 'selected': ''}}>{{$coLender->f_name.'('.$coLender->comp_name.')'}}</option>
                    @endforeach
                 </select>
                 @error('co_lender_id')
                    <span class="error">{{ $message }}</span>
                 @enderror
              </div>
           </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
               <label for="txtEmail">Capsave Share (%)
               <span class="mandatory">*</span>
               </label>
               <input type="test" name="capsave_percent" id="capsave_percent" value="{{old('capsave_percent')}}" class="form-control share_percent" placeholder="Capsave Share (%)" maxlength="5">
               @error('capsave_percent')
                  <span class="error">{{ $message }}</span>
               @enderror
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
               <label for="txtEmail">Co-lender Share (%)
               <span class="mandatory">*</span>
               </label>
               <input type="test" name="co_lender_percent" id="co_lender_percent" value="{{old('co_lender_percent')}}" class="form-control share_percent" placeholder="Co-lender Share (%)" maxlength="5">
               @error('co_lender_percent')
                  <span class="error">{{ $message }}</span>
               @enderror
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
             <div class="form-group">
                <label for="txtMobile">Comment
                </label>
                <textarea class="form-control" name="capsave_comment" id="capsave_comment" placeholder="Comment" maxlength="250" rows="3">{{old('capsave_comment')}}</textarea>
                @error('capsave_comment')
                  <span class="error">{{ $message }}</span>
                @enderror
             </div>
          </div>
        </div> 
        <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAgency">Submit</button>  
     </form>
   </div>
@endsection

@section('jscript')
<script type="text/javascript">
    $(document).ready(function () {
        $('#shareColenderForm').validate({ // initialize the plugin
            rules: {
                'co_lender_id' : {
                    required : true
                },
                'capsave_percent': {
                    required: true,
                    max: 100,
                },
                'co_lender_percent' : {
                    required : true,
                    max: 100,
                }
            },
            messages: {
                'co_lender_id': {
                    required: "Please select Co-lender",
                },
                'capsave_percent': {
                    required: 'Please fill Capsave percent',
                },
                'co_lender_percent': {
                    required: "Please enter Co-lender percent",
                }
            }
        });

        $("#shareColenderForm button[type=submit]").click(function(){
            if($('#shareColenderForm').valid()){
              unsetError('input[name=co_lender_percent]');
              let total = 0;
              $('.share_percent').each(function (k, v){
                if($(this).val() != ''){
                  total += parseFloat($(this).val());
                }
              });
           
              if(total > 100){
                setError('input[name=co_lender_percent]', 'Total shared(%) should  not exceed more than 100%');
                return false;
              }else{
                $('#shareColenderForm').submit();
                $("#shareColenderForm button[type=submit]").attr("disabled","disabled");
              }
            }  
        });
    });
</script>
@endsection
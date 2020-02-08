@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_manual_charges')}}" target="_top">
     @csrf
     
       <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">User Name</label>
          <select class="form-control" id="user_id" name="user_id">
          <option value=""> Please Select</option>
            @foreach($customer as $row)
            <option value="{{$row->user_id}}">{{$row->user->f_name}}/{{$row->customer_id}}</option>
         @endforeach   
        </select>
         </select>
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>
      
       <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Program</label>
          <select class="form-control" id="program_id" name="program_id">
          <option value="">Please Select</option>
          @foreach($program as $key => $value)    
          <option value="{{$value->prgm_id}}">{{$value->program->prgm_name}}</option>
          @endforeach
         </select>
        </div>
      </div>

     <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Charge</label>
          <select class="form-control" id="chrg_name" name="chrg_name">
          <option value="">Please Select</option>
          @foreach($transtype as $key)    
          <option value="{{$key->id}}">{{$key->chrg_name}}</option>
          @endforeach
         </select>
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>

       <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Amount</label>
            <input type="text"  class="form-control" id="amount" name="amount" placeholder="Enter Amount" maxlength="50">
        
        </div>
      </div>
        <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name"> Date</label>
          <input type="text" readonly="readonly"  class="form-control datepicker-dis-fdate" id="charge_date" name="charge_date" placeholder="Enter Date" value="{{Carbon\Carbon::today()->format('d/m/Y')}}" >
        </div>
      </div>

      </div>
      <div class="row">
         <div class="form-group col-md-12 text-right">
              <input type="hidden"   id="id" name="id" >
              <input type="submit" class="btn btn-success btn-sm" name="add_charge" id="add_charge" value="Submit"/>
        </div>
      </div>
   </form>

</div>
@endsection
@section('jscript')
<script type="text/javascript">
        var messages = {
            get_chrg_amount: "{{ URL::route('get_chrg_amount') }}",
            token: "{{ csrf_token() }}",
 };
 
    
      //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','#chrg_name',function(){
      
      var chrg_name =  $(this).val(); 
      if(chrg_name=='')
      {
           
             $("#amount").empty();
      }
      var postData =  ({'id':chrg_name,'_token':messages.token});
       jQuery.ajax({
        url: messages.get_chrg_amount,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                      if(res.status=1)
                      {
                          $("#amount").val(res.amount);
                          $("#id").val(res.id);
                      }
                      else
                      {
                          alert('Something went wrong, Please try again');
                      }
                }
        }); 
    }); 
        
        
        
        
    $(document).ready(function () {
       $(document).on('click', 'input[name="chrg_calculation_type"]', function (e) {
          if ($(this).val() == '2') $('#approved_limit_div').show();
          else $('#approved_limit_div').hide();
        })

        $(document).on('click', 'input[name="is_gst_applicable"]', function (e) {
          if ($(this).val() == '1') $('#gst_div').show();
          else $('#gst_div').hide();
        })
        
        


       /////////////// validation the time of final submit/////////////// 
      $(document).on('click','#add_charge',function(e){
      
       if ($('form#chargesForm').validate().form()) {
           
        $("#user_id" ).rules( "add", {
        required: true,
    
        messages: {
        required: "Please select user",
        }
        });
          $("#program_id" ).rules( "add", {
        required: true,
     
        messages: {
        required: "Please select bank name",
        }
        });
          $("#chrg_name" ).rules( "add", {
        required: true,
        messages: {
        required: "Please select charge",
        }
        });
         $("#amount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter amount",
        }
        });
        $("#charge_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter date",
        }
        });      
      
        
       } else {
        /// alert();
        }  
     
    });   
    });
</script>
@endsection
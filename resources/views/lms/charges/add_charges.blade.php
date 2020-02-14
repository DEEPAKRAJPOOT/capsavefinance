@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="chargesForm" name="chargesForm" method="POST" action="{{route('save_manual_charges')}}" target="_top">
     @csrf
     
       <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">User Name</label>
          <select class="form-control" id="user_id" name="user_id" readonly="readonly">
          <option value="{{$customer->user_id}}">{{$customer->f_name}} {{$customer->l_name}}</option>
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
          @if($program)
          @foreach($program as $value)    
          <option value="{{$value->prgm_id}}">{{$value->prgm_name}}</option>
          @endforeach
          @else
           <option value="">No data found</option>
          @endif
         </select>
          <span id="msgprogram" class="error"></span>
        </div>
      </div>

     <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Charge</label>
          <select class="form-control chrg_name" id="chrg_name" name="chrg_name">
         
         </select>
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>
      </div>
      <div class="form-group col-md-6">             
          <label for="chrg_type">Charge Type</label><br>            
          <div class="form-check-inline ">              
          <label class="form-check-label fnt">               
          <input type="radio" class="form-check-input" id="chrg_calculation_type1" name="chrg_calculation_type" value="1"> &nbsp;&nbsp;Fixed </label>            
          </div>
          <div class="form-check-inline">               
              <label class="form-check-label fnt">               
                  <input type="radio" class="form-check-input" id="chrg_calculation_type2"  name="chrg_calculation_type" value="2">&nbsp;&nbsp;Percentage</label>
          </div> </div>
       <div class="row">
        <div class="form-group col-md-12">
          <label for="chrg_name">Amount/Percent</label>
            <input type="text"  class="form-control" id="amount" name="amount" placeholder="Charge Calculation Amount" maxlength="50">
        
        </div>
           <div class="form-group col-md-6" id="approved_limit_div">
             <label for="chrg_type">Charge Applicable On</label>
              <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                 
              </select>
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
          <div class="form-group col-md-6 text-left">
              
          </div>
         <div class="form-group col-md-6 text-right">
             <span  id="submitMsg" class="error"></span>
              <input type="hidden"   id="id" name="id" >
              <input type="hidden"   id="chrg_applicable_hidden_id" name="chrg_applicable_id" >
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
            get_trans_name: "{{ URL::route('get_trans_name') }}",
            token: "{{ csrf_token() }}",
 };
 
    $(document).on('change','#program_id',function(){
        $(".chrg_name").empty();
        $("#msgprogram").html('');
        var postData =  ({'prog_id':$("#program_id").val(),'_token':messages.token});
        jQuery.ajax({
        url: messages.get_trans_name,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (data) {
                    if(data.status==1)
                    {   $(".chrg_name").append('<option value="">Please select</option>'); 
                        $(data.res).each(function(i,v){
                            $(".chrg_name").append('<option value="'+v.charge.id+'">'+v.charge.chrg_name+'</option>'); 
                        });
                    }
                    else
                    {
                             $(".chrg_name").append('<option value="">No charge found</option>'); 
                       
                    }
                }
        });         
    });
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','#chrg_name',function(){
      $("#chrg_applicable_id").empty();
      $("#chrg_calculation_type1").attr('disabled',false);
      $("#chrg_calculation_type2").attr('disabled',false);
      var chrg_name =  $(this).val(); 
      if($("#program_id").val()=='') 
      {    
            
             $(this).val('');
             $("#msgprogram").html('Please select program');
             return false;
      }
      if(chrg_name=='')
      {
             $("#chrg_calculation_type1").attr('checked',false);
             $("#chrg_calculation_type2").attr('checked',false);
             $("#amount").empty();
      }
      var postData =  ({'id':chrg_name,'prog_id':$("#program_id").val(),'user_id':$("#user_id").val(),'_token':messages.token});
       jQuery.ajax({
        url: messages.get_chrg_amount,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                      if(res.status==1)
                      {
                       
                        var  applicable  = res.applicable;  
                        if(res.type==1)
                         {
                            
                             $("#approved_limit_div").hide();
                             $("#chrg_calculation_type1").attr('checked',true);
                             $("#chrg_calculation_type2").attr('disabled',true);
                         }
                         else if(res.type==2)
                         {
                             $("#approved_limit_div").show(); 
                             $("#chrg_calculation_type2").attr('checked',true);
                             $("#chrg_calculation_type1").attr('disabled',true);
                         } 
                          $("#chrg_applicable_id").html(applicable);
                          $("#chrg_applicable_hidden_id").val(res.chrg_applicable_id);
                          $("#chrg_applicable_id option").attr('disabled','disabled');
                          $("#amount").val(res.amount);
                          $("#id").val(res.id);
                      }
                      else
                      {
                         $("#chrg_name").val('');
                         alert('Something went wrong, Please try again');
                      }
                }
        }); 
    }); 
        
    $(document).on('change','#program_id_old',function(){
       var postData =  ({'prog_id':$("#program_id").val(),'_token':messages.token});
       jQuery.ajax({
        url: messages.get_trans_name,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                },
                success: function (res) {
                      alert(res)
                }
        }); 
    });      
        
        
    $(document).ready(function () {
       $("#chrg_name").html('<option value="">No data found</option>'); 
       document.getElementById('amount').addEventListener('input', event =>
        event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
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
        required: "Please select program name",
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
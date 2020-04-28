@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'userLocation'])


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage User InVoice Location</h3>
            <small>Manage User InVoice Location</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage User Invoice</li>
                <li class="active">Manage User InVoice Location</li>
            </ol>
        </div>
    </section>

    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">



                    <form action="#" method="post"  id="invoice_location">
                        @csrf
                       
                        <div class="row">
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Customer Primary Location   </label><br />
                                 <select class="form-control" name="customer_pri_loc" id="customer_pri_loc">
                                      <option disabled value="" selected>Select</option>
                                      
                                  </select>
                            </div>
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Select Capsave Location</label><br />
                                 <select class="form-control" name="capsav_location" id="capsav_location">
                                      <option disabled value="" selected>Select</option>
                                      
                                  </select>
                            </div>
                          </div>
                             <div class="form-group mb-0 mt-1 d-flex justify-content-between pull-right">
                                 <input type="submit" class="btn btn-success btn-sm" name="user_invoice_location" id="user_invoice_location" value="Submit"/>
                            </div>

                    </form>





                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('jscript')
<script type="text/javascript">
   var message = {
       token: "{{ csrf_token() }}",
       user_id: "{{ $userInfo -> user_id }}",
       get_bank_addr: "{{route('get_bank_address')}}",
   }

   $('#capsav_location').on('change', function() {
       var capsav_location = $(this).val();
       if(!capsav_location.length) {
           return false;
       };
       $.ajax({
          type:"POST",
          data: {'capsav_location' : capsav_location, '_token':message.token},
          url: message.get_bank_addr,
          success:function(data){ 
              console.log(data)
            if (data.status == 1) {
            }else{
              alert(data.message);
            }             
          }
       });
   });

</script>
@endsection
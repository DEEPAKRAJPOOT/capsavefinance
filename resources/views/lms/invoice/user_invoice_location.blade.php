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


                    <form action="{{route('save_user_invoice_location', ['user_id' => $user_id] )}}" method="post"  id="invoice_location">
                        @csrf
                        <input type="hidden" value="{{$user_id}}" name="user_id">
                        <div class="row">
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Customer Primary Location   </label><br />
                                 <select class="form-control" name="customer_pri_loc" id="customer_pri_loc">
                                      <option disabled value="" selected>Select</option>
                                      @foreach($user_addr as $addr)
                                      <option value="{{$addr->biz_addr_id}}">{{$addr->addr_1}}</option>
                                      @endforeach
                                  </select>
                            </div>
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Select Capsave Location</label><br />
                                 <select class="form-control" name="capsav_location" id="capsav_location">
                                      <option disabled value="" selected>Select</option>
                                      @foreach($capsave_addr as $addr)
                                      <option value="{{$addr->comp_addr_id}}">{{$addr->cmp_add}} {{$addr->state}}</option>
                                      @endforeach
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
       user_id: "{{ $user_id }}",
   }
</script>
<script type="text/javascript">

$(document).ready(function () {
      $('#invoice_location').validate({ // initialize the plugin
         rules: {
            'customer_pri_loc': {
               required: true,
            },
            'capsav_location': {
               required: true,
            },
         },
         messages: {
            'customer_pri_loc': {
               required: "This field is required",
            },
            'capsav_location': {
               required: "This field is required",
            },
         }
      });
   });

    // GET state id for Capsave address
   $('#capsav_location').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
        $.ajax({
           type:"GET",
           data: { "approved": "True"},
           url:"{{url('lms/get-capsav-invoice-state')}}?state="+stateID,
           success:function(data){
            if(data) {
                $.each(data,function(key,value) {
                    $('#invoice_location').append('<input type="hidden" name="capsave_state" value="' + value + '">')
                });
            }
           }
        });
    }else{
        $("#capsav_location").empty();
    }

   });

   // GET state id for user address
   $('#customer_pri_loc').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
        $.ajax({
           type:"GET",
           data: { "approved": "True"},
           url:"{{url('lms/get-user-invoice-state')}}?state="+stateID,
           success:function(data){
            if(data) {
                $.each(data,function(key,value) {
                    $('#invoice_location').append('<input type="hidden" name="user_state" value="' + value + '">')
                });
            }
           }
        });
    }else{
        $("#customer_pri_loc").empty();
    }

   });
</script>
@endsection
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
                    <div class="table-responsive ps ps--theme_default w-100" style="margin-bottom: 20px;">
                     @include('lms.customer.limit_details')
                    </div>
                    <form action="{{route('save_user_invoice_location', ['user_id' => $user_id] )}}" method="post"  id="invoice_location">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Customer Location   </label><br />
                                 <select class="form-control" name="customer_pri_loc" id="customer_pri_loc">
                                      <option disabled value="" selected>Select Customer Location</option>
                                      @foreach($user_addr as $u_addr)
                                      <option state="{{$u_addr->state_id}}" value="{{$u_addr->biz_addr_id}}">{{$u_addr->addr_1}} {{$u_addr->addr_2}} {{$u_addr->city_name}} {{$u_addr->state_name}} {{$u_addr->pin_code}}</option>
                                      @endforeach
                                  </select>
                                  <input type="hidden" name="user_state" id="user_state" value="">
                            </div>
                            <div class="form-group col-md-6">
                                 <label for="entity_type">Company Location</label><br />
                                 <select class="form-control" name="capsav_location" id="capsav_location">
                                      <option disabled value="" selected>Select Company Location</option>
                                      @foreach($capsave_addr as $c_addr)
                                      <option state="{{$c_addr->state_id}}" value="{{$c_addr->comp_addr_id}}">{{$c_addr->cmp_add}} {{$c_addr->city}}, {{$c_addr->state_name}} {{$c_addr->pincode}}</option>
                                      @endforeach
                                  </select>
                                  <input type="hidden" name="capsave_state" id="capsave_state" value="">
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

    <div class="row">
        <div class="col-sm-12">
            <table id="cusCapLoc_list" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoices_list_info" style="width: 100%;">
            <thead>
                <tr role="row">
                <th>#</th>
                <th>Customer Location</th>
                <th>Customer State</th>
                <th>Capsave Location</th>
                <th>Capsave State</th>
                <th>Created Date</th>
                <th>Status</th> 
                </tr>
            </thead>
            <tbody>

            </tbody>
            </table>
            <div id="cusCapLoc_list_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
        </div>
    </div>

</div>


@endsection
@section('jscript')
<script type="text/javascript">
   var message = {
       token: "{{ csrf_token() }}",
       user_id: "{{ $user_id }}",
       data_not_found: "{{ trans('error_messages.data_not_found') }}",
       get_cust_and_cap_loca: "{{ URL::route('get_cust_and_cap_loca') }}",
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

  $(document).on('change', '#capsav_location', function () {
   let state_id =  $(this).find("option:selected").attr('state');
   $('#capsave_state').val(state_id);
  })
  
  $(document).on('change', '#customer_pri_loc', function () {
   let state_id =  $(this).find("option:selected").attr('state');
   $('#user_state').val(state_id);
  })
</script>
<script type="text/javascript" src="{{ asset('backend/js/ajax-js/lms/cusCapLocation.js') }}"></script>
@endsection
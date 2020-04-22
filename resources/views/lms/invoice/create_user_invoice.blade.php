@extends('layouts.backend.admin-layout')

@section('content')


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Create User Invoice</h3>
            <small>Create User Invoice</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">View User Invoice</li>
                <li class="active">Create User Invoice</li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-fields">
                        <div class="active" id="details">

                        

                        <form id="userInvoice" name="userInvoice" method="POST" action="url{{ route('save_user_invoice') }}" target="_top">
                        @csrf

                            <div class="table-responsive ps ps--theme_default w-100">

                                <table class="table border-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-left border-0" width="30%"> <b>Billing Address</b> </td>
                                            <td class="text-right border-0" width="30%"> <b>Original Of Recipient</b> </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                            <input type="hidden" value="{{$userInfo->user_id}}" id="userID">


                                <table class="table border-0">
                                    <tbody>
                                        <tr>
                                            <!-- USER -->
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="state_id">State Name</label>
                                                        <select class="form-control" name="state_id" id="state_id">
                                                            <option disabled value="" selected>Select State</option>
                                                            @foreach($state_list as $state)
                                                            <option value="{{$state->state_code}}">{{$state->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="app_id">Applications</label>
                                                        <select class="form-control" name="app_id" id="app_id">
                                                            <option disabled value="" selected>Select Application</option>
                                                            @foreach($appInfo as $ad_id) 
                                                            <option value="{{$ad_id->app_id}}">{{$ad_id->business->biz_entity_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="biz_gst_no">GSTIN</label>
                                                        <select class="form-control" name="biz_gst_no" id="biz_gst_no">
                                                            <option disabled value="" selected>Select GSTIN</option>
                                                            @foreach($gstInfo as $gstn)
                                                                <option value="{{$gstn->pan_gst_hash}}">{{$gstn->pan_gst_hash}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-4" style="margin-left: 25px;">
                                                        <a href="javascript:void(0);" class="invoice-state"><i style="color: #FFF;" id="state_abbr">MH</i></a>
                                                        <label>City Code</label>
                                                        <input type="text" class="form-control" id="invoice_city" name="invoice_city" placeholder="City Code" maxlength="5">
                                                    </div>
                                                    <div class="form-group col-4">
                                                        <label>Invoice ID</label>
                                                        <input type="text" class="form-control" tabindex="15" id="invoice_id" name="invoice_id" placeholder="Invoice ID">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="pan_no">Enter PAN Number</label>
                                                        <input type="text" class="form-control" id="pan_no" name="pan_no" placeholder="Enter PAN No" value="{{$gstInfo[0]->pan_gst_hash}}">
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="invoice_date">Invoice Date</label>
                                                        <input type="text" class="form-control dateFilter" id="invoice_date" name="invoice_date" placeholder="Invoice Date">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="gst_addr">Enter Address</label>
                                                        <input type="text" class="form-control" id="gst_addr" name="gst_addr" placeholder="Enter Address">
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="comp_id">Refrence No</label>
                                                        <input type="text" class="form-control" id="comp_id" name="comp_id" value="{{$customerID[0]->customer_id}}" placeholder="Refrence Number">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="invoce_state_code">Enter State Code</label>
                                                        <input type="text" class="form-control" id="invoce_state_code" name="invoce_state_code" placeholder="Enter State Code">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-left border-0" width="30%">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <label for="place_of_supply">Place Of Supply</label>
                                                        <input type="text" class="form-control" id="place_of_supply" name="place_of_supply" placeholder="Place Of Supply">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12 mb-0">
                                    <input type="submit" class="btn btn-success btn-sm pull-right" name="add_address" id="add_address" value="Submit" />
                                </div>
                            </div>
                            
                        </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

@endsection


@section('jscript')

<script>
    var message = {
        token: "{{ csrf_token() }}",
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {


        $('#userInvoice').validate({ // initialize the plugin
            rules: {
                'state_id': {
                    required: true,
                },
                'app_id': {
                    required: true,
                },
                'biz_gst_no': {
                    required: true,
                },
                'invoice_state': {
                    required: true,
                },
                'invoice_city': {
                    required: true,
                },
                'invoice_id': {
                    required: true,
                    digits: true,
                },
                'pan_no': {
                    required: true,
                },
                'invoice_date': {
                    required: true,
                },
                'gst_addr': {
                    required: true,
                },
                'comp_id': {
                    required: true,
                },
                'place_of_supply': {
                    required: true,
                },
                'invoce_state_code': {
                    required: true,
                },
            },
            messages: {
                'state_id': {
                    required: "This field is required",
                },
                'app_id': {
                    required: "This field is required",
                },
                'biz_gst_no': {
                    required: "This field is required",
                },
                'invoice_state': {
                    required: "This field is required",
                },
                'invoice_city': {
                    required: "This field is required",
                },
                'invoice_id': {
                    required: "This field is required",
                },
                'pan_no': {
                    required: "This field is required",
                },
                'invoice_date': {
                    required: "This field is required",
                },
                'gst_addr': {
                    required: "This field is required",
                },
                'comp_id': {
                    required: "This field is required",
                },
                'place_of_supply': {
                    required: "This field is required",
                },
                'invoce_state_code': {
                    required: "This field is required",
                },
            }
        });
    });
</script>

<script>
    let invoice_id = document.getElementById('invoice_id');
    let invoice_city = document.getElementById('invoice_city');

    invoice_id.addEventListener('input', function() {
        let pinVal =  document.getElementById('invoice_id').value;
        let pinStr = pinVal.toString();

        if (isNaN(invoice_id.value) || pinStr.length >= 4) {
            invoice_id.value = "";
        }
    });
    invoice_city.addEventListener('input', function() {
        let pinVal =  document.getElementById('invoice_city').value;
        let pinStr = pinVal.toString();

        if (isNaN(invoice_city.value) || pinStr.length >= 4) {
            invoice_city.value = "";
        }
    });
</script>

<script type="text/javascript">

    $('#state_id').on('change',function(){
    var stateID = $(this).val();
    var state = $("#state_id :selected").text()
    console.log(state)
    var place_of_supply = $('#place_of_supply');
        $('#state_abbr').empty();
    if(stateID) {
        $('#state_abbr').append(stateID);
        $('#place_of_supply').val(state);
        $("#place_of_supply").next().remove();
    }

   });

//    Date picker
   $(document).ready(function(){
        $("#invoice_date").datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView : 2,
            //endDate: new Date()
        });


        var nowDate = new Date(); 
        var currentDate = nowDate.getDate() +'/'+ (nowDate.getMonth()+1) +'/'+ nowDate.getFullYear();

        var oneMonthAddedDate = new Date();
        oneMonthAddedDate.setMonth( oneMonthAddedDate.getMonth() + 1 );
        var currentAddedDate  = oneMonthAddedDate.getDate() +'/'+ (oneMonthAddedDate.getMonth()+1) +'/'+ oneMonthAddedDate.getFullYear();

        $("#invoice_date").val(currentDate);
     
   });
</script>


<script>
    $('#biz_gst_no').on('change', function() {
        var gstin = $(this).val();
        var userID = $('#userID').val();
        if(!gstin.length) {
            return false;
        };

        $.ajax({
           type:"GET",
           data: { "approved": "True"},
           url:"{{url('/lms/get-biz-add-user-invoice')}}?user_id="+userID,
           success:function(data){ 
            if(data){
                $('#gst_addr').val(data);
                $('#gst_addr').next().remove();
            } else {
                $('#gst_addr').val();
            }
           }
        });
    });
</script>
<script>
    $('#state_id').on('change', function() {
        var state_id = $(this).val();
        if(!state_id.length) {
            return false;
        };

        $.ajax({
           type:"GET",
           data: { "approved": "True"},
           url:"{{url('/lms/get-user-state-code')}}?state_code="+state_id,
           success:function(data){ 

                $.each(data, function(key, value) {
                    console.log(key);
                   if(data) {
                       $('#invoce_state_code').val(key)
                       $("#invoce_state_code").next().remove();
                   }
                });
               
           }
        });
    });
</script>
@endsection
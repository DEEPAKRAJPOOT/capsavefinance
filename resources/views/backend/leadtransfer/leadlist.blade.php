
@extends('layouts.backend.admin-layout')

@section('content')


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Transfer Lead</h3>
            <small>Assign Lead</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Transfer Lead</li>
                <li class="active">Assign Lead</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                    <label for="" class="">Select Role<span class="error_message_label">*</span></label>	
                    <select class="form-control" name="selectedrole" id="selectedrole" >
                        <option value="">Select Role</option>
                        @foreach($roles as $userRole)
                        <option value="{{$userRole['id']}}" >{{$userRole['name']}}</option>
                        @endforeach 
                    </select>
                    <span class="text-danger error" id="role_error"></span>
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="" class="">Select User<span class="error_message_label">*</span></label>	
                        <select class="form-control" name="selecteduser" id="selecteduser" >
                            <option value="">Select user</option>
                        </select>
                        <span class="text-danger error" id="user_error"></span>
                    </div>
                </div> 
                <input type="hidden" value="" id="hiddenRoleid">
                <input type="hidden" value="" id="hiddenUserid">
                <div class="col-md-1">
                    <button type="button" id="leadsearchbtn" class="btn btn-success btn-sm float-right">Search Leads</button>
                </div>    
                
                <div class="col-sm-5">
                    <a data-toggle="modal" data-target="#assignUserLeads" data-url="{{route('assign_user_leads')}}" data-height="400px" data-width="100%" data-placement="top">
                        <button type="button" id="assignedhbtn" class="btn btn-success btn-sm float-right" disabled style="padding: 10px;margin: 21px;line-height: 25px;">
                            <span class="btn-label">
                                <i class="fa fa-exchange"></i>
                            </span>
                            Assign Leads
                        </button>
                    </a> 
                </div>
                 </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="assignleadMaster" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th style="width: 2%;">Select Lead.</th>
                                        <th>Lead.ID.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Anchor</th>
                                        <th>User Type</th>
                                        <th>Assignee Detail</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            
                            <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
        </div>
      </div>
    </div>
{!!Helpers::makeIframePopup('assignUserLeads','Assign Leads', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editAnchorFrm','Edit Anchor Detail', 'modal-lg')!!}
{!!Helpers::makeIframePopup('add_bank_account','Add Bank Detail', 'modal-lg')!!}
{!!Helpers::makeIframePopup('edit_bank_account','Edit Bank Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
</script>
<script>

    var messages = {
        get_assigned_user_lead:"{{ URL::route('get_assigned_user_lead') }}",
        get_lead: "{{ URL::route('get_lead') }}",
        set_users_leads : "{{URL::route('set_users_leads')}}",
        get_backend_users: "{{ URL::route('get_backend_users') }}",
        get_users_leads:"{{URL::route('get_users_leads')}}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script>
    var selected = new Array();
  $('#selectedrole').on('change', function() {
    var selectedroleId = $(this).find(":selected").val();
    var lenderSelect = $('#selecteduser');
    $('#hiddenRoleid').val($(this).val());
    $.ajax({
                url  : messages.get_backend_users,
                type :'POST',
                data : {role_id : selectedroleId, _token : messages.token},
                beforeSend: function() {
                    //$(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    console.log(result);
                    if(result.status == '1'){

                        $('#role_error').text('');
                        $('#user_error').text('');
                        lenderSelect.empty();
                        $('#leadsearchbtn').attr('disabled',false);
                        var option = new Option("Select user", "", true, true);
                        lenderSelect.append(option);
                        $.each(result.data, function (i, item) { 
                            var option = new Option(item,i, false, false);
                            lenderSelect.append(option);  
                        });

                    }else if(result.status == '2'){
                        lenderSelect.empty();
                        var option = new Option("Select user", "", true, true);
                        lenderSelect.append(option);
                        $('#leadsearchbtn').attr('disabled',true);
                        $('#user_error').text('');
                        $('#role_error').text(result.message);

                    }else{
                        lenderSelect.empty();
                        var option = new Option("Select user", "", true, true);
                        lenderSelect.append(option);
                        $('#role_error').text('');
                        $('#leadsearchbtn').attr('disabled',true);
                        $('#user_error').text(result.message);
                    }
                },
                error:function(error) {

                },
                complete: function() {
                    //$(".isloader").hide();
                },
        })
   });
</script>

<script>
    $('#selecteduser').on('change', function() {
        selected = new Array();
        $('#hiddenUserid').val($(this).val());
        $('#searchbtn').attr('disabled',false);
        $('#role_error').text('');
        $('#user_error').text('');
    });
    var oTables3;var role_id =0;var user_id =0;
jQuery(document).ready(function ($) {
    
    if(oTables3 != undefined){
            oTables3.destroy();
    }
    fillTable(role_id,user_id);
    $('#leadsearchbtn').on('click', function() {
         role_id = $('#hiddenRoleid').val();
         user_id = $('#hiddenUserid').val();
        console.log(oTables3);
        if(oTables3 != undefined){
            oTables3.destroy();
        }
        
        fillTable(role_id,user_id);
    });

    
});
    function fillTable(role_id,user_id){

        $.ajax({
                url  : messages.get_users_leads,
                type :'POST',
                data : {role_id : role_id,user_id:user_id, _token : messages.token},
                beforeSend: function() {
                    //$(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    if(result.status === '1'){
                        console.log(role_id);
                        console.log(user_id);
                        oTables3 = $('#assignleadMaster').DataTable({
                        processing: true,
                        serverSide: true,
                        pageLength: 10,
                        searching: false,
                        bSort: false,
                        "dom": '<"top">rt<"bottom"flpi><"clear">',
                        ajax: {
                        "url": messages.get_assigned_user_lead, // json datasource
                            "method": 'POST',
                            data: function (d) {
                                d.role_id = $('#hiddenRoleid').val();
                                d.user_id = $('#hiddenUserid').val();
                                d._token = messages.token;
                            },
                            "error": function () {  // error handling
                            
                                $("#assignleadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                                $("#leadMaster_processing").css("display", "none");
                            }
                        },
                        columns: [
                                    {data: 'checkbox'},
                                    {data: 'id'},
                                    {data: 'name'},
                                    {data: 'email'},
                                    {data: 'mobile_no'},
                                    {data: 'anchor'},
                                    {data: 'userType'},
                                    {data: 'salesper'},
                                    {data: 'active'},
                                    //{data: 'biz_name'},
                                    {data: 'created_at'},
                                ],
                        aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6,7]}]
            
                    });
    
                   oTables3.draw();

                    }else if(result.status === '2'){
                        $('#user_error').text('');
                        $('#role_error').text('');
                        if(result.data.hasOwnProperty('role_id')){
                            $('#role_error').text(result.data.role_id[0]);
                        }
                        if(result.data.hasOwnProperty('user_id')){
                            $('#user_error').text(result.data.user_id[0]);
                        }
                        $('#searchbtn').attr('disabled',true);
                    }else{

                        console.log('Not founds !');
                    }
                },
                error:function(error) {

                    
                },
                complete: function() {
                    //$(".isloader").hide();
                },
        })
    }
    function selectLeadToassign(element){
        var leaduser_id = $(element).val();
        var prevassigneduser_id = $('#hiddenUserid').val();
        var currentRoleId = $('#hiddenRoleid').val();
        if ($(element).is(":checked")) {
                selected.push($(element).val());
        }else{

            for(var i = 0; i<selected.length;i++) {
                if (selected[i] == $(element).val()) {
                    // remove the item from the array
                    selected.splice(i, 1);
                }
            }
        } 
      if(selected.length > 0){
        $('#assignedhbtn').attr('disabled',false);
      }else{
        $('#assignedhbtn').attr('disabled',true);
      }
        
        $.ajax({
                url  : messages.set_users_leads,
                type :'POST',
                data : {role_id : currentRoleId,assigneduser_id:prevassigneduser_id,selected_leads:selected, _token : messages.token},
                beforeSend: function() {
                    //$(".isloader").show();
                },
                dataType : 'json',
                success:function(result) {
                    console.log(result);
                },
                error:function(error) {

                    
                },
                complete: function() {
                    //$(".isloader").hide();
                },
        })    
    }
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}""></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}?v="{{Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'd-m-Y h:i A')}}"" type="text/javascript"></script>
@endsection
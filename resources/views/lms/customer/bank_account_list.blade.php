@extends('layouts.backend.admin-layout')

@section('content')

@include('layouts.backend.partials.admin_customer_links',['active'=>'bank'])
<div class="content-wrapper">
    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
            <section class="content-header">
                <div class="header-icon">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                </div>
                <div class="header-title">
                    <h3 class="mt-2">Bank Account</h3>

                    <ol class="breadcrumb">
                        <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Bank Account</li>
                    </ol>
                </div>
                <div class="clearfix"></div>
            </section>
            <div class="row">
                <div class="col-sm-12">
                    <div class="head-sec">
                        @can('add_bank_account')
                        <a data-toggle="modal" 
                           title="Add Bank" 
                           data-height="400px" 
                           data-width="100%" 
                           data-target="#add_bank_account"
                           id="register" 
                           data-url="{{ route('add_bank_account', ['user_id' => request()->get('user_id')]) }}" >
                            <button class="btn  btn-success btn-sm float-right mb-3" type="button">
                                + Add Bank
                            </button>
                        </a>
                        @endcan

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <table id="bank_listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Acc. Holder Name </th>
                                        <th>Acc. Number</th>
                                        <th>Bank Name</th>
                                        <th>IFSC Code</th>
                                        <th>Branch Name </th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{!!Helpers::makeIframePopup('add_bank_account','Add Bank', 'modal-lg')!!}
@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<script>

var messages = {
    get_bank_account_list: "{{ URL::route('get_bank_account_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    set_default_account : "{{ URL::route('set_default_account') }}",
   

};



</script>

<script>



    try {
        jQuery(document).ready(function ($) {
            var bank_listing = $('#bank_listing').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                searching: false,
                bSort: true,
                ajax: {
                    url: messages.get_bank_account_list,
                    method: 'POST',
                    data: function (d) {
                        d._token = messages.token;

                    },
                    error: function () { // error handling

                        $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                        $("#leadMaster_processing").css("display", "none");
                    }
                },
                columns: [
                    {data: 'acc_name'},
                    {data: 'acc_no'},
                    {
                        data: 'bank_name'
                    },

                    {
                        data: 'ifsc_code'
                    },
                    {
                        data: 'branch_name'
                    },

                    {
                        data: 'is_active'
                    },
                    {
                        data: 'action'
                    }
                ],
                aoColumnDefs: [{
                        'bSortable': false,
                        'aTargets': [6]
                    }]

            });



            window.reloadDataTable = function ()
            {
                bank_listing.draw();
            }




            $(document).on('click', '.make_default', function () {
                var currentValue = ($(this).prop('checked')) ? 1 : 0;
                var acc_id = $(this).data('rel');
                $.confirm({
                    title: 'Confirm!',
                    content: 'Are you sure to Make Default?',
                    buttons: {
                        Yes: {
                            action: function () {
                                jQuery.ajax({
                                    url: messages.set_default_account,
                                    data: {bank_account_id: acc_id, _token: messages.token , value: currentValue },
                                    'type': 'POST',
                                    beforeSend: function () {
                                       $('.isloader').show();
                                   },
                                    success: function (data) {
                                        $('.isloader').hide();
                                        window.reloadDataTable();
                                    }
                                });
                            }

                        },
                        Cancel: {
                            action: function () {
                                window.reloadDataTable();
                            }
                        },
                    },

                });
            });


        });
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }





</script>
@endsection
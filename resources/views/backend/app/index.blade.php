@extends('layouts.backend.admin-layout')

@section('content')

 @include('layouts.backend.partials.admin-sidebar')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
<style>
select[name='leadMaster_length']{
    height: calc(1.8125rem + 2px);
    margin: 0 10px 0 10px;
    width: 100px;
}
input[type='search']{
    height: calc(1.8125rem + 2px);
    display: inline;
    position: absolute;
    border: 1px solid rgba(0, 0, 0, 0.15);
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Application</h3>            
            <ol class="breadcrumb">
                <li><a href="https://admin.zuron.in/admin/dashboard"><i class="mdi mdi-home"></i> Home</a></li>
                <li class="active">Manage Application</li>
            </ol>
        </div>
    </section>
</div>



    


    @endsection
    
    @section('jscript')
    <script>

        var messages = {
            get_lead: "{{ URL::route('get-lead') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",

        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
    @endsection
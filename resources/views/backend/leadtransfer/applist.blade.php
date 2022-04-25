@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Transfer Lead</h3>
            <small>Assign Cases</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Transfer Lead</li>
                <li class="active">Assign Cases</li>
            </ol>
        </div>
    </section>
    </div>
{!!Helpers::makeIframePopup('addAnchorFrm','Add Anchor', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editAnchorFrm','Edit Anchor Detail', 'modal-lg')!!}
{!!Helpers::makeIframePopup('add_bank_account','Add Bank Detail', 'modal-lg')!!}
{!!Helpers::makeIframePopup('edit_bank_account','Edit Bank Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
</script>

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
@endsection
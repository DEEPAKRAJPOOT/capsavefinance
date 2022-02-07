@extends('layouts.backend.admin_popup_layout')

@section('content')
<style>
.ps__scrollbar-x-rail {
    display: none !important;
}
</style>
<div class="modal-body table-responsive ps ps--theme_default">
    <table class="table table-striped cell-border no-footer"  cellspacing="0" width="100%">
        <tbody> 
            <tr>
                <td><b>Customer name:</b></td>
                <td>{{$fullCustName}}</td>
                <td><b>Application Id:</b></td>
                <td>{{$appId}}</td>
            </tr>
            <tr>
                <td><b>Total Customer:</b></td>
                <td>{{$tCust}}</td>
                <td><b>Total Amount:</b></td>
                <td>{{$tAmt}}</td>
            </tr>
            <tr>
                <td><b>Total Invoices:</b></td>
                <td>{{$tInv}}</td>
                <td><b>Invoices No:</b></td>
                <td>{{$invNoString}}</td>
            </tr>
            @if($res_text)
            @php $bankName = ($bankType == 1)?'IDFC':'KOTAK'; @endphp
            <tr>
                <td><b>{{ $bankName }} API Response:</b></td>
                <td colspan="3">
                    <div style="width:500px;white-space:initial;">{{$res_text}}</div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    </br>
    <div class="row">
        <div class="col-md-3 offset-sm-9">
            <form action="{{route('rollback_disbursal_batch_request')}}" method="post" target="_top" onsubmit="return confirm('Are you sure you want to rollback the request?');">
            @csrf
                <input type="hidden" name="disbursal_batch_id" value="{{$disbursal_batch_id}}" />
                <input type="submit" class="btn btn-primary btn-sm ml-4" value="Rollback Request" />
            </form>
        </div>
    </div>
</div>
@endsection
@section('jscript')
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursal_batch_request.js') }}"></script>
@endsection
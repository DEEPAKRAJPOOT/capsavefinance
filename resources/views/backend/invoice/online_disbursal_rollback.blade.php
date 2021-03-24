@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
    <table class="table table-striped cell-border no-footer"  cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
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
        </tbody>
    </table>
    </br>
    <div class="row">
        <div class="col-sm-12 offset-sm-10">
            <form action="{{route('rollback_disbursal_batch_request')}}" method="post">
            @csrf
                <input type="hidden" name="disbursal_batch_id" value="{{$disbursal_batch_id}}" />
                <input type="submit" class="btn btn-primary" value="Submit" />
            </form>
        </div>
    </div>
        <!-- <button class="btn btn-action-btn btn-sm"  title="Online Disbursal Rollback" tAmt="'.$tAmt.'" tInv="'.$tInv.'" tCust="'.$tCust.'" appId="'.$appId.'" fullCustName="'.$fullCustName.'" inv_no="'.$invNoString.'" onclick="disbursal_rollback(\''. route('rollback_disbursal_batch_request',$disbursal_batch_id) .'\',this)" ><i class="fa fa-undo"></i></button> -->
@endsection
@section('jscript')
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursal_batch_request.js') }}"></script>
@endsection
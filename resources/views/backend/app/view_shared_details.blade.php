@extends('layouts.backend.admin_popup_layout')

@section('content')
    <div class="row">     
        <div class="col-12 dataTables_wrapper mt-4">
            <div class="overflow">
                <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="approver" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Assigned By</th>
                                        <th>Assigned To</th>
                                        <th>Comment</th>
                                        <th>Assigned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($approvers) != 0)
                                    @foreach($approvers as $approver)
                                    <tr>
                                        <td>{{ $approver['assignby'] }} @if($approver['assignby_role'])<small> ( {{ $approver['assignby_role'] }} )</small>@endif</td>
                                        <td>@if($approver['assignto']) {{ $approver['assignto'] }} @else Application Pool @endif @if($approver['assignto_role'])<small> ( {{ $approver['assignto_role'] }} )</small>@endif</td>
                                        <td>{{ $approver['sharing_comment'] }}</td>
                                        <td>{{ $approver['assigne_date'] }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4"> No record found </td>
                                    </tr>
                                    @endIf
                                </tbody>
                            </table>
                            <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jscript')
<script>
/*   
try {
    jQuery(document).ready(function ($) {
        $('#approver').DataTable({ 
           
            "aaSorting": []
        });
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}    
*/       
</script>
@endsection  
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
                                        <th>Approver Name</th>
                                        <th>Email</th>
                                        <th>Approved Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($approvers) != 0)
                                    @foreach($approvers as $approver)
                                    <tr>
                                    <td>{{ $approver['approver'] }} {{--<small> ( {{ $approver['approver_role'] }} )</small>--}}</td>
                                    <td>{{ $approver['approver_email'] }}</td>
                                    <td>{{ $approver['approved_date'] }}</td>
                                    <td>{{ $approver['stauts'] }}</td>
                                    </tr>
                                    @endforeach
                                    @else

                                    
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
</script>
@endsection  
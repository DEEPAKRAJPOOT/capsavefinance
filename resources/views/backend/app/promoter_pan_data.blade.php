@extends('layouts.confirm-layout')

@section('content')



 <table class="table  table-striped table-hover overview-table">
        <thead class="thead-primary">
            <tr>
                <th width="10%" class="text-left" colspan="2">PAN Status Detail</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Status of the PAN</th>
                <td>Active</td>
            </tr>

            <tr>
                <th>PAN has been tagged as duplicate by Income Tax Department(ITD)</th>
                <td>No</td>
            </tr>
            <tr>
                <th>Given Name matches with the ITD Records</th>
                <td>Yes</td>
            </tr>
            <tr>
                <th>Given DOB matches with the ITD Records</th>
                <td>Yes</td>
            </tr>
        </tbody>
    </table>
 
 
 
@endsection

@section('jscript')

<script>
    var messages = {
        is_accept: "{{ Session::get('is_accept') }}",
    };
    $(document).ready(function() {

        if (messages.is_accept == 1) {
            var parent = window.parent;
            parent.jQuery("#pickLead").modal('hide');
            //window.parent.jQuery('#my-loading').css('display','block');

            parent.oTable1.draw();
            //window.parent.location.href = messages.paypal_gatway;
        }

    })
</script>
@endsection
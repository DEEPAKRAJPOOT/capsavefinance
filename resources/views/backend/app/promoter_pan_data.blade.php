@extends('layouts.confirm-layout')

@section('content')


 <table class="table  table-striped table-hover overview-table">
    @if($res != null)
        <thead class="thead-primary">
            <tr>
                <th width="10%" class="text-left" colspan="2">PAN Status Detail</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Name of the PAN</th>
                <td>{{isset($res->name) ? $res->name : '' }}</td>
            </tr>

          
        </tbody>

    @else 
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">PAN Not Verified</th>
        </tr>
    </thead>
    @endif
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
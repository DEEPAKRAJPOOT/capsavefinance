@extends('layouts.confirm-layout')

@section('content')

@if(count($res) > 0)
<table class="table  table-striped table-hover overview-table">
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">Passport Detail</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Given Name</th>
            <td>Suresh</td>
            <th>Name Match</th>
            <td>-</td>
        </tr> <tr>
            <th>Surname</th>
            <td>Kumar</td>
            <th>Name Match Score</th>
            <td>-</td>
        </tr> <tr>
            <th>Passport Number (from source)</th>
            <td>L7259111</td>
            <th>Match</th>
            <td>-</td>
        </tr> <tr>
            <th>Dispatched On (from Source)</th>
            <td>6/3/2014</td>
            <th>Match</th>
            <td>-</td>
        </tr> <tr>
            <th>Type of Application</th>
            <td>Normal</td>
            <th>Passport Application Date</th>
            <td>1/1/2014</td>
        </tr>                                          
    </tbody>
</table>   
 @else
<h5>       Verification not found due to some stuck response from Api......
                   </h5>
@endif

@endsection

@section('jscript')

<script>
    var messages = {
        is_accept: "{{ Session::get('is_accept') }}",
    };
    $(document).ready(function () {

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
@extends('layouts.confirm-layout')

@section('content')


<table class="table  table-striped table-hover overview-table">
    @if($res != null)
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">Passport Detail</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Given Name</th>
            <td>{{$res->name->nameFromPassport}}</td>
            <th>Name Match</th>
            <td>{{$res->name->nameScore}}</td>
        </tr> <tr>
            <th>Surname</th>
            <td>{{$res->name->surnameFromPassport}}</td>
            <th>Name Match Score</th>
            <td>{{$res->name->nameMatch}}</td>
        </tr> <tr>
            <th>Passport Number (from source)</th>
            <td>{{$res->passportNumber->passportNumberFromSource}}</td>
            <th>Match</th>
            <td>{{$res->passportNumber->passportNumberMatch}}</td>
        </tr> <tr>
            <th>Dispatched On (from Source)</th>
            <td>{{$res->dateOfIssue->dispatchedOnFromSource}}</td>
            <th>Match</th>
            <td>{{$res->dateOfIssue->dateOfIssueMatch}}</td>
        </tr> <tr>
            <th>Type of Application</th>
            <td>{{$res->typeOfApplication}}</td>
            <th>Passport Application Date</th>
            <td>{{$res->applicationDate}}</td>
        </tr>                                          
    </tbody>
    @else 
    <thead class="thead-primary">
        <tr>
            <th class="text-left" colspan="4" width="10%">Passport Not Verified</th>
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
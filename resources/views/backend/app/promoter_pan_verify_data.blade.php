@extends('layouts.confirm-layout')

@section('content')
@php if($res) { @endphp
 <table class="table  table-striped table-hover overview-table">
        <thead class="thead-primary">
            <tr>
                <th width="10%" class="text-left" colspan="2">PAN Verify Status Detail</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Status </th>
                <td>{{($res->status) ? $res->status : '' }}</td>
            </tr>
            <tr>
                <th>Duplicate</th>
                <td>{{($res->duplicate) ? 'True' : 'False' }}</td>
            </tr>
            <tr>
                <th>Name Match</th>
                 <td>{{($res->nameMatch) ? 'True' : 'False' }}</td>
            </tr>
            <tr>
                <th>Dob Match</th>
                <td>{{($res->dobMatch) ? 'True' : 'False' }}</td>
            </tr>
            
            
       
        </tbody>
    </table>
@php } @endphp
 @endsection
 
 


@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="modal-body text-left">
<div id="scollapse1" class="card-body bdr" style="padding: 0; border: 1px solid #e9ecef;">
    <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
        <thead>
            <tr role="row">
                <th width="10%" >Document Name</td>
                <th width="10%" >Document Type</td>
                <th width="10%" >Created By</td>
                <th width="10%" >Created At</td>
                <th width="10%" >View Document</td>
            </tr>
        </thead>
        <tbody>
            @foreach($offer_document as $document)
            <tr>
                <td>{{$document->file_name}}</td>
                <td>{{$document->file_type}}</td>
                <td>{{$document->f_name}} {{$document->l_name}}</td>
                <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $document->created_at)->setTimezone(config('common.timezone'))->format('Y-m-d')}}</td>
                <td><a href="{{ route('view_adhoc_file', ['file_id' => $document->file_id ])}}" title="View Document" target="_blank" class="btn btn-action-btn btn-sm float-right mr-2"> <i class="fa fa-eye" aria-hidden="true"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
 
@endsection
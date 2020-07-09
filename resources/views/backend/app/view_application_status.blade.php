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
                                        <th>Status</th>
                                        <th>Comment</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($allCommentsData)
                                    @php
                                        $statusIdArr = [
                                            43 => 'Rejected',
                                            44 => 'Cancelled',
                                            45 => 'On Hold',
                                            46 => 'Data Pending'
                                        ];
                                    @endphp
                                    @foreach($allCommentsData as $rowData)
                                    @php
                                    $date = Helpers::convertDateTimeFormat($rowData->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s');
                                    @endphp
                                    <tr>
                                        <td>{{ $statusIdArr[$rowData->status_id] }}</td>
                                        <td>{{ $rowData->note_data }}</td>
                                        <td>{{$rowData->f_name.' '.$rowData->m_name}}</td>
                                        <td>{{ $date }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4"><strong>Data Not Found</strong></td>
                                        </tr>
                                    @endif
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




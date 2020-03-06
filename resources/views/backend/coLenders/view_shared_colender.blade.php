@extends('layouts.backend.admin_popup_layout')
@section('content')
  <div class="modal-body text-left">
      <table class="table table-responsive overview-table" cellpadding="0" cellspacing="0" border="1">
          <thead>
          <tr>
              <th><b>Co-lender Name</b></th>
              <th><b>Capsave Share(%)</b></th>
              <th><b>Co-lender Share(%)</b></th>
              <th><b>Capsave Comment</b></th>
              <th><b>Co-lender Comment</b></th>
              <th><b>Co-lender Status</b></th>
              <th><b>Created at</b></th>
              <!-- <th><b>Updated at</b></th> -->
              <!-- <th><b>Action</b></th> -->
          </tr>
          </thead>
          <tbody>
          @forelse($sharedCoLenders as $key=>$sharedColender)
              <tr>
                  <td>{{$sharedColender->colender->user->f_name.' ('.$sharedColender->colender->comp_name.')'}}</td>
                  <td>{{$sharedColender->capsave_percent}}%</td>
                  <td>{{$sharedColender->co_lender_percent}}%</td>
                  <td>{{$sharedColender->capsave_comment}}</td>
                  <td>{{$sharedColender->co_lender_comment}}</td>
                  <td>{{($sharedColender->co_lender_status == 0)? 'Pending':(($sharedColender->co_lender_status == 1)? 'Accept': 'Reject')}}</td>
                  <td>{{($sharedColender->created_at)? \Carbon\Carbon::parse($sharedColender->created_at)->format('d-m-Y'): ''}}</td>
                  {{--<td>{{($sharedColender->updated_at)? \Carbon\Carbon::parse($sharedColender->updated_at)->format('d-m-Y'): ''}}</td>--}}
                  <!-- <td></td> -->
              </tr>
              @empty
              <tr>
                  <td colspan="7" style="text-align: center;">No record found</td>
              </tr>
              @endforelse
      </table>
  </div>
@endsection

@section('jscript')
@endsection
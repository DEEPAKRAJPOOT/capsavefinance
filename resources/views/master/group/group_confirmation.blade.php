@extends('layouts.backend.admin_popup_layout')

@section('content')
  <div class="modal-body text-left">

<form method="POST" id="camForm" action="{{route('save_group_linking',['userUcicId' => $userUcicId, 'old_group_id' => $old_group_id, 'appIds' => $appIds])}}">
@csrf
 <div class="row">
    <div class="col-sm-12" style="min-height:150px">
        <table class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid"  style="width: 100%;">
            <thead>
                <tr role="row">
                    <th>App Id</th> 
                    <th>App Status</th>
                </tr>
            </thead>
            <tbody>
                @if(count($GroupDetailsArray) > 0) 
                    @foreach($GroupDetailsArray as $GroupDetail)
                        <tr>
                            <th>{{$GroupDetail->app_code}}</th>
                            <th>{{$GroupDetail->status_name}}</th>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <th colspan="2">No Record Found</th>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="col-md-9">
        <div class="form-group">
            @if(count($GroupDetailsArray) > 0) 
                <span> <input type="checkbox" class="from-control" name="groupconfirm" value="1" id="groupconfirm" required>
                <span class="ml-2">Group exposure data will be reset for all above applications, Please confirm.</span>
            @else
                <span> <input type="checkbox" class="from-control" name="groupconfirm" value="2" id="groupconfirm" required>
                <span class="ml-2">Group exposure data will not impact any applications, Please confirm.</span>
            @endif
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <input type="hidden" value="" name="group_id" id="group_id">
        <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
    </div>
</div>
</form>
  </div>
@endsection


@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp

@section('jscript')
@if($operation_status == config('common.YES'))
<script>
    try {
    var p = window.parent;
    p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
    p.jQuery("#groupConfirmation").modal('hide');
    p.location.reload();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
<script>
  var inputElement = window.parent.document.getElementById("groupsubmit");
  var inputValue = inputElement.getAttribute("data-id");
  $('#group_id').val('').val(inputValue);



  $(document).ready(function () {
    $("#groupconfirm").click(function() {
        if($("#groupconfirm").is(':checked')) {
            $("#submitbutton").removeAttr("disabled"); ;  
        }  else {
            $("#submitbutton").attr("disabled", "disabled");
        }
    });
    });

  
    



</script>


@endsection            

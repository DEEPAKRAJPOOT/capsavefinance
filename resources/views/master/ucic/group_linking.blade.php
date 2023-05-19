@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    @include('master.ucic.tab_nav')
    <div class="inner-container">
        <div class="card mt-4">
            <div class="card-body ">
             <form method="POST" id="camForm" action=""> 
             @csrf
                <div class="data mt-4">
                    <h2 class="sub-title bg">Group Company Exposure
                    </h2>
                    <div class="col-md-12 mt-4">
                         <div class="row mb-4">
                            <div class="col-md-2">
                                <label for="txtPassword"><b>Group Name</b></label>
                                <span style="color: red; font-size: 20px"> * </span>
                            </div>
                            <div class="col-md-4">
                                <select name="group_id" class="form-control group-company" id="groupselect">
                                    @if(count($allNewGroups))
                                    <option value="">Please Select Group</option>
                                    @endif
                                    @forelse($allNewGroups as $allNewGroup)
                                    <option value="{{ $allNewGroup->group_id }}" {{ isset($data['group_id']) && $data['group_id'] == $allNewGroup->group_id ? 'selected' : ''}}>{{ $allNewGroup->group_name }}</option>
                                    @empty
                                    <option value="">No Groups Found</option>
                                    @endforelse
                                </select>
                            </div>
                            <span  class="group_nameId" style="color:red;"></span>                    
                        </div>
                     </div>    
                </div>  

                @can('save_group_linking')
                    <a  id="popUpAnchor" title="Group Change Confirmation" href="#" data-toggle="modal" data-target="#groupConfirmation" data-url="{{route('group_confirmation_change', ['group_id' => $data['group_id'], 'new_group_id' => 0,'userUcicId' => $data['user_ucic_id']]) }}" data-height="250px" data-width="100%" data-placement="top" class="btn btn-sm">
                    <button class="btn btn-success pull-right mt-3" type="button" id="groupsubmit" data-id="" > Save</button> 
                    </a>
                @endcan
              </form>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('groupConfirmation','Group Change Confirmation', 'modal-lg')!!}
@endsection
@section('jscript')
<script type="text/javascript">

    $(document).on('submit', '#camForm', function(e) {
        $('.group_nameId').text(" ");
        $filledInput = 0;
    
        $('#ptpq-block input').each(function () {
            if ($(this).val()) $filledInput++;
        });
        if ($filledInput > 1 && !$('select[name="group_id"]').val()) {
            $('.group_nameId').text("Group Name is required.");       
            $('select[name="group_id"]').focus();
            return false;
        }
    
        return true;
    });

    $(document).ready(function () {
        $('#camForm').validate({ // initialize the plugin
            rules: {
                'group_id' : {
                    required : true,
                },
            },
            messages: {
                'group_id': {
                    required: "Please enter Group Name",
                },
            }
        });

        $('#groupsubmit').on('click',function() {
            var new_group_id = $('#groupselect').val();
            var old_group_id = "{{ $data['group_id'] }}";
            if(new_group_id == "" ) {
                alert("Please select Group Name");
                return false;
            }
            if(new_group_id == old_group_id) {
                alert("Please change group name, if you want to change group.");
                return false;
            }

        });

        $('#groupselect').on('change', function() {
            var new_group_id = $(this).val(); 
            let oldUrl = $("#popUpAnchor").attr('data-url');
            $("#popUpAnchor").attr('data-url',oldUrl+'&newGroupId='+new_group_id);
            $('#groupsubmit').attr("data-id",new_group_id);
        });
    });
</script>
@endsection
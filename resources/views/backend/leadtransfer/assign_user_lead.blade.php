@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
                
                {!!
                Form::open(
                array(
                'route' => 'assign_user_leads',
                'name' => 'assignUserLeads',
                'autocomplete' => 'off', 
                'id' => 'assignUserLeads',
                'target' => '_top',
                'method'=> 'POST'
                )
                )
                !!}
                    <div class="row"> 
                        <div class="col-6">
                            <div class="form-group">
                                <label for="user_role">Role
                                <span class="mandatory">*</span>
                                </label>
                               
                                <select class="form-control user_role" name="selected_role" id="user_role" disabled>
                                    <option value="">Please Select Role</option>
                                    @foreach($allCollectedData['roleList'] as $roleList)
                                    
                                      <option value="{{$roleList['id']}}" {{ ($roleList['id'] == $allCollectedData['toAssignedData']->role_id ? "selected":"") }} disabled>{{$roleList['name']}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('anchor_user_type', '<span class="error">:message</span>') !!}
                                <input type="hidden" name="user_role" value="{{$allCollectedData['toAssignedData']->role_id}}">
                            </div>
                        </div> 
                        <div class="col-6">
                            <div class="form-group">
                                <label for="role_user">Select User
                                <span class="mandatory">*</span>
                                </label>
                                <select class="form-control role_user" name="role_user" id="role_user">
                                    <option value="">Please Select User</option>
                                    @foreach($allCollectedData['roleUsers'] as $key=>$value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach    
                                </select>
                                <span class="text-danger error" id="user_error"></span>
                            </div>
                        </div>   
                    </div>
                  
                <input type="hidden" name="role_id" value="{{$allCollectedData['toAssignedData']->role_id}}">
                <input type="hidden" name="assigneduser_id" value="{{$allCollectedData['toAssignedData']->assigneduser_id}}">
                @foreach ($allCollectedData['toAssignedData']->selected_leads as $value)
                    <input type="hidden" name="selected_leads[]" value="{{$value}}">
                @endforeach
                
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAssignedLead">Submit</button>  
          {!!
        Form::close()
        !!}
         </div>
     
@endsection
@section('jscript')
<script>
   
    
    $( "#assignUserLeads" ).submit(function( event ) {
        event.preventDefault();
        var selected_user = $('#role_user').val().length;
        $('#user_error').text('');
        if(selected_user != 0){
            
            document.getElementById("assignUserLeads").submit();
        }else{
            
            $('#user_error').text('Please select user.');
            return false;
        }
    });


    $('#role_user').on('change', function() {
        $('#user_error').text('');
    });
    
 
</script>
@endsection

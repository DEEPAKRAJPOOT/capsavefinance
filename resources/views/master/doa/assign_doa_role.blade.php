@extends('layouts.backend.admin_popup_layout')

@section('content')
<div class="modal-body text-left">
{!!
Form::open(
    array(
        'route' => 'save_assign_role_level',
        'name' => 'save_assign_role_level',
        'autocomplete' => 'off', 
        'id' => 'frm_assign_role_level',
        'target' => '_top'
    )
)
!!}

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">Level Name</label>                                                            
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">{{ $levelName }}</label>                                                            
        </div>
    </div>    
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">City</label>                                                            
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">{{ $city }}</label>                                                            
        </div>
    </div>    
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">Limit Amount</label>                                                            
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">{{ $limitAmount }}</label>
        </div>
    </div>    
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> Select Role
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::select('role[]',
                $roleList,
                $doaLevelRoles,
                [
                'class' => 'form-control',                
                'id' => 'role',
                'multiple'=>'multiple'
                ])
            !!}                        
        </div>
    </div>        
</div>

{!! Form::hidden('doa_level_id', $doaLevelId) !!}
<button type="submit" class="btn btn-success btn-sm float-right">Submit</button>  
{!!
Form::close()
!!}
</div>
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>  
$(document).ready(function () {
    
    $('#frm_assign_role_level').validate({
        rules: {
            role: {
               required: true
            }            
        },
        messages: {
        }
    });            
});
</script>        
@endsection
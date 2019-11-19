@extends('layouts.backend.admin_popup_layout')

@section('content')
{!!
Form::open(
    array(
        'route' => 'save_assign_case',
        'name' => 'assignCase',
        'autocomplete' => 'off', 
        'id' => 'assignCase',
        'target' => '_top'
    )
)
!!}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="txtCreditPeriod">Select Assignee
                <span class="mandatory">*</span>
            </label>
            {!!
                Form::select('assignee',
                $assignee,
                null,
                array('class'=>'sel-assignee form-control')
                )
            !!}
        </div>
    </div>

</div>
{!! Form::hidden('app_id', $app_id) !!}
{!! Form::hidden('biz_id', $biz_id) !!}
<button type="submit" class="btn btn-primary float-right">Submit</button>  
{!!
Form::close()
!!}
@endsection

@section('jscript')
@endsection            

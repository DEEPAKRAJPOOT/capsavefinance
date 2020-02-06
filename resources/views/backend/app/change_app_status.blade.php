@extends('layouts.backend.admin_popup_layout')

@section('content')
{!!
Form::open(
    array(
        'route' => 'update_app_status',
        'name' => 'changeAppStatus',
        'autocomplete' => 'off', 
        'id' => 'changeAppStatus',
        'target' => '_top'
    )
)
!!}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="txtCreditPeriod">Status
                <span class="mandatory">*</span>
            </label>                        
            {!!
                Form::select('app_status',
                $appStatus,
                null,
                array('class'=>'sel-app-status form-control')
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

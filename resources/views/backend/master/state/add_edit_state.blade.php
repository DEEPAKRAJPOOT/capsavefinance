@extends('layouts.admin')

@section('content')
<div class="prtm-content">
    <div class="prtm-page-bar">
        <ul class="breadcrumb">
            <li class="breadcrumb-item text-capitalize">
                @if(isset($state_data->id))
                    <h3>Edit state</h3>
                    @else
                    <h3>Create state</h3>
                    @endif
                 </li>
        </ul>
    </div>

    <div class="form-validation-style">
        <div class="prtm-block">

            <div class="prtm-block-content">

                {!!
                Form::open(
                array('name' => 'ProStateMaster',
                'autocomplete' => 'off', 
                'id' => 'ProStateMaster',  
                'class' => 'form-horizontal',
                'url' => route('add_edit_state'),

                )
                ) 
                !!}
                <input type="hidden" name="state_id" id='state_id' value="{{ isset($state_data->id) ? $state_data->id : null}}">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Country Name
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            {!!
                            Form::select('country_id',
                            [''=>'Select country']+$countryDropDown->toArray(),
                            isset($state_data->country_id) ? $state_data->country_id : '',
                            array('id' => 'country_id',
                            'class'=>'form-control'))
                            !!}
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">State name
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                           {!!
                            Form::text('state_name',
                            isset($state_data->name) ? $state_data->name : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'State Name',
                            'id'=>'state_name'
                            ])
                            !!} 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Select
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            {!!
                            Form::select('status',
                            [''=>'Status', '1'=>'Active', '0'=>'In Active'],
                            isset($state_data->is_active) ? $state_data->is_active : '',
                            array('id' => 'status',
                            'class'=>'form-control'))
                            !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-success">Submit</button>
                            
                             <a href="{{ route('manage_state') }}" class="active"><span>Back State List</span></a>

                        </div>
                    </div>

                    {!!
                    Form::close()
                    !!}
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('pageTitle')
    @if(isset($state_data->id))
        Edit state
        @else
        Create state
    @endif
    @endsection
    @section('jscript')
    <script>
        var messages = {
            country_name: "{{trans('master.country.country_name')}}",
            state_name: "{{trans('master.state.state_name')}}",
            is_active: "{{trans('master.country.is_active')}}"
        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/master/state.js') }}" ></script>

    @endsection

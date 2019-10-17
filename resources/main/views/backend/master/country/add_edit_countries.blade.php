@extends('layouts.admin')

@section('content')
<div class="prtm-content">
    <div class="prtm-page-bar">
        <ul class="breadcrumb">
            <li class="breadcrumb-item text-capitalize">
                @if(isset($country_data->id))
                    <h3>Edit country</h3>
                    @else
                    <h3>Create country</h3>
                    @endif
                 </li>
        </ul>
    </div>

    <div class="form-validation-style">
        <div class="prtm-block">

            <div class="prtm-block-content">

                {!!
                Form::open(
                array('name' => 'ProCountryMaster',
                'autocomplete' => 'off', 
                'id' => 'ProCountryMaster',  
                'class' => 'form-horizontal',
                'url' => route('add_edit_countries'),

                )
                ) 
                !!}
                <input type="hidden" name="country_id" id='country_id' value="{{ isset($country_data->id) ? $country_data->id : null}}">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Name
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            {!!
                            Form::text('country_name',
                            isset($country_data->country_name) ? $country_data->country_name : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'country Name',
                            'id'=>'country_name'
                            ])
                            !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">country dode
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            {!!
                            Form::text('country_code',
                            isset($country_data->country_code) ? $country_data->country_code : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'country Code',
                            'id'=>'country_code'
                            ])
                            !!} </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Select
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            {!!
                            Form::select('status',
                            [''=>'Status', '1'=>'Active', '0'=>'In Active'],
                            isset($country_data->is_active) ? $country_data->is_active : '',
                            array('id' => 'status',
                            'class'=>'form-control'))
                            !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-success">Submit</button>
                            
                             <a href="{{ route('manage_country') }}" class="active"><span>Back Country List</span></a>

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
    Edit Country
    @endsection
    @section('jscript')
    <script>
        var messages = {
            country_name: "{{trans('master.country.country_name')}}",
            country_code: "{{trans('master.country.country_code')}}",
            is_active: "{{trans('master.country.is_active')}}"
        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/master/country.js') }}" ></script>

    @endsection

@extends('layouts.admin')

@section('content')
<div class="prtm-content">
    <div class="prtm-page-bar">
        <ul class="breadcrumb">
            <li class="breadcrumb-item text-capitalize">
                @if(isset($rt_data->id))
                    <h3>Edit rights type</h3>
                    @else
                    <h3>Create rights type</h3>
                    @endif
                 </li>
        </ul>
    </div>

    <div class="form-validation-style">
        <div class="prtm-block">

            <div class="prtm-block-content">

                {!!
                Form::open(
                array('name' => 'ProRtMaster',
                'autocomplete' => 'off', 
                'id' => 'ProRtMaster',  
                'class' => 'form-horizontal',
                'url' => route('add_edit_right_type'),

                )
                ) 
                !!}
                <input type="hidden" name="rt_id" id='cluster_data' value="{{ isset($rt_data->id) ? $rt_data->id : null}}">
                <div class="form-body">

                    <div class="form-group">
                        <label class="control-label col-md-3">Title
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                           {!!
                            Form::text('title',
                            isset($rt_data->title) ? $rt_data->title : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Title',
                            'id'=>'title'
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
                            isset($rt_data->is_active) ? $rt_data->is_active : '',
                            array('id' => 'status',
                            'class'=>'form-control'))
                            !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn btn-success">Submit</button>
                            
                             <a href="{{ route('manage_right_type') }}" class="active"><span>Back Right Type List</span></a>

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
    @if(isset($rt_data->id))
        Edit Rights Type
        @else
        Create  Rights Type
    @endif
    @endsection
    @section('jscript')
    <script>
        var messages = {
            title: "{{trans('master.cluster.title')}}",
            is_active: "{{trans('master.country.is_active')}}"
        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/master/right_type.js') }}" ></script>

    @endsection

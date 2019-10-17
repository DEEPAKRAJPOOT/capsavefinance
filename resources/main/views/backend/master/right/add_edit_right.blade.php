@extends('layouts.admin')

@section('content')
<div class="prtm-content">
    <div class="prtm-page-bar">
        <ul class="breadcrumb">
            <li class="breadcrumb-item text-capitalize">
                @if(isset($right[0]->id))
                    <h3>Edit Rights</h3>
                    @else
                    <h3>Create Source</h3>
                    @endif
                 </li>
        </ul>
    </div>
 <a href="{{ route('manage_rights') }}" class="btn btn-success "><span>Back Source List</span></a>

    <div class="form-validation-style">
        <div class="prtm-block">

            <div class="prtm-block-content">

                {!!
                Form::open(
                array('name' => 'ProRightMaster',
                'autocomplete' => 'off', 
                'id' => 'ProRightMaster',
                'class' => 'form-horizontal',
                'url' => route('add_edit_right'),

                )
                ) 
                !!}

                <div class="">

                <div class="row">
                
                <div class="col-sm-4">
                    <h4>User Name</h4>
                    <p>
                      @if(isset($right[0]))
                        {{$right[0]->first_name}} {{$right[0]->last_name}}</p>
                    @else
                    {!!
                            Form::text('user_id',
                            '2645',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Title',
                            'id'=>'title'
                            ])
                            !!}
                    @endif
                  </div>
                
                  <div class="col-sm-4">
                    <h4>Title</h4>
                   <p>
                       {!!
                            Form::text('title',
                            isset($right[0]->title) ? $right[0]->title : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Title',
                            'id'=>'title'
                            ])
                            !!}
                   </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Type Title</h4>
                    <p>

                        {!!
                            Form::select('type_id',
                            [''=>'Type of Rights*'] + Helpers::getRightTypeDropDown()->toArray(),
                            (isset($right[0]->type_id) && !empty($right[0]->type_id)) ? $right[0]->type_id : (old('type_id') ? old('type_id') : ''),
                            array('id' => 'type_id',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}



                      </p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Number</h4>
                    <p>
                        {!!
                            Form::text('number',
                            isset($right[0]->number) ? $right[0]->number : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Number',
                            'id'=>'number'
                            ])
                            !!}
                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Inventor</h4>
                   <p>
                       {!!
                            Form::text('inventor',
                            isset($right[0]->inventor) ? $right[0]->inventor : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Inventor',
                            'id'=>'inventor'
                            ])
                            !!}

                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Assignee</h4>
                    <p>
                        {!!
                            Form::text('assignee',
                            isset($right[0]->assignee) ? $right[0]->assignee : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Assignee',
                            'id'=>'assignee'
                            ])
                            !!}
                    </p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Cluster Title</h4>
                    <p>       {!!
                            Form::select('cluster_id',
                            [''=>'Type of Rights*'] + Helpers::getClusterDropDown()->toArray(),
                            (isset($right[0]->cluster_id) && !empty($right[0]->cluster_id)) ? $right[0]->cluster_id : (old('type_id') ? old('type_id') : ''),
                            array('id' => 'type_id',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Rights For id</h4>
                   <p>
                       {!!
                            Form::select('right_for_id',
                            [''=>'Type of Rights*'] + Helpers::getRightForDropDown()->toArray(),
                            (isset($right[0]->right_for_id) && !empty($right[0]->right_for_id)) ? $right[0]->right_for_id : (old('type_id') ? old('type_id') : ''),
                            array('id' => 'type_id',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                   </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Url's</h4>
                    <p>
                         {!!
                            Form::text('url',
                            isset($right[0]->url) ? $right[0]->url : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'url',
                            'id'=>'url'
                            ])
                            !!}

                    </p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Description</h4>
                    <p>
                          {!!
                            Form::text('description',
                            isset($right[0]->description) ? $right[0]->description : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'description',
                            'id'=>'description'
                            ])
                            !!}

                   </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Keywords</h4>
                   <p>
                       <input type="text" name="keywords" id="keywords" data-role="tagsinput" value="{{ (isset($right[0]->keywords) && !empty($right[0]->keywords)) ? $right[0]->keywords : (old('keywords') ? old('keywords') : '') }}" class="form-control" placeholder="keywords">
                       </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Is Trading</h4>
                    <p>

                        {!!
                            Form::select('is_trading',
                            [''=>'Please Select','0'=>'No','1'=>'Yes'] ,
                            (isset($right[0]->is_trading)) ? $right[0]->is_trading : (old('is_trading') ? old('is_trading') : ''),
                            array('id' => 'is_trading',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                       
                    </p>
                  </div>
                </div>
                <br>

                <div class="row">
                  <div class="col-sm-4">
                    <h4>Trading Type</h4>
                    <p>

                        {!!
                            Form::select('trading_type',
                            [''=>'Please Select','1'=>'Exclusive Purchage','2'=>'Non Exclusive Purchage'] ,
                            (isset($right[0]->trading_type) && !empty($right[0]->trading_type)) ? $right[0]->trading_type : (old('trading_type') ? old('trading_type') : ''),
                             array('id' => 'trading_type',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Phase</h4>
                   <p>
                       {!!
                            Form::select('phase',
                            [''=>'Please Select','0'=>'Draft','1'=>'Completed'] ,
                            (isset($right[0]->phase)) ? $right[0]->phase : (old('phase') ? old('phase') : ''),
                             array('id' => 'phase',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                     </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Is Featured</h4>
                    <p>
                        {!!
                            Form::select('is_featured',
                            [''=>'Please Select','0'=>'Normal','1'=>'Featured'] ,
                            (isset($right[0]->is_featured)) ? $right[0]->is_featured : (old('is_featured') ? old('is_featured') : ''),
                             array('id' => 'is_featured',
                            'class'=>'form-control select2Cls clsRequired'))
                        !!}
                       </p>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Date</h4>
                    <p>
                        <input type="text" value="{{(isset($right[0]->date) && !empty($right[0]->date)) ? Helpers::getDateByFormat($right[0]->date,
                        'Y-m-d', 'm/d/Y') : (old('date') ? old('date') : '')}}" name="date" id="date" class="form-control datepick clsRequired" placeholder="Date of Right*">
                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Date</h4>
                    <p>
                    <input type="text" value="{{(isset($right[0]->expiry_date) && !empty($right[0]->expiry_date)) ? Helpers::getDateByFormat($right[0]->expiry_date,
                        'Y-m-d', 'm/d/Y') : (old('expiry_date') ? old('expiry_date') : '')}}" name="expiry_date" id="expiry_date" class="form-control datepick" placeholder="Expiry Date of Right">
                    </p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Thumbnail</h4>
                    <p>
                        ffff
                       </p>
                  </div>
                </div>
              </div> 

                <input type="hidden" name="right_id" id='cluster_data' value="{{ isset($right[0]->id) ? $right[0]->id : null}}">
                @if(isset($right[0]->user_id))
                <input type="hidden" name="user_id" id='cluster_data' value="{{ isset($right[0]->user_id) ? $right[0]->user_id : null}}">
                @endif
                <div class="form-body">

                    <div class="form-group">
                        <br>
                    <div class="row">
                        <div class="col-md-offset-4 col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                           
                            
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
    @if(isset($right[0]->id))
        Edit Rights
        @else
        Create Rights
    @endif
    @endsection
    @section('addtional_css')
<link rel="stylesheet" href="{{ asset('frontend/inside/css/bootstrap-tagsinput.css') }}">
@endsection
    @section('jscript')
    <script>
        var messages = {
            title: "{{trans('master.cluster.title')}}",
            is_active: "{{trans('master.country.is_active')}}"
        };

        //Date picker
       $(function() { 
             var min = $('#date').datepicker('getDate');
              $('#expiry_date').datepicker('option', {minDate: min});

        });
         $("#date").datepicker({
            onSelect: function(date){
            var min = $(this).datepicker('getDate');
              $('#expiry_date').datepicker('option', {minDate: min});
        }
     })
    $('.datepick').each(function(){
     $(this).datepicker();
    });
     
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/master/right.js') }}" ></script>
    <script src="{{ asset('frontend/inside/js/bootstrap-tagsinput.js')}}"></script>

    @endsection

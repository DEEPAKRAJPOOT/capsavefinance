@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <!--      Wizard container        -->
        <div class="wizard-container">

            <div class="card wizard-card" data-color="orange" id="wizardProfile">

                    {!!
                        Form::open(
                        array('name' => 'ProStateMaster',
                        'autocomplete' => 'off', 
                        'id' => 'ProUpdateEducation',  
                        'class' => 'form-horizontal',
                        'url' => route('update_education'),

                        )
                        ) 
                    !!}
                    
                    <div class="tab-content">
                        <div id="account">
                            <h5 class="info-text">Update Education Details</h5>                            
                            @if(count($educations) > 0 )
                                @foreach($educations as $i => $education)
                                <input type="hidden" name="before_deleted_id[]" id='before_deleted_id' value="{{ isset($education['id']) ? $education['id'] : null}}">
                                <div class="col-md-12 clonedEducationInfo" id="clonedEducationInfo{{$i}}">
                                <div class="row education education-form-block" id="education">
                                    <input type="hidden" class="primary_id" name="after_deleted_id[{{$i}}]" id='after_deleted_id' value="{{ isset($education['id']) ? $education['id'] : null}}">
                                    <div class="inner-edit">
                                        <div class="col-sm-5">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label>University Name</label>
                                                    <div class="form-group">
                                                        {!!
                                                        Form::text('university['.$i.']',
                                                        isset($education['university']) ? $education['university'] : '',
                                                        [
                                                        'class' => 'form-control input_university',
                                                        'placeholder' => 'University Name',
                                                        'id'=>'university'.$i
                                                        ])
                                                        !!}
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Course Name</label>
                                                    <div class="form-group">
                                                       
                                                        {!!
                                                        Form::text('course['.$i.']',
                                                       isset($education['course']) ? $education['course'] : '',
                                                        [
                                                        'class' => 'form-control input_course',
                                                        'placeholder' => 'Course Name',
                                                        'id'=>'course'.$i
                                                        ])
                                                        !!}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-sm-5">
                                            <div class="col-sm-12">
                                                <label for="checkbox3">Dates Attended</label>
                                            </div>
                                            <div class="col-sm-12 small-select">
                                                <div class="row">
                                                    <div class="text-field-select col-sm-6">
                                                        @php $fromYear = Helpers::getYearDropdown() @endphp
                                                        
                                                        <div class="form-group">
                                                            {!!
                                                            Form::select('date_attended_from_year['.$i.']',
                                                            [''=>'From'] + Helpers::getYearDropdown(),
                                                            isset($education['date_attended_from_year']) ? $fromYear[$education['date_attended_from_year']] :'',
                                                            array('id' => 'date_attended_from_year'.$i,
                                                            'class'=>'form-control select2Cls input_from_year'))
                                                            !!}
                                                        </div>
                                                    </div>
                                                    <div class="text-field-select col-sm-6">
                                                        {!!
                                                        Form::select('date_attended_to_year['.$i.']',
                                                        [''=>'To']+ Helpers::getYearDropdown(),
                                                        isset($education['date_attended_from_year']) ? $fromYear[$education['date_attended_to_year']] :'',
                                                        array('id' => 'date_attended_to_year'.$i,
                                                        'class'=>'form-control select2Cls input_to_year'))
                                                        !!}
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-10">
                                            <div class="form-group ">
                                                <label>Additional information</label>
                                                <div data-error="hasError" class="textarea-field">
                                                    
                                                {!!
                                                    Form::textarea('remarks['.$i.']',
                                                    isset($education['remarks']) ? $education['remarks'] : '',
                                                    [
                                                    'class' => 'form-control input_remarks',
                                                    'placeholder' => 'Remarks',
                                                    'id'=>'remarks'.$i,
                                                    'rows' => 2, 
                                                    'cols' => 40
                                                    ])
                                                !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="remove deleteEducationInfo"  style="display: block;"><i class="fa fa-trash-o" title="Remove" aria-hidden="true"></i></div>
                            </div>
                           
                            @endforeach
                           
                            <span class="add-more-education add-slab btn btn-primary mb-2"><span>+</span> Add Education</span>
                           
                            @else
                            <div class="col-md-12 clonedEducationInfo" id="clonedEducationInfo0">
                                <div class="row education education-form-block" id="education">
                                    <div class="inner-edit">
                                        <div class="col-sm-5">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label>University Name</label>
                                                    <div class="form-group">
                                                        
                                                        {!!
                                                        Form::text('university[0]',
                                                        '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'University Name',
                                                        'id'=>'university0'
                                                        ])
                                                        !!}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Course Name</label>
                                                    <div class="form-group">
                                                       
                                                        {!!
                                                        Form::text('course[0]',
                                                        '',
                                                        [
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Course Name',
                                                        'id'=>'course0'
                                                        ])
                                                        !!}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-sm-5">
                                            <div class="col-sm-12">
                                                <label for="checkbox3">Dates Attended</label>
                                            </div>
                                            <div class="col-sm-12 small-select">
                                                <div class="row">
                                                    <div class="text-field-select col-sm-6">

                                                        <div class="form-group">
                                                            {!!
                                                            Form::select('date_attended_from_year[0]',
                                                            [''=>'From'] + Helpers::getYearDropdown(),
                                                            null,
                                                            array('id' => 'date_attended_from_year0',
                                                            'class'=>'form-control select2Cls'))
                                                            !!}
                                                        </div>
                                                    </div>
                                                    <div class="text-field-select col-sm-6">
                                                        {!!
                                                        Form::select('date_attended_to_year[0]',
                                                        [''=>'To']+ Helpers::getYearDropdown(),
                                                        null,
                                                        array('id' => 'date_attended_to_year0',
                                                        'class'=>'form-control select2Cls'))
                                                        !!}
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-10">
                                            <div class="form-group ">
                                                <label>Additional information</label>
                                                <div data-error="hasError" class="textarea-field">
                                                   {!!
                                                    Form::textarea('remarks[0]',
                                                    null,
                                                    [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Remarks',
                                                    'id'=>'remarks0',
                                                    'rows' => 2, 
                                                    'cols' => 40
                                                    ])
                                                !!}
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="remove deleteEducationInfo"  style="display: none;"><i class="fa fa-trash-o" title="Remove" aria-hidden="true"></i></div>
                            </div>
                        
                                <span class="add-more-education add-slab btn btn-primary mb-2"><span>+</span> Add Education</span>
                           
                            
                            @endif
                            
                        </div>
                    </div>
                    
                            <div class=" tab-content">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        
                {!!
                    Form::close()
                !!}
            </div>
        </div>
        <!-- wizard container -->
    </div>

    <!-- end row -->
</div>
@endsection
@section('pageTitle')
Edit Education
@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/validation/education.js') }}" type="text/javascript"></script>
@endsection


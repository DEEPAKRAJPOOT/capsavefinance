
<!--<div class="row">-->

<!--    <div class="form-group password-input">

        {!! Form::hidden('chrg_calculation_type['.$len.']', isset($data->chrg_calculation_type) ? $data->chrg_calculation_type : null) !!}
        {!! Form::hidden('chrg_type['.$len.']', isset($data->chrg_type) ? $data->chrg_type : null) !!}
        {!! Form::hidden('is_gst_applicable['.$len.']', isset($data->is_gst_applicable) ? $data->is_gst_applicable : null) !!}
    </div>-->

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-2">
                <label for="chrg_type">Charge Type</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_type == 1 ? 'checked' : ($data->chrg_type != 2 ? 'checked' : '' )}} name="chrg_type[{{$len}}]" value="1">Auto
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_type == 2 ? 'checked' : ''}} name="chrg_type[{{$len}}]" value="2">Manual
                    </label>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="is_gst_applicable">GST Applicable</label><br />
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->is_gst_applicable == 1 ? 'checked' : ($data->is_gst_applicable != 2 ? 'checked' : '' )}} name="is_gst_applicable[{{$len}}]" value="1">Yes
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->is_gst_applicable == 2 ? 'checked' : ''}} name="is_gst_applicable[{{$len}}]" value="2">No
                    </label>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="chrg_type">Charge Calculation</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_calculation_type == 1 ? 'checked' : ($data->chrg_calculation_type != 2 ? 'checked' : '' )}} name="chrg_calculation_type[{{$len}}]" value="1">Fixed
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_calculation_type == 2 ? 'checked' : ''}} name="chrg_calculation_type[{{$len}}]" value="2">Percentage
                    </label>
                </div>
            </div>
            @if(isset($data->chrg_calculation_type))
            <div class="form-group col-md-3">
                <label for="chrg_calculation_amt">Amount/Percent</label>
                {!! Form::text('chrg_calculation_amt['.$len.']', 
                    isset($data->chrg_calculation_amt)  ?  number_format($data->chrg_calculation_amt) : null, 
                    ['class'=>'form-control  number_format clsRequired','placeholder'=>"Enter  Amount" ,'required'=>'required']) !!}
            </div>
             @endif
             @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 2)
            <div class="form-group col-md-3" id="approved_limit_div">
                <label for="chrg_type">Charge Applicable On</label>
                {!!
                Form::select('chrg_tiger_id['.$len.']',
                [''=>'Please select' ,  1 => 'Limit Amount', 
                2 => ' Outstanding Amount',
                3 => 'Oustanding Principal',
                4 => 'Outstanding Interest',
                5 => 'Overdue Amount'],
                isset($data->chrg_applicable_id)  ?   $data->chrg_applicable_id  : null,
                ['id' => 'chrg_tiger_id_'.$len,
                'class'=>'form-control clsRequired ',
                'required'=>'required'
                ])
                !!}
            </div>
             @endif
        </div>
    </div>




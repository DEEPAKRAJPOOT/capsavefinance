
<!--<div class="row">-->

<!--    <div class="form-group password-input">

        {!! Form::hidden('chrg_calculation_type['.$len.']', isset($data->chrg_calculation_type) ? $data->chrg_calculation_type : null) !!}
        {!! Form::hidden('chrg_type['.$len.']', isset($data->chrg_type) ? $data->chrg_type : null) !!}
        {!! Form::hidden('is_gst_applicable['.$len.']', isset($data->is_gst_applicable) ? $data->is_gst_applicable : null) !!}
    </div>-->
    <div class="row amtpercentrow">
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
        <div class="form-group col-md-3">
            <label for="chrg_type">Charge Calculation</label><br />
            <div class="form-check-inline "><label class="form-check-label fnt"><input type="radio" class="form-check-input charge_calculation_type" {{$data->chrg_calculation_type == 1 ? 'checked' : ($data->chrg_calculation_type != 2 ? 'checked' : '' )}} name="chrg_calculation_type[{{$len}}]" value="1"  data-ct_idx="{{$len}}">Fixed</label></div>
            <div class="form-check-inline">
                <label class="form-check-label fnt"><input type="radio" class="form-check-input charge_calculation_type" {{$data->chrg_calculation_type == 2 ? 'checked' : ''}} name="chrg_calculation_type[{{$len}}]" value="2" data-ct_idx="{{$len}}">Percentage</label>
            </div>
        </div>
        @if(isset($data->chrg_calculation_type))
        <div class="form-group col-md-2 amtpercent">
            <label for="chrg_calculation_amt"><span id="sdt" class="sdt">{{isset($data->chrg_calculation_type)? (($data->chrg_calculation_type == 1)? 'Amount': 'Percent') : 'Amount'}}</span></label>
            <a href="javascript:void(0);" class="verify-owner-no"><i class="fa-change fa {{isset($data->chrg_calculation_type)? (($data->chrg_calculation_type == 1)? 'fa-inr': 'fa-percent') : 'fa-inr'}}"
            aria-hidden="true"></i></a>
            {!! Form::text('chrg_calculation_amt['.$len.']', 
                isset($data->chrg_calculation_amt)  ?  number_format($data->chrg_calculation_amt,2) : null, 
                ['id'=>'chrg_calculation_amt', 'class'=>'form-control chrg_calculation_amt '.(isset($data->chrg_calculation_type)? (($data->chrg_calculation_type == 1)? 'formatNum': 'amtpercnt') : 'formatNum').' clsRequired','placeholder'=>" " ,'required'=>'required']) !!}
        </div>
         @endif
        <div class="form-group approved_limit_div col-md-3 {{isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type != 2 ? 'hide' : '' }}" id="approved_limit_div">
            <label for="chrg_type">Charge Applicable On</label>
            {!!
            Form::select('chrg_tiger_id['.$len.']',
            [''=>'Please select' ,  1 => 'Limit Amount', 
            2 => ' Outstanding Amount',
            3 => 'Oustanding Principal',
            //4 => 'Outstanding Interest',
            //5 => 'Overdue Amount'
            ],
            isset($data->chrg_applicable_id)  ?   $data->chrg_applicable_id  : null,
            ['id' => 'chrg_tiger_id_'.$len,
            'class'=>'form-control clsRequired ',
            'required'=>'required'
            ])
            !!}
        </div>
    </div>
<script>
    
    </script>




<div class="col-md-12">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group password-input">

                {!! Form::hidden('chrg_calculation_type['.$len.']', isset($data->chrg_calculation_type) ? $data->chrg_calculation_type : null) !!}
                {!! Form::hidden('chrg_type['.$len.']', isset($data->chrg_type) ? $data->chrg_type : null) !!}
                {!! Form::hidden('is_gst_applicable['.$len.']', isset($data->is_gst_applicable) ? $data->is_gst_applicable : null) !!}
                <label for="txtPassword">Charge Calculation <span class="error_message_label">*</span>
                </label>
                <div class="block-div">

                    <p>  
                       Charge Amount/Percentage:
                    </p>
                </div>
            </div>
        </div>
        @if(isset($data->chrg_calculation_type))
        <div class="col-md-4">
            <div class="form-group password-input">
<!--                <label for="txtPassword">Amount/Percent <span class="error_message_label">* </span></label>-->
                <div class="block-div">
                    <a href="javascript:void(0);" class="verify-owner-no" style="top:12px;">
                                                                    <i class="fa fa-inr" aria-hidden="true"></i></a>
                    {!! Form::text('chrg_calculation_amt['.$len.']', 
                    isset($data->chrg_calculation_amt)  ?  number_format($data->chrg_calculation_amt) : null, 
                    ['class'=>'form-control  number_format clsRequired col-md-6','placeholder'=>"Enter  Amount" ,'required'=>'required']) !!}
                    

                </div>
            </div>
        </div>
        @endif 

        @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 2)
        <div class=" mt-3 rate row" style="">
<!--            <div class="col-md-3">

                {!! Form::text('chrg_calc_min_rate['.$len.']',
                isset($data->chrg_calc_min_rate)  ?   $data->chrg_calc_min_rate  : null
                ,['class'=>'form-control clsRequired pl-2','placeholder'=>"Min Rate" ,'required'=>'required']) !!}

            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-3">
                {!! Form::text('chrg_calc_max_rate['.$len.']',
                isset($data->chrg_calc_max_rate)  ?   $data->chrg_calc_max_rate  : null
                ,['class'=>'form-control clsRequired  pl-2','placeholder'=>"Max Rate" , 'required'=>'required']) !!}

            </div>-->


            <div class="col-md-12">
                <div class="form-group password-input">

                    <div class="block-div">
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
                </div>
            </div>
        </div>
        @endif 

        @if(isset($data->is_gst_applicable) &&  $data->is_gst_applicable == 1)

        <div class="col-md-4">
            <div class="form-group password-input">
                <label for="txtPassword">GST <span class="error_message_label">*</span></label>
                <div class="block-div">
                    {!! Form::text('gst_rate['.$len.']',isset($data->gst_percentage) ?  $data->gst_percentage : null ,
                    ['class'=>'form-control clsRequired pl-2 valid_perc percentage','placeholder'=>"Rate" ,'required'=>'required']) !!}
                </div>
            </div>
        </div>
        @endif 
    </div>

</div>

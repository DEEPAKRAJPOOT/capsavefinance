
<div class="col-md-12">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group password-input">
                <label for="txtPassword">Charge Calculation <span class="error_message_label">*</span>
                </label>
                <div class="block-div">

                    <p>  
                        @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 1)
                        Flat
                        @endif 

                        @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 2)
                        Percentage
                        @endif 
                    </p>
                </div>
            </div>
        </div>
        @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 1)
        <div class="col-md-4">
            <div class="form-group password-input">
                <label for="txtPassword">Charge Amount <span class="error_message_label">*</span></label>
                <div class="block-div">

                    {!! Form::text('chrg_calculation_amt['.$len.']','',['class'=>'form-control clsRequired col-md-6','placeholder'=>"Enter  Amount" ,'required'=>'required']) !!}

                </div>
            </div>
        </div>
        @endif 

        @if(isset($data->chrg_calculation_type) &&  $data->chrg_calculation_type == 2)
        <div class=" mt-3 rate row" style="">
            <div class="col-md-3">

                {!! Form::text('chrg_calc_min_rate['.$len.']','',['class'=>'form-control clsRequired pl-2','placeholder'=>"Min Rate" ,'required'=>'required']) !!}

            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-3">
                {!! Form::text('chrg_calc_max_rate['.$len.']','',['class'=>'form-control clsRequired  pl-2','placeholder'=>"Max Rate" , 'required'=>'required']) !!}

            </div>


            <div class="col-md-4">
                <div class="form-group password-input">

                    <div class="block-div">
                        {!!
                        Form::select('chrg_tiger_id['.$len.']',
                        [''=>'Please select'] +$applicable_data,
                        null,
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
                    {!! Form::text('gst_rate['.$len.']','',['class'=>'form-control clsRequired pl-2','placeholder'=>"Rate" ,'required'=>'required']) !!}
                </div>
            </div>
        </div>
        @endif 
    </div>
    
</div>

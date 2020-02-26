
<div class="row">

    <div class="form-group password-input">

        {!! Form::hidden('chrg_calculation_type['.$len.']', isset($data->chrg_calculation_type) ? $data->chrg_calculation_type : null) !!}
        {!! Form::hidden('chrg_type['.$len.']', isset($data->chrg_type) ? $data->chrg_type : null) !!}
        {!! Form::hidden('is_gst_applicable['.$len.']', isset($data->is_gst_applicable) ? $data->is_gst_applicable : null) !!}
    </div>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <label for="chrg_type">Charge Type</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_type == 1 ? 'checked' : ($data->chrg_type != 2 ? 'checked' : '' )}} name="chrg_type" value="1">Auto
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_type == 2 ? 'checked' : ''}} name="chrg_type" value="2">Manual
                    </label>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="chrg_type">Charge Calculation</label><br />
                <div class="form-check-inline ">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_calculation_type == 1 ? 'checked' : ($data->chrg_calculation_type != 2 ? 'checked' : '' )}} name="chrg_calculation_type" value="1">Fixed
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->chrg_calculation_type == 2 ? 'checked' : ''}} name="chrg_calculation_type" value="2">Percentage
                    </label>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="is_gst_applicable">GST Applicable</label><br />
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->is_gst_applicable == 1 ? 'checked' : ($data->is_gst_applicable != 2 ? 'checked' : '' )}} name="is_gst_applicable" value="1">Yes
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label fnt">
                        <input type="radio" class="form-check-input" {{$data->is_gst_applicable == 2 ? 'checked' : ''}} name="is_gst_applicable" value="2">No
                    </label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_calculation_amt">Amount/Percent</label>
                <input type="text" class="form-control" id="chrg_calculation_amt" name="chrg_calculation_amt" placeholder="Charge Calculation Amount" value="{{$data->chrg_calculation_amt}}" maxlength="10">
            </div>
            <div class="form-group col-md-6" id="approved_limit_div">
                <label for="chrg_type">Charge Applicable On</label>
                <select class="form-control" name="chrg_applicable_id" id="chrg_applicable_id">
                    <option value="" selected>Select</option>
                    <option {{$data->chrg_applicable_id == 1 ? 'selected' : ''}} value="1">Limit Amount</option>
                    <option {{$data->chrg_applicable_id == 2 ? 'selected' : ''}} value="2">Outstanding Amount</option>
                    <option {{$data->chrg_applicable_id == 3 ? 'selected' : ''}} value="3">Outstanding Principal</option>
                    <option {{$data->chrg_applicable_id == 4 ? 'selected' : ''}} value="4">Outstanding Interest</option>
                    <option {{$data->chrg_applicable_id == 5 ? 'selected' : ''}} value="5">Overdue Amount</option>
                </select>
            </div>
        </div>

        <div class="row">

        </div>
    </div>




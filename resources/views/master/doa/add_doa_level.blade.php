@extends('layouts.backend.admin_popup_layout')

@section('content')
<div class="modal-body text-left">
{!!
Form::open(
    array(
        'route' => 'save_doa_level',
        'name' => 'save_doa_level',
        'autocomplete' => 'off', 
        'id' => 'frm_doa_level',
        'target' => '_top'
    )
)
!!}

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod">Level Code
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::text('level_code',
                isset($doaLevel->level_code) ? $doaLevel->level_code : $levelCode,
                [
                'class' => 'form-control',
                'placeholder' => 'Level Code',
                'id' => 'level_code',
                'readonly' => 'readonly'
                ])
            !!}                        
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> Level Name
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::text('level_name',
                isset($doaLevel->level_name) ? $doaLevel->level_name : '',
                [
                'class' => 'form-control',
                'placeholder' => 'Level Name',
                'id' => 'level_name'
                ])
            !!}            
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> State
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::select('state_id',
                [''=>'Select State'] + $stateList,
                isset($doaLevel->state_id) ? $doaLevel->state_id : '',
                [
                'class' => 'form-control',                
                'id' => 'state_id'
                ])
            !!}                        
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> City
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::select('city_id',
                [''=>'Select City'] + $cityList,
                isset($doaLevel->city_id) ? $doaLevel->city_id : '',
                [
                'class' => 'form-control',                
                'id' => 'city_id'
                ])
            !!}                        
        </div>
    </div>    
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> Limit Min Amount
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::text('min_amount',                
                isset($doaLevel->min_amount) ? $doaLevel->min_amount : '',
                [
                'class' => 'form-control',
                'placeholder' => 'Min Amount',
                'id' => 'min_amount'
                ])
            !!}                        
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> Limit Max Amount
                <span class="mandatory">*</span>
            </label>                                                
            {!!
                Form::text('max_amount',                
                isset($doaLevel->max_amount) ? $doaLevel->max_amount : '',
                [
                'class' => 'form-control',
                'placeholder' => 'Max Amount',                
                'id' => 'max_amount'
                ])
            !!}                        
        </div>
    </div>    
</div>
{!! Form::hidden('doa_level_id', isset($doaLevel->doa_level_id) ? $doaLevel->doa_level_id : '') !!}
<button type="submit" class="btn btn-success btn-sm float-right">Submit</button>  
{!!
Form::close()
!!}
</div>
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>    
var messages = {    
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",    
    ajax_get_city_url : "{{ route('ajax_get_city') }}"
};
$(document).ready(function () {
    
    $('#frm_doa_level').validate({
        rules: {
            level_code: {
               required: true
            },
            level_name: {
               required: true
            },
            state_id: {
               required: true
            },
            city_id: {
               required: true
            },
            min_amount: {
               required: true
            },
            max_amount: {
               required: true
            }
        },
        messages: {
        }
    });
            
    $(document).on('change', '#state_id', function(){
        var state_id = $(this).val();       
        $.ajax({
            url  : messages.ajax_get_city_url,
            type :'POST',
            data : {state_id : state_id, _token : messages.token},
            beforeSend: function() {
                $(".isloader").show();
            },
            dataType : 'json',
            success:function(result) {
                var optionList = result;
                $("#city_id").empty().append('<option>Select City</option>');
                $.each(optionList, function (index, data) {
                    $("#city_id").append('<option  value="' + data.city_id + '"  >' + data.name +  '</option>');
                }); 
            },
            error:function(error) {
            },
            complete: function() {
                $(".isloader").hide();
            }
        })
    })
});
</script>        
@endsection
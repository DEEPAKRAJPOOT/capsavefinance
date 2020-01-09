@extends('layouts.backend.admin_popup_layout')

@section('content')
@if(Session::get('is_data_found'))
<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
    <span id="message"></span>
</div>
@endif

<div class="modal-body text-left">
    {!!
    Form::open(
    array(
    'route' => 'save_doa_level',
    'name' => 'save_doa_level',
    'autocomplete' => 'off', 
    'id' => 'frm_doa_level',
    //'target' => '_top'
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



    @include('master.doa.doa_level_states' , ['data'=>$doaLevelStates])
    
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
    <button type="submit" class="btn btn-success btn-sm  submit float-right">Submit</button>  
    {!!
    Form::close()
    !!}
</div>
@endsection
@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>
var messages = {
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    ajax_get_city_url: "{{ route('ajax_get_city') }}",
    is_data_found: "{{ Session::get('is_data_found') }}",
    is_data_saved: "{{ Session::get('is_data_saved') }}",
    target_model: "{{ isset($doaLevel->doa_level_id) && !empty($doaLevel->doa_level_id) ? 'editDoaLevelFrame' : 'addDoaLevelFrame' }}"
};
$(document).ready(function () {
    var parent = window.parent;
    if (messages.is_data_found == 1) {
        $("#message").html('Level is already exits.');
    } else if (messages.is_data_saved == 1) {
        parent.jQuery("#" + messages.target_model).modal('hide');
        parent.oTable.draw();
    }
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

    $(document).on('change', '.state_id', function () {
        var state_id = $(this).val();
        var selector = $(this);

        var city_value = selector.data('rel');

        $.ajax({
            url: messages.ajax_get_city_url,
            type: 'POST',
            data: {state_id: state_id, _token: messages.token},
            beforeSend: function () {
                $(".isloader").show();
            },
            dataType: 'json',
            success: function (result) {
                var optionList = result;
                selector.parents('.parent_div').find('.city_id').empty().append('<option value="">Select City</option>');
                $.each(optionList, function (index, data) {
                    let check = '';
                    if (data.id == city_value) {
                        check = 'selected="selected"';

                    }

                    selector.parents('.parent_div').find('.city_id').append('<option  value="' + data.id + '"  ' + check + ' >' + data.name + '</option>');
                });
            },
            error: function (error) {
            },
            complete: function () {
                $(".isloader").hide();
            }
        })
    })



    $('.state_id').each(function () {

        $(this).trigger('change');

    });





    $(document).on('click', '.add_more', function () {
        var num = $('.parent_div').length;
        var new_line = $('.parent_div').first().clone().insertBefore(".placer4");
        new_line.find('select[name="state_id[0]"]').attr({id: 'state_id_' + num, name: 'state_id[' + num + '] '}).val('').removeClass('error');
        new_line.find('select[name="city_id[0]"]').attr({id: 'city_id_' + num, name: 'city_id[' + num + '] '}).empty().append('<option value="">Select City</option>').removeClass('error');
        new_line.find("label[class='error']").remove();
        new_line.find('.delete').show();
        num++;
    });


    $(document).on('click', '.delete', function () {
        var selector = $(this);
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to Delete?',
            buttons: {
                Yes: {
                    action: function () {
                        if($('.parent_div').length > 1) {
                              $('.delete').last().show();
                          }
                        selector.parents('.parent_div').remove();
                          
                    }

                },
                No: {
                    action: function () {
                    }
                },
            },

        });

    });




    $(document).on('click', '.submit', function (e) {
        e.preventDefault();


        let form = $('#frm_doa_level');
        var rules = {};
        var msg = {};
        form.removeData('validator');
        $("label[class='error']").remove();

        let validationRules = {
            rules: {
                level_code: {
                    required: true
                },
                level_name: {
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
        }

        $('.clsRequired ').each(function (index, value) {
            $(this).removeClass('error');
            rules[value.name] = {
                required: true
            };
        });


        var validRules = {
            rules: Object.assign(validationRules.rules, rules),
            messages: Object.assign(validationRules.messages, msg),
            ignore: ":hidden"
        };



        form.validate(validRules);
        var valid = form.valid();
        if (valid) {
            form.submit();
        }

    });





});
</script>        
@endsection
@extends('layouts.backend.admin_popup_layout')

@section('content')
<div class="modal-body text-left">
    {!!
    Form::open(
    array(
    'route' => 'save_assign_role_level',
    'name' => 'save_assign_role_level',
    'autocomplete' => 'off', 
    'id' => 'frm_assign_role_level',
    'target' => '_top'
    )
    )
    !!}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Level Name</label>                                                            
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"><strong>{{ $levelName }}</strong></label>                                                            
            </div>
        </div>    
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">City</label>                                                            
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"><strong>{{ $city }}</strong></label>                                                            
            </div>
        </div>    
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod">Limit Amount</label>                                                            
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"><strong>{{ $limitAmount }}</strong></label>
            </div>
        </div>    
    </div>

    @if(count($doaLevelRoles))
   @php $i =-1; @endphp
    @foreach($doaLevelRoles as  $keys=>$values)
    @php  $i++; @endphp

    <div class="row parent_div">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> Select Role
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                Form::select('role['.$i.']',
                [''=>'Please Select'] + $roleList,
                $keys,
                [
                'class' => 'form-control role_change clsRequired ',                
                'id' => 'role_'.$i,
                'data-rel'=> json_encode($values)
                ])
                !!}                        
            </div>
        </div> 
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> Select Role Users
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                Form::select('role_user['.$i.'][]',
                [],
                $values,
                [
                'class' => 'form-control multi-select-role-users role_user clsRequired',                
                'id' => 'role_user_'.$i,
                'multiple'=>'multiple',
                ])
                !!}                        
            </div>
        </div> 
        <div class="text-right mt-3">           
            <button style="display:none" type="button" class="btn btn-danger ml-2 float-left btn-sm delete"> Delete</button>
        </div>

    </div>


    @endforeach 
    @else 


    <div class="row parent_div">
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> Select Role
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                Form::select('role[0]',
                [''=>'Please Select'] + $roleList,
                $doaLevelRoles,
                [
                'class' => 'form-control role_change clsRequired ',                
                'id' => 'role_0',
                ])
                !!}                        
            </div>
        </div> 
        <div class="col-md-6">
            <div class="form-group">
                <label for="txtCreditPeriod"> Select Role Users
                    <span class="mandatory">*</span>
                </label>                                                
                {!!
                Form::select('role_user[0][]',
                [],
                null,
                [
                'class' => 'form-control multi-select-role-users role_user clsRequired',                
                'id' => 'role_user',
                'multiple'=>'multiple',
                ])
                !!}                        
            </div>
        </div> 
        <div class="text-right mt-3">           
            <button style="display:none" type="button" class="btn btn-danger ml-2 float-left btn-sm delete"> Delete</button>
        </div>

    </div>



    @endif









    <div class="placer4"></div>
    <div class="col-12 col-sm-12">
        <div class="text-right mt-3">           
            <button style="" type="button" class="btn btn-primary ml-2 btn-sm add_more"> Add More</button>
        </div>
    </div>




    {!! Form::hidden('doa_level_id', $doaLevelId) !!}
    <button type="submit" class="btn btn-success btn-sm float-right submit">Submit</button>  
    {!!
    Form::close()
    !!}
</div>
@endsection

@section('additional_css')
<link rel="stylesheet" href="{{ url('backend/assets/css/bootstrap-multiselect.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection

@section('jscript')
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>

var messages = {
    get_user_by_role: "{{ route('get_ueser_by_role') }}",
    token: "{{csrf_token()}}"
}

$(document).ready(function () {



    $(document).on('click', '.submit', function (e) {
        e.preventDefault();


        let form = $('#frm_assign_role_level');
        var rules = {};
        var msg = {};
        form.removeData('validator');
        $("label[class='error']").remove();

        let validationRules = {
            rules: {
                'role[0]': {
                    required: true
                },
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



//    $('#frm_assign_role_level').validate({
//        rules: {
//            role: {
//               required: true
//            }            
//        },
//        messages: {
//        }
//    });

    $('.multi-select-role-users').multiselect({
        maxHeight: 400,
        enableFiltering: true,
        numberDisplayed: 6,
        selectAll: true,
    });


    $(document).on('click', '.add_more', function () {
        var num = $('.parent_div').length;
        var new_line = $('.parent_div').first().clone().insertBefore(".placer4");
        new_line.find('select[name="role[0]"]').attr({id: 'role_' + num, name: 'role[' + num + '] '}).val('').removeClass('error');
        new_line.find('select[name="role_user[0][]"]')
                .attr({id: 'role_user_' + num, name: 'role_user[' + num + '][] '})
                .empty()
                .multiselect('clearSelection')
                .removeClass('error');

        new_line.find('.btn-group:not(:first)').remove();

        new_line.find("label[class='error']").remove();
        new_line.find('.delete').show();
        num++;
    });


    $(document).on('change', '.role_change', function () {
        var selector = $(this);
        var value = selector.val();
        var selected_value = (selector.data('rel')) ? selector.data('rel')  : [];
        $.ajax({
            url: messages.get_user_by_role,
            type: 'POST',
            data: {role_id: value, _token: messages.token},
            beforeSend: function () {
                $(".isloader").show();
            },
            dataType: 'json',
            success: function (result) {
                var optionList = result.data;
                selector.parents('.parent_div').find('.role_user').empty();
                $.each(optionList, function (index, data) {
                    let check = '';
                    if (selected_value.indexOf(+index) != -1) {
                        check = 'selected="selected"';
                    }
                    selector.parents('.parent_div').find('.role_user').append('<option  value="' + index + '"  ' + check + ' >' + data + '</option>');
                });
                selector.parents('.parent_div').find('.multi-select-role-users').multiselect('rebuild');
                selector.parents('.parent_div').find('.btn-group:not(:first)').remove();
            },
            error: function (error) {
            },
            complete: function () {
                $(".isloader").hide();
            }
        })

    });
    
$('.role_change').each( function ()
{
   
  $(this).trigger('change');  
});


    $(document).on('click', '.delete', function () {
        var selector = $(this);
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to Delete?',
            buttons: {
                Yes: {
                    action: function () {
                        if ($('.parent_div').length > 1) {
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




});
</script>        
@endsection
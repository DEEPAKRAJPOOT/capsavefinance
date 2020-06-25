@extends('layouts.backend.admin_popup_layout')

@section('content')
  <div class="modal-body text-left">
{!!
Form::open(
    array(
        'route' => 'save_app_rejection',
        'name' => 'addAppRejection',
        'autocomplete' => 'off', 
        'id' => 'addAppRejection',
        'target' => '_top'
    )
)
!!}
<div class="row">
    <div class="col-md-12">
        <label for="reason"> Status
                <span class="mandatory">*</span>
        </label>
        <div class="form-group form-check">
            <ul class="custom-check-label">
                <li>
                    <input type="radio" class="form-check-input" {{($status_id == config('common.mst_status_id')['APP_REJECTED']) ? 'checked' : ''}} id="status1" name="status" value="1" data-error="#errNm1">
                    <label class="form-check-label" for="status1">Reject</label>
                </li>
                <li>
                    <input type="radio" class="form-check-input" {{($status_id == config('common.mst_status_id')['APP_CANCEL']) ? 'checked' : ''}} id="status2" name="status" value="2" data-error="#errNm1">
                    <label class="form-check-label" for="status2">Cancel</label> 
                </li>
                <li>
                    <input type="radio" class="form-check-input" {{($status_id == config('common.mst_status_id')['APP_HOLD']) ? 'checked' : ''}} id="status3" name="status" value="3" data-error="#errNm1">
                    <label class="form-check-label" for="status3">Hold</label>
                </li>
                <li>
                    <input type="radio" class="form-check-input" {{($status_id == config('common.mst_status_id')['APP_DATA_PENDING']) ? 'checked' : ''}} id="status4" name="status" value="4" data-error="#errNm1">
                    <label class="form-check-label" for="status4">Data Pending</label>
                </li>
            </ul>
        </div>
        <div class="errorTxt">
            <span id="errNm1"></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="reason"> Comments
                <span class="mandatory">*</span>
            </label>
            <textarea type="text" name="reason" value="" class="form-control" tabindex="1" maxlength="500" placeholder="write reason..." data-error="#errNm2">{{ $reason }}</textarea>
        </div>
        <div class="errorTxt">
            <span id="errNm2"></span>
        </div>
    </div>
</div>
{!! Form::hidden('app_id', $app_id) !!}
{!! Form::hidden('biz_id', $biz_id) !!}
{!! Form::hidden('user_id', $user_id) !!}
{!! Form::hidden('note_id', $note_id) !!}
<button type="submit" id="submit" class="btn btn-success btn-sm float-right submit">Submit</button>  
{!!
Form::close()
!!}
  </div>
@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/assets/js/application.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function($){
        $(document).on('click', '.submit', function (e) {
//            e.preventDefault();
            var form = $('#addAppRejection');

            var validRules = {
                rules: {
                    status: {
                        required: true
                    },
                    reason: {
                        required: true
                    }
                },
                messages: {
                    status: {
                        required: 'Atleast one radio button should be checked.'
                    },
                    reason: {
                        required: 'Please enter your comment.'
                    }
                },
                errorPlacement: function(error, element) {
                    var placement = $(element).data('error');
                    if (placement) {
                      $(placement).append(error);
                    } else {
                      error.insertAfter(element);
                    }
                  }
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

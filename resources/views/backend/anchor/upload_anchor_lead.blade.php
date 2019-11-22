@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
           <form id="anchorForm" name="anchorForm" method="POST" action="{{route('add_anchor_lead')}}"  target="_top" enctype="multipart/form-data">
		@csrf
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Upload File
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="file" name="anchor_lead" id="anchor_lead" value="" class="form-control anchor_lead" >
                              </div>
                           </div>
                           
                        </div>
                           
                
                <button type="submit" class="btn btn-primary float-right" id="saveAnch">Submit</button>  
           </form>
         </div>
     



@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {
            $('#saveAnch').on('click', function (event) {
                $('input.anchor_lead').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.comp_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.email').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.phone').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('select.state').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.city').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.pin_code').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                // test if form is valid 
                if ($('form#anchorForm').validate().form()) {
                    var form = $("#anchorForm");
                    $.ajax({
                        type: "POST",
                        url: '{{Route('add_anchor_reg')}}',
                        data: form.serialize(), // serializes the form's elements.
                        cache: false,
                        success: function (res)
                        {
                            if (res.status == 1)
                            {
                               
                                       $('#addAnchorFrm').dialog('close');
                                     window.location.href = "/anchor";
                            }
                        },
                        error: function (error)
                        {
                            console.log(error);
                        }

                    });
                } else {
                    console.log("does not validate");
                }
            })
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate();
        });

</script>
@endsection
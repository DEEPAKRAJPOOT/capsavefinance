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
                @if ($is_superadmin == '1')
                <div  class="row">                    
                      <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Anchor
                                 <span class="mandatory">*</span>
                                 </label>        
                                     <select class="form-control assigned_anchor" name="assigned_anchor" id="assigned_anchor">
                            <option value="">please select</option>
                             @foreach($anchDropUserList as $key => $value)
                             <option value="{{$value->anchor_id}}"> {{$value->comp_name}} </option>
                             @endforeach
                         </select>
                                  
                              </div>
                           </div> 
                       
                </div>
                @endif
                           
                
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch">Submit</button>  
           </form>
         </div>
     



@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {
              $('#anchorForm').validate({ // initialize the plugin
                rules: {
                anchor_lead: {
                required: true,
                extension: "csv"
                },
                 assigned_anchor: {
                required: true,
                }
                },
                messages: {
                anchor_lead: {
                required: "Please select file",
                extension:"Please select only csv format",
                }
                }
                });

            $('form#anchorForm').validate();
        });

</script>
@endsection
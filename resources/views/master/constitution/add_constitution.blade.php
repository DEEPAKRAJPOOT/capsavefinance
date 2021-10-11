@extends('layouts.backend.admin_popup_layout')
@section('content')

 <div class="modal-body text-left">
     <form id="stateForm" name="stateForm" method="POST" action="{{route('save_constitution')}}" target="_top">
              @csrf

      <div class="row">
        <div class="form-group col-6">
          <label for="name">Constitution Name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Enter Constitution Name" maxlength="50">
        </div>
        <div class="form-group col-6">
             <label for="state_type">Status</label><br />
             <select class="form-control" name="is_active" id="is_active">
                  <option disabled value="" selected>Select</option>
                  <option value="1">Active</option>
                  <option value="2">In-Active</option>
              </select>
        </div>
      </div>
        <div class="row">
            <div class="form-group col-6">
            <label for="cibil_lc_code">Constitution Code</label>
            <input type="text" class="form-control" id="cibil_lc_code" name="cibil_lc_code" placeholder="Enter Constitution Code" maxlength="50">
            </div>
        </div>
      <div class="row">
         <div class="form-group col-md-12 mb-0">
             <input type="submit" class="btn btn-success btn-sm pull-right" name="add_constitution" id="add_constitution" value="Submit"/>
        </div>
      </div>
   </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">

    var messages={
        unique_constitution_url:"{{ route('check_unique_constitution_url') }}",
        unique_constitution_code:"{{ route('check_unique_constitution_code') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {
        $.validator.addMethod("uniqueConsti",
            function(value, element, params) {
                var result = true;
                var data = {name : value, _token: messages.token};
                if (params.id) {
                    data['id'] = params.id;
                }
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_constitution_url, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Constitution name is already exists'
        );
        $.validator.addMethod("uniqueConstiCode",
            function(value, element, params) {
                var result = true;
                var data = {cibil_lc_code : value, _token: messages.token};
                if (params.id) {
                    data['id'] = params.id;
                }
                $.ajax({
                    type:"POST",
                    async: false,
                    url: messages.unique_constitution_code, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Constitution code is already exists'
        );

        $('#stateForm').validate({ // initialize the plugin
            rules: {
                'name' : {
                    required : true,
                    uniqueConsti: true
                },
                'cibil_lc_code' : {
                    required : true,
                    uniqueConstiCode: true
                },
                'is_active' : {
                    required : true,
                },
            },
            messages: {
                'name': {
                    required: "Please enter Constitution Name",
                },
                'cibil_lc_code': {
                    required: "Please enter Constitution Code",
                },
                'is_active': {
                    required: "Please select Constitution Status",
                },
            }
        });
    });
</script>
@endsection
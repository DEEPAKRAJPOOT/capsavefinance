@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="securitydocumentForm" name="securitydocumentForm" method="POST" action="{{route('edit_security_document')}}"
        target="_top">
        @csrf
        <input type="hidden" name="security_doc_id" id="security_doc_id" value="{{ $securityDoc_data->security_doc_id }}">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">Security Document Type</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ isset($securityDoc_data) ? $securityDoc_data->name : "old('name')" }}"
                    placeholder="Enter Security Document Type" maxlength="50">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="chrg_type">Status</label><br />
                <select class="form-control" name="is_active" id="is_active">
                    <option value="" selected>Select</option>
                    <option value="1"
                        value="{{ isset($securityDoc_data) ? $securityDoc_data->is_active : "old('location_code')" }}"
                        {{ isset($securityDoc_data) && $securityDoc_data->is_active == 1 ? 'selected' : '' }}>Active</option>
                    <option value="2"
                        value="{{ isset($securityDoc_data) ? $securityDoc_data->is_active : "old('location_code')" }}"
                        {{ isset($securityDoc_data) && $securityDoc_data->is_active == 2 ? 'selected' : '' }}>In-Active
                    </option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 text-right">
                <input type="submit" class="btn btn-success btn-sm" name="edit_security_document" id="edit_security_document"
                    value="Submit" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
<script type="text/javascript">

    var messages = {
        check_unique_securitydocument_name: "{{ route('check_unique_securitydocument_name') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {
        $.validator.addMethod("uniqueSecurityDocumentName",
            function (value, element, params) {
                var result = true;
                var data = { name: value, _token: messages.token };
                if (params.security_doc_id) {
                    data['security_doc_id'] = params.security_doc_id;
                }
                $.ajax({
                    type: "POST",
                    async: false,
                    url: messages.check_unique_securitydocument_name, // script to validate in server side
                    data: data,
                    success: function (data) {
                        result = (data.status == 1) ? false : true;
                    }
                });
                return result;
            }, 'Security Document Name already exists'
        );

        $('#securitydocumentForm').validate({ // initialize the plugin
            rules: {
                'name': {
                    required: true,
                    uniqueSecurityDocumentName: {
                        security_doc_id: $("#security_doc_id").val()
                    }
                },
                'is_active': {
                    required: true,
                },
            },
            messages: {
                'name': {
                    required: "Please Enter Security Document Name",
                },
                'is_active': {
                    required: "Please Select Status",
                },
            }
        });
    });
</script>
@endsection
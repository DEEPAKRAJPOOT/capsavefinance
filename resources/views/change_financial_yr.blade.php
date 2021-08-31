@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Application</h3>
            <small>Change Financial Year</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Application</li>
                <li class="active">Change Financial Year</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">

            <div class="" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Financial Year</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="modal-body text-left">
                                <form id="entityForm" name="entityForm" method="POST"
                                    action="{{ route('api_change_year') }}" target="_top">
                                    @csrf

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="app_id">Enter App ID</label>
                                            <input type="text" class="form-control" id="app_id" name="app_id"
                                                placeholder="Enter App ID" maxlength="50">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="year">Enter Three Consecutive Year</label>
                                            <input type="text" class="form-control" id="year" name="year"
                                                placeholder="2020 2019 2018" required pattern="(8 6)\d{2} \d{2} \d{3}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <input type="submit" class="btn btn-success btn-sm pull-right"
                                                name="add_entity" id="add_entity" value="Submit" />
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

@endsection

@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>

<script type="text/javascript">

    var messages = {
        // unique_entity_url:"{{ route('check_unique_entity_url') }}",
        token: "{{ csrf_token() }}"
    }

    $(document).ready(function () {
        $('input[name="app_id"]').mask('0000000000');
        $('input[name="year"]').mask('0000-0000-0000');

        $.validator.addMethod("yearsOrder",
            function (value, element, params) {
                var result = true;
                var year = value.split('-');

                if (year.length != 3 || (year[0] - year[1]) != (year[1] - year[2]) || (year[0] - year[1]) != 1) {
                    result = false;
                }
                return result;
            }, 'Three consecutive years in Desc Order are required to Change the years'
        );

        $('#entityForm').validate({ // initialize the plugin
            rules: {
                'app_id': {
                    required: true,
                    number: true
                },
                'year': {
                    required: true,
                    maxlength: 14,
                    yearsOrder: $('#year').val()
                },
            },
            messages: {
                'app_id': {
                    required: "Please enter App Id",
                },
                'year': {
                    required: "Enter Year",
                },
            }
        });
    });
</script>
@endsection
@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Financial Trans Config</h3>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Select Type</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="trans_type" id="trans_type"  class="form-control form-control-sm">
                            <option value="">Select Type</option>
                            @foreach($transType as $key=>$val)
                            <option value="{{$val->trans_config_id}}" {{(old('trans_type') == $val->trans_config_id)? 'selected': ''}}> {{$val->trans_type}} </option>                            
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Variables</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <select name="variable" id="variable" class="multi-select-demo form-control form-control-sm" multiple="multiple">
                            @foreach($variables as $key=>$val)
                            <option value="{{$val->id}}" {{(old('variable') == $val->id)? 'selected': ''}}> {{$val->name}} </option>                            
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Select Journal</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="trans_type" id="trans_type"  class="form-control form-control-sm">
                            <option value="">Select Journal</option>
                            @foreach($journals as $key=>$val)
                            <option value="{{$val->id}}" {{(old('trans_type') == $val->id)? 'selected': ''}}> {{$val->name}} </option>                            
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="mb-0">Submit</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="accountList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Code</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script>
$('.multi-select-demo').multiselect();
var messages = {
    get_ajax_account_list: "{{ URL::route('get_ajax_account_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection
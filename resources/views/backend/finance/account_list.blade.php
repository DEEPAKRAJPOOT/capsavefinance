@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Account List</h3>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
        <h4>{{ isset($accountId) ? 'Edit' : 'Add'}} Account</h4>
            {!!
                Form::open(
                array(
                'method' => 'post',
                'route' => 'save_account',
                'id' => 'frmAccount'
                )
                ) 
            !!}   
            <input type="hidden" name="accountId" value="{{ isset($accountId) ? $accountId : ''}}" /> 
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Account Code</label>
                        <span class="mandatory">*</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input  class="form-control" type="text" name="account_code" id="account_code" placeholder="Account Code" value="{{ old('account_code') ? old('account_code') : isset($accountData->account_code) ? $accountData->account_code : ''}}" />
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Account Name</label>
                        <span class="mandatory">*</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input  class="form-control" type="text" name="account_name" id="account_name" placeholder="Account Name" value="{{ old('account_name') ? old('account_name') : isset($accountData->account_name) ? $accountData->account_name : ''}}" />
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                    <label class="mb-0">Active</label>
                    <span class="mandatory">*</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    <select name="is_active" id="is_active" class="form-control form-control-sm">
                        <option value="">Is Active</option>
                        <option value="1" {{ (old('is_active') == '1')? 'selected': (isset($accountData->is_active) && $accountData->is_active=='1') ? 'selected' : ''}}>Yes</option>
                        <option value="0" {{ (old('is_active') == '0')? 'selected': (isset($accountData->is_active) && $accountData->is_active=='0') ? 'selected' : ''}}>No</option>                  
                    </select>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn  btn-success btn-sm">Submit</button>
                        @if(isset($accountId) && !empty($accountId))
                            <a class="btn  btn-success btn-sm" href="{{ route('get_fin_account') }}">
                                Cancel
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            {!!  Form::close() !!} 
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="accountList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Active</th>
                                    <th>Action</th>
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
<script>
var messages = {
    get_ajax_account_list: "{{ URL::route('get_ajax_account_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection
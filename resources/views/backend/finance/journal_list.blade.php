@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Journal List</h3>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <h3>{{ isset($journalId) ? 'Edit' : 'Add'}} Journal</h3>
            {!!
                Form::open(
                array(
                'method' => 'post',
                'route' => 'save_journal',
                'id' => 'frmJournal'
                )
                ) 
            !!}   
            <input type="hidden" name="journalId" value="{{ isset($journalId) ? $journalId : ''}}" /> 
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Journal Name</label>
                        <span class="mandatory">*</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input  class="form-control" type="text" name="name" id="name" placeholder="Journal Name" value="{{ old('name') ? old('name') : isset($journalData->name) ? $journalData->name : ''}}" />
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                    <label class="mb-0">Journal Type</label>
                    <span class="mandatory">*</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <select name="journal_type" id="journal_type" class="form-control form-control-sm">
                    <option value="">Select Journal Type</option>
                        @foreach(config('common.JOURNAL_TYPE') as $key=>$val)
                            <option value="{{$key}}" {{ (old('journal_type') == $key)? 'selected': (isset($journalData->journal_type) && $journalData->journal_type==$key) ? 'selected' : ''}}> {{$val}} </option>
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
                        <button class="btn  btn-success btn-sm">Submit</button>
                    </div>
                </div>
            </div>
            {!!  Form::close() !!} 
            @if(isset($journalId) && !empty($journalId))
            <div class="row">
                <div class="col-sm-12">
                    <a href="{{ route('get_fin_journal') }}">
                        <button class="btn  btn-success btn-sm">Add Journal</button>
                    </a>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="journalList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Name</th>
                                    <th>Journal Type</th>
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
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script>
var messages = {
    get_ajax_journal_list: "{{ URL::route('get_ajax_journal_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
    $(document).ready(function(){
        $('#frmJournal').validate({
            rules: {
                "name": {
                   required: true
                },
                "journal_type": {
                    required: true
                }                
            }
        });
    });
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection
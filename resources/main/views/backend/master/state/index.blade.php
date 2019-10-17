@extends('layouts.admin')

@section('content')
<div class="prtm-page-bar">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><h2>manage State</h2></a></li>        
    </ul>
</div>
<div class="row">
    <div class="form-group">
    <div class="col-md-4" >
        <a href="{{ route('edit_state') }}" class="btn btn-success btn-md">Create State</a>
    </div>
    </div>
</div><br/>
<div class="row">
        {!!
            Form::open(
            array('name' => 'ProStateMaster',
            'autocomplete' => 'off', 
            'id' => 'manageState',  

            )
            ) 
        !!}
    <div class="form-group">
       
        <div class="col-md-4">
                 
       {!!
            Form::text('by_state_name',
            null,
            [
            'class' => 'form-control',
            'placeholder' => 'Search by state name',
            'id'=>'by_state_name'
            ])
            !!}
        </div>
        <div class="col-md-4">

            {!!
                Form::select('is_active',
                [''=>'Status', '1'=>'Active', '0'=>'In Active'],
                null,
                array('id' => 'is_active',
                'class'=>'form-control'))
            !!}
        </div>
        <button type="submit" class="btn btn-success">Search</button>
    </div>
    {!!
        Form::close()
    !!}
</div>
<div class="data-table-style">
    <div class="prtm-block pos-relative">
        
        <div class="prtm-block-content">
            <div class="dataTables_wrapper">
                <div class="table-responsive">
                    <table class="table table-hover dataTable" id="stateMaster">
                        <div class="row"><tr><input type="submit" name="delete_selected" id="delete_selected_country" class="btn-danger btn del-selected-cls delete-sel-jobseeker" value="Delete Selected"></tr></div>
                        <thead>
                            <tr>
                                <th width="10"><input type="checkbox" id="selectedAll" /></th>
                                <th width="50">{{ trans('master.id') }}</th>
                                <th width="50">{{ trans('master.country_name') }}</th>
                                <th  width="50">{{ trans('master.state_name') }}</th>
                                <th  width="50">{{ trans('master.status') }}</th>                                
                                <th width="150">{{ trans('master.action') }}</th>
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
@endsection
@section('pageTitle')
State
@endsection
@section('jscript')
<script>
    
var messages = {
    get_ajax_states: "{{ URL::route('get_ajax_states') }}",
    delete_state: "{{ URL::route('delete_state') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
       
};
</script>
   <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/master/state.js') }}" type="text/javascript"></script>
@endsection

@extends('layouts.admin')

@section('content')

<div class="prtm-block">
    <div class="prtm-block-title mrgn-b-lg">
        <h3>Users List</h3> 
    </div>
    <div class="row">
        {!!
            Form::open(
            array('name' => 'ProCountryMaster',
            'autocomplete' => 'off', 
            'id' => 'manageUser',  

            )
            ) 
        !!}
    <div class="form-group">
       
        <div class="col-md-4">
                 
       {!!
            Form::text('by_email',
            null,
            [
            'class' => 'form-control',
            'placeholder' => 'Search by First name, Last name and Email',
            'id'=>'by_name'
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
    <div class="table-border-top prtm-full-block">
        
            <div class="table-responsive">
                <div class="text-right"><input type="submit" name="delete_selected" id="delete_selected_user" class="btn-danger btn del-selected-cls delete-sel-jobseeker" value="Delete Selected"></div>
                        
                <table class="data-table table-hover mrgn-all-none text-capitalize th-pad-all-sm td-pad-all-sm th-fw-light" id="usersTable" style="width:100%;">
                    <thead>
                        <tr>
                            <th width="10"><input type="checkbox" id="selectedAll" /></th>
                            <th> User Name </th>
                            <th> Email </th>
                            <th> Phone </th>
                            <th> Date </th>
                            <th> Status </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
       
    </div>
</div>
         
@endsection
@section('pageTitle')
User list
@endsection
@section('jscript')
<script>
var messages = {
    get_users: "{{ URL::route('get_users') }}",
    delete_users: "{{ URL::route('delete_users') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",    
};
</script>
<script src="{{ asset('backend/js/user.js') }}" type="text/javascript"></script>
@endsection

@extends('layouts.admin')

@section('content')
<div class="mycls hide alert alert-success">
   Record has been deleted.
</div>
<div class="prtm-page-bar">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><h2>manage Rights</h2></a></li>
    </ul>
</div>
<div class="row">
    <div class="form-group">
    <div class="col-md-4" >
        <a href="{{ route('edit_right') }}" class="btn btn-success btn-md">Create Rights</a>
    </div>
    </div>
</div><br/>
<div class="row">
        {!!
            Form::open(
            array('name' => 'ProRightMaster',
            'autocomplete' => 'off', 
            'id' => 'manageSource',  

            )
            ) 
        !!}
    <div class="form-group">
       
        <div class="col-md-4">
                 
       {!!
            Form::text('by_title',
            null,
            [
            'class' => 'form-control',
            'placeholder' => 'Search by title',
            'id'=>'by_title'
            ])
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
                    <div class="text-right"><input type="submit" name="delete_selected" id="delete_selected_source" class="btn-danger btn del-selected-cls delete-sel-jobseeker" value="Delete Selected"></div>
                    <table class="table table-hover dataTable" id="rightMaster">
                         <thead>
                            <tr>
                                <th  width="10"><input type="checkbox" id="selectedAll" /></th>
                                <th width="50">User Name</th>
                                <th width="100">Title</th>
                                <th width="50">Number</th>
                                <th width="100">Phase</th>
                                <th width="100">Is Featured</th>
                                <th >Action</th>
                         
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
Rights
@endsection
@section('jscript')
<script>
    
var messages = {
    get_ajax_right: "{{ URL::route('get_ajax_right') }}",
    delete_right: "{{ URL::route('delete_rights') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
       
};
</script>
   <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/master/right.js') }}" type="text/javascript"></script>
@endsection

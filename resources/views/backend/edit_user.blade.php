@extends('layouts.backend.admin-layout')

@section('content')

<div class="form-style">
    <div class="row">
        <div class="col-md-12 ">
            <div class="prtm-block">
                <div class="prtm-block-title mrgn-b-lg">
                    <h3 class="text-capitalize">Edit user</h3> 
                </div>
                {!!
                Form::open(
                array('name' => 'ProBackendUser',
                'autocomplete' => 'off', 
                'id' => 'ProBackendUser',  
                'class' => 'form-horizontal',
                'url' => route('save_backend_user'),

                )
                ) 
                !!}
                <input type="hidden" name="user_id" id='user_id' value="{{ isset($userData->user_id) ? $userData->user_id : null}}">
                    <div class="form-group">
                        <label for="first_name">First name</label>
                        {!!
                            Form::text('first_name',
                            isset($userData->f_name) ? $userData->f_name : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'First name',
                            'id'=>'first_name'
                            ])
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last name</label>
                        {!!
                            Form::text('last_name',
                            isset($userData->l_name) ? $userData->l_name : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Last name',
                            'id'=>'last_name'
                            ])
                        !!}
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="msg"> Phone</label>   
                        {!!
                            Form::text('phone',
                            isset($userData->phone_no) ? $userData->phone_no : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Phone',
                            'id'=>'phone'
                            ])
                        !!}
                    </div>
                   
                    <div class="form-group">
                        <label for="Address 1"> Address 1</label>   
                        {!!
                            Form::text('addr1',
                            isset($userData->addr1) ? $userData->addr1 : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Address 1',
                            'id'=>'addr1'
                            ])
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="Address 2"> Address 2</label>   
                        {!!
                            Form::text('addr2',
                            isset($userData->addr2) ? $userData->addr2 : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Address 2',
                            'id'=>'addr2'
                            ])
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="area"> area</label>   
                        {!!
                            Form::text('area',
                            isset($userData->area) ? $userData->area : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Area',
                            'id'=>'area'
                            ])
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="country"> Country</label>   
                        {!!
                            Form::select('country_id',
                            [''=>'Select country']+$countryDropDown->toArray(),
                            isset($userData->country_id) ? $userData->country_id : '',
                            array('id' => 'country_id',
                            'class'=>'form-control'))
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="area"> City</label>   
                        {!!
                            Form::text('city',
                            isset($userData->city) ? $userData->city : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'City',
                            'id'=>'city'
                            ])
                        !!}
                    </div>
                    <div class="form-group">
                        <label for="area"> Zip Code</label>   
                        {!!
                            Form::text('zip_code',
                            isset($userData->zip_code) ? $userData->zip_code : '',
                            [
                            'class' => 'form-control',
                            'placeholder' => 'Zip Code',
                            'id'=>'zip_code'
                            ])
                        !!}
                    </div>
                <div class="form-group">
                        <label for="status"> Status</label>   
                        {!!
                            Form::select('status',
                            [''=>'Select Status']+['0'=>'Inactive','1'=>'Active'],
                            isset($userData->status) ? $userData->status : '',
                            array('id' => 'status',
                            'class'=>'form-control'))
                        !!}
                    </div>
                <div class="row">                       
                    <button type="submit" class="btn btn-success">Submit</button>
                    <a href="{{ route('manage_users') }}" class="btn btn-base">> Back to list</a>
                </div>
                {!!
                    Form::close()
                !!}
            </div>
           
        </div>
        
    </div>
</div>
@endsection
@section('pageTitle')
Edit User
@endsection
@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/validation/user.js') }}" type="text/javascript"></script>
@endsection


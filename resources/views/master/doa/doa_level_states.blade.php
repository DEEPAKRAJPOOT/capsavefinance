
@if( count( $data))

@foreach($data as $keys => $values)  


<div class="row parent_div">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> State
                <span class="mandatory">*</span>
            </label>                                                
            {!!
            Form::select('state_id['.$keys.']',
            [''=>'Select State'] + $stateList,
            isset($values['state_id']) ? $values['state_id'] : '',
            [
            'class' => 'form-control clsRequired state_id',                
            'id' => 'state_id_'.$keys,
            'data-rel'=>isset($values['city_id']) ? $values['city_id'] : '',
            ])
            !!}                        
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> City
                <span class="mandatory">*</span>
            </label>                                                
            {!!
            Form::select('city_id['.$keys.']',
            [''=>'Select City'],
            isset($doaLevel->city_id) ? $doaLevel->city_id : '',
            [
            'class' => 'form-control clsRequired city_id ',                
            'id' => 'city_id_'.$keys
            ])
            !!}                        
        </div>
    </div>  

<div class="col-12 col-sm-12">
    <div class="text-right mt-3">           
        <button style="display:  {{($keys> 0) ? 'block': 'none'}} " type="button" class="btn btn-danger ml-2 float-left btn-sm delete"> Delete</button>
    </div>
</div>

</div>


@endforeach()



@else 


<div class="row parent_div">
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> State
                <span class="mandatory">*</span>
            </label>                                                
            {!!
            Form::select('state_id[0]',
            [''=>'Select State'] + $stateList,
            isset($doaLevel->state_id) ? $doaLevel->state_id : '',
            [
            'class' => 'form-control clsRequired state_id',                
            'id' => 'state_id_0'
            ])
            !!}                        
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="txtCreditPeriod"> City
                <span class="mandatory">*</span>
            </label>                                                
            {!!
            Form::select('city_id[0]',
            [''=>'Select City'] + $cityList,
            isset($doaLevel->city_id) ? $doaLevel->city_id : '',
            [
            'class' => 'form-control clsRequired city_id ',                
            'id' => 'city_id_0'
            ])
            !!}                        
        </div>
    </div>    
    <div class="col-12 col-sm-12">
    <div class="text-right mt-3">           
        <button style="display:none" type="button" class="btn btn-danger ml-2 float-left btn-sm delete"> Delete</button>
    </div>
</div>
</div>


@endif



<div class="placer4"></div>
<div class="col-12 col-sm-12">
    <div class="text-right mt-3">           
        <button style="" type="button" class="btn btn-primary ml-2 btn-sm add_more"> Add More</button>
    </div>
</div>
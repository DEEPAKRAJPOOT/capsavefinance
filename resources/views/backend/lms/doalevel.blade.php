<div class="col-md-12">
    <h5 class="card-title">DOA Level</h5>
</div> 

<div class="col-md-12">
    <div class="form-group password-input">
        <div class="row">
            <div class="col-md-6">
                <h5>DOA Level </h5>

                {!!
                Form::select('doa_level[]',
                $doaLevelList,
                null,
                ['id' => 'doaLevelList',
                'class'=>'form-control multi-select-demo',
                'multiple'=>'multiple'
                ])
                !!}



            </div>

            <div class="col-md-6">
                <h5>Required</h5>

                {!!
                Form::checkbox('required',
                1,
                '',
                null,
                ['id' => 'required',
                'class'=>'form-control',
                ])
                !!}



            </div>

        </div>
    </div>
</div>
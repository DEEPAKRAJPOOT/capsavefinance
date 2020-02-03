@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
         
        {!!   Form::open(
               array( 'name'=>'anchorForm',
                'route'=>'app_status_disbursed',
                'method'=>'POST',
                'target'=>'_top',
                'id'=>'anchorForm'
                )
            )   !!}
 
                <div class="text-center">
               <label for="email">Are You Sure You Want To Change The Status?</label>
               {!!Form::hidden ("app_id",isset($app_id)?$app_id:'', array('id'=>'app_id'))!!}
               {!!Form::hidden ("biz_id",isset($biz_id)?$biz_id:'', array('id'=>'biz_id'))!!}
            </div>   
            <div class="text-center">     
            <button type="submit" class="btn btn-primary btn-sm " id="saveAnch">OK</button> <button id="close_btn" type="button" class="btn btn-secondary  btn-sm">No</button>
            </div>    
           {!!Form::close()!!}
         </div>
     



@endsection

@section('jscript')
<script type="text/javascript">
     $(document).ready(function(){
$('#close_btn').click(function() {
    parent.$('#changeAppDisbursStatus').modal('hide');
});
});
</script>
@endsection
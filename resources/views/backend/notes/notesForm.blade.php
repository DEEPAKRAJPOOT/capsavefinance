@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
	                <label class="">Comment</label>                                          
	                <textarea class="form-control" id='notesData'></textarea>
	                <span id='msg'></span>
	    </div> 
        <button type="submit" class="btn btn-primary float-right" onclick="submitNotes();">Submit</button>
	</div>
</div>	
@endsection
@section('jscript')
<script>
   function submitNotes(){ 
       var notesData = $.trim($('#notesData').val());
       if(notesData == ''){
            $('#msg').html('Please Enter Comment');
            setTimeout(function(){ $('#msg').html(''); }, 2000);
       }else{
           $.ajax({
               type: 'POST',
               url:'/application/notes',
               data:{'notesData':notesData},
               dataType:'json',
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
               success:function(data){
                  $('#msg').html(data.message);
                	console.log(data);
               }
           });
       }

    }
</script>
@endsection	



		
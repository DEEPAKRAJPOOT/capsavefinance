
<!DOCTYPE html>
<html>
<body>
	<div class="row">
		<div class="form-group">
	                <label class="">Comment</label>                                          
	                <textarea class="form-control" id='notesData'></textarea>
	                <span id='errorMsg'></span>
	    </div> 
        <button type="submit" class="btn btn-primary float-right" onclick="submitNotes();">Submit</button>
	</div>
</body>
<script>
   function submitNotes(){ 
       var notesData = $.trim($('#notesData').val());
       if(notesData == ''){
            $('#errorMsg').html('Please Enter Comment');
            setTimeout(function(){ $('#errorMsg').html(''); }, 1000);
       }else{
           $.ajax({
               type: 'POST',
               url:'/notes',
               data:{'notesData':notesData},
               dataType:'html',
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
               success:function(data){
                console.log(data);
               }
           });
       }

    }
</script>
</html>		



		
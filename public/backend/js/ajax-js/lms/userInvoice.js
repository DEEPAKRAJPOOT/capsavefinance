$('#autoPopulateState').on('change',function(){
    var stateID = $(this).val();
    if(stateID){
      //  var image_id =  $(this).data('city-error');
        $.ajax({
           type:"GET",
           data: { "approved": "True"},
           url:"{{url('/lms/get-autocomplete-state')}}",
           success:function(data){
            if(data){
                console.log(data);

            }else{
               $("#autoPopulateState").empty();
               console.log('else')
            }
           }
        });
    }else{
        $("#autoPopulateState").empty();
        console.log('last else')
    }

   });
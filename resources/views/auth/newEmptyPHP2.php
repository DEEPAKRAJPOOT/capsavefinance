$('#submit').on('click', function(event) {

           // adding rules for inputs with class 'comment'
           $('input.comment').each(function() {
               $(this).rules("add",
                   {
                       required: true
                   })
           });            

           // prevent default submit action        
           //event.preventDefault();

           // test if form is valid
           if($('form.commentForm').validate().form()) {
               console.log("validates");
               $( "#submit1" ).trigger('click');
               //return true;
           } else {
               console.log("does not validate");
           }
       })
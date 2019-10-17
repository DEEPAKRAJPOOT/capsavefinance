<script type='text/javascript'>
	
	$(document).ready(function(){

	$('.fileupload').click(function(){
		alert();
	})


	})

    $('.number').keypress(function(event) {

     if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46) 
          return true;

     else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
          event.preventDefault();

    });


	$('#companydetails').validate({
	   ignore: [], 
	   rules: {
	        customername: {
	           required: true,
	           pattern:"^[a-z A-Z\s]+$",
	           minlength:2,
	           maxlength:50,
	        },
	      	regisno: {
	           required: true,
	           alphanumeric:true,
	           minlength:5,
	           maxlength:20,
	        },
	     	regisdate: {
	           required: true,
	        },
	        status: {
	           required: true,
	           
	        },
	        naturebusiness: {
	           required: true,
	        },
	    },
	    messages:{
	        customername: {              
	           required:"This field is required", 
	           pattern:"Please enter only alphabetical characters",   
	           minlength:"Please enter at least 2 characters",
	           maxlength:"Please enter maximum 50 characters",          
	        },

	        regisno: {              
	           required:"This field is required", 
	           alphanumeric:"Please enter only alphabetical characters", 
	           minlength:"Please enter at least 5 alphabetical characters",  
	           maxlength:"Please enter maximum 20 characters",          
	        },
	        regisdate: {              
	           required:"This field is required", 
      	        },
	        status: {              
	           required:"This field is required", 
            },
	        naturebusiness: {              
	          required:"This field required",         
	        }
	     
	    }
		  
	});


	$('#addressdetails').validate({
	   ignore: [], 
	   rules: {
	        country: {
	           required: true,
			},
	      	city: {
	           required: true,
	            pattern:"^[a-z A-Z\s]+$",
	            maxlength:50,
	         },
	     	region: {
	           required: true,
	           pattern:"^[a-z A-Z\s]+$",
	           maxlength:30,
	        },
	        building: {
	           required: true,
	           pattern: /^[a-zA-Z0-9\s,-]*$/,
	           maxlength:50
	        },
	        street: {
	           required: true,
	           pattern:/^[a-zA-Z0-9\s,-]*$/,
	           maxlength:20,
	        },
	        postalcode: {
	        	required: true,
	        	alphanumeric:true,
	        	maxlength:10,
	        },
	        pobox: {
	        	required: true,

	        },
	        email: {
	        	required: true,
	        	pattern:/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
	        	maxlength:50,
	        },
	        telephone: {
	        	required: true,
	        	digits:true,
	        	maxlength:10,
	        },
	        
	        mobile: {
	        	required: true,
	        	maxlength:10,
	        	digits:true,
	        },
	        faxno: {
	        	required: true,
	        	//maxlength:20,
	        },
	         corr_country: {              
                 required:true,
	        },
	        corr_city: {              
                 required:true,
	        },
	        corr_region: {
	           required: true,
	           pattern:"^[a-z A-Z\s]+$",
	           maxlength:30,
	        },
	        corr_building: {
	           required: true,
	           pattern:/^[a-zA-Z0-9\s,-]*$/,
	           maxlength:50,
	        },
	        corr_street: {
	           required: true,
	           pattern:/^[a-zA-Z0-9\s,-]*$/,
	           maxlength:20,
	        },
	        corr_postal: {
	        	required: true,
	        	alphanumeric:true,
	        	maxlength:10,
	        },
	         corr_pobox: {
	        	required: true,

	        },
	        corr_email: {
	        	required: true,
	        	pattern:/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
	        	maxlength:50,
	        },
	        corr_tele: {
	        	required: true,
	        	digits:true,
	        	maxlength:10,
	        },
	        
	        corr_mobile: {
	        	required: true,
	        	maxlength:10,
	        	digits:true,
	        },
	        corr_fax: {
	        	required: true,
	        	//maxlength:20,
	        },

	    },
	    messages:{
	        country: {              
	           required:"This field is required", 
	        },

	        city: {              
	           required:"This field is required", 
	           pattern:"Please enter valid city name",
	           maxlength:"Please enter maximum 50 characters",

      	    },
	        region: {              
	           required:"This field is required", 
	           pattern:"Please enter alphabetical only characters",
	           maxlength:"Please enter maximum 30 characters",
      	    },
      	    building: {
	           required:"This field is required", 
	           pattern:"Please enter only characters", 
	         //  minlength:"Please enter at least 2 characters",
	           maxlength:"Please enter maximum 50 characters", 
	        },
	        street: {
	           required:"This field is required", 
	            pattern:"Please enter only alphabetical characters", 
	            maxlength:"Please enter maximum 20 characters",
	        },
	        postalcode: {
	        	required:"This field is required", 
	            alphanumeric:"Please enter only alphabetical characters", 
	            maxlength:"Please enter maximum 10 characters",
	        },
	        pobox: {
	        	required:"This field is required", 
	        },
	        email: {
	        	required:"This field is required", 
	        	pattern:"Please enter a valid email address",
	        },
	        telephone: {
	        	required:"This field is required", 
	            maxlength:"Please enter maximum 10 number",
	        	digits:"Please enter valid telephone no",
	        },
	        
	        mobile: {
	        	required:"This field is required", 
	        	maxlength:"Please enter maximum 10 number",
	        	digits:"Please enter valid mobile no",
	        },
	        faxno: {
	        	required:"This field is required", 
	        },
	        corr_country: {
	        	required:"This field is required", 	
	        },
	        corr_city: {
	        	required:"This field is required", 	
	        },

	        corr_region: {
	           required:"This field is required",
	           
	        },
	        corr_building: {
	           required:"This field is required",
	           pattern:"Please enter only characters", 
	           maxlength:"Please enter maximum 50 characters", 
	        },
	        corr_street: {
	            required:"This field is required",
	            pattern:"Please enter only alphabetical characters", 
	            maxlength:"Please enter maximum 20 characters",
	        },
	        corr_postal: {
	            required:"This field is required", 
	            alphanumeric:"Please enter only alphabetical characters", 
	            maxlength:"Please enter maximum 10 characters",
	        },
	        corr_pobox: {
	        	required:"This field is required", 
	        },
	        corr_email: {
	        	required:"This field is required", 
	        	pattern:"Please enter a valid email address",
	        },
	        corr_tele: {
	        	required:"This field is required", 
	            maxlength:"Please enter maximum 10 number",
	        	digits:"Please enter valid telephone no",
	        },
	        
	        corr_mobile: {
	        	required:"This field is required", 
	        	maxlength:"Please enter maximum 10 number",
	        	digits:"Please enter valid mobile no",
	        },
	        corr_fax: {
	        	required:"This field is required", 
	        },
	       
	       
	     
	    }
		  
	});
	
	$('#shareholding_struc').validate({
	   ignore: [], 
	   rules: {
	   		company: {
	   		   required: true,
	   		},
	        "cname[]": {
	           required: true,
	           pattern:"^[a-z A-Z\s]+$",
	           minlength:2,
	           maxlength:50,
	        },
	        passportno: {
	           required: true,
	           alphanumeric:true,
	           minlength:5,
	           maxlength:20,
	        },
	        share_percentage: {
	        	required:true,
	        },
	     	 usd: {
	           required: true,
	           digits:true,
	        },


	    },
	    messages:{
	    	company: {
	    	   required:"This field is required", 
	    	},
	        "cname[]": {              
	           required:"This field is required", 
	           pattern:"Please enter only characters",   
	           minlength:"Please enter at least 2 characters",
	           maxlength:"Please enter maximum 50 characters",          
	        },

	        passportno: {              
	           required:"This field is required", 
	           alphanumeric:"Please enter only alphabetical characters", 
	           maxlength:"Please enter maximum 20 characters",          
	        },
	        share_percentage: {
	          required:"This field is required", 
	        },
	       	usd: {              
	           required:"This field is required", 
	           digits:"Please enter only digits",
	           maxlength:"Please enter maximum 30 digits",  
      	        },

	    }
		  
	});

	$('#financialform').validate({
	    
	   rules: {
	        yearly_usd: {
	          required: true,
	          maxlength:30,
	           
	        },
	        yearly_profit_usd: {
	           required: true,
	           maxlength:30,
	        },
	        total_debts_usd: {
	        	required: true,
	        	maxlength:30,
	        },
	        total_recei_usd: {
	        	required: true,
	        	maxlength:30,
	        },
	        total_cash_usd: {
	        	required: true,
	        	maxlength:30,
	        }

	    },
	    messages:{
	        yearly_usd: {              
	           required:"This field is required", 
         	   maxlength:"Please enter maximum 20 digits",
	        },

	        yearly_profit_usd: {              
	           required:"This field is required", 
         		maxlength:"Please enter maximum 20 digits",	
	        },
	        total_debts_usd: {
	        	required:"This field is required", 
	        	maxlength:"Please enter maximum 20 digits",
	        },
	        total_recei_usd: {
	        	required:"This field is required", 
	        	maxlength:"Please enter maximum 20 digits",
	        },
	        total_cash_usd: {
	        	required:"This field is required", 
	        	maxlength:"Please enter maximum 20 digits",
	        }

	    }
		  
	});

	$('#documentform').validate({
	 	    rules: {
		        "files[]": {
		          required: true,
		          extension: "pdf",
		        },
		        article_assoc: {
		         
		          extension: "pdf",
		        },
		        licence: {
		         
		          extension: "pdf",
		        },
		        director_passport: {
		          
		          extension: "pdf",
		        },
		        police_certificate: {
		          
		          extension: "pdf",
		        },
		        bankreference: {
		        
		          extension: "pdf",
		        },
		        lawfirm: {
		        	
		          extension: "pdf",
		        },
		         auditorreference: {
		          
		          extension: "pdf",
		        },
		        auditor_financial: {
		         
		          extension: "pdf",
		        },
	        /*termcondition: {
	        	required:true,
	        }*/

	     },
	        messages:{
		        "files[]": {              
		           required:"This field is required", 
	         	   extension: "Please upload only pdf file",
		         },
		          article_assoc: {
		          
	         	   extension: "Please upload only pdf file",
		        },
		        licence: {
		          
	         	   extension: "Please upload only pdf file",
		        },
		        director_passport: {
		        
	         	   extension: "Please upload only pdf file",
		        },
		        police_certificate: {
		         
	         	   extension: "Please upload only pdf file",
		        },
		        bankreference: {
		         
	         	   extension: "Please upload only pdf file",
		        },
		        lawfirm: {
		        	
	         	   extension: "Please upload only pdf file",
		        },
		         auditorreference: {
		         
	         	   extension: "Please upload only pdf file",
		        },
		        auditor_financial: {
		         
	         	   extension: "Please upload only pdf file",
		        }
	        }
	});

	

</script>
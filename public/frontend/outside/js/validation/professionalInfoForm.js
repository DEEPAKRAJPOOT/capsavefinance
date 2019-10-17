/* global messages, message */
try {
    jQuery(document).ready(function ($) {


        $('.datepicker').keydown(function (e) {
            e.preventDefault();
            return false;
        });
        
        $('.datepicker').on('paste',function (e) {
            e.preventDefault();
            return false;
        });
        
        
        $.validator.addMethod('alphnum_hyp_uscore_space', function (value) { 
            return /^[A-Za-z0-9\s_-]+$/.test(value); 
        },messages.alphnum_hyp_uscore_space);
        
        $.validator.addMethod('alpha_num', function (value) { 
            return /^[A-Za-z0-9]+$/.test(value); 
        },messages.alpha_num);
        
        $.validator.addMethod('req_other_prof_status', function (value) { 
           var prof=$("#prof_status").val();
           if(prof=='8' && value ==''){
               return false;
           }
           return true;
        },messages.req_this_field);
        
        $.validator.addMethod('req_prof_detail', function (value) { 
           var prof=$("#prof_status").val();
           if((prof=='1' || prof=='3'||prof=='4') && value ==''){
               return false;
           }
           return true;
        },messages.req_this_field);
        
        $.validator.addMethod('req_employment', function (value) { 
           var prof=$("#prof_status").val();
          
           if((prof=='1' || prof=='3'||prof=='4'||prof=='6') && value ==''){
               return false;
           }
           return true;
        },messages.req_this_field);
        
        $.validator.addMethod('decimal_num', function (value) { 
            if(value !='' && value!=null){
               return /^-?[0-9]+(?:\.[0-9]{1,2})?$/.test(value); 
            }
            return true;
            
        },messages.invalid_amount);

        $('#professionalInformationForm').validate({
	   ignore: [], 
	   rules: {
	        prof_status: {
	           required: true,
	        },
                other_prof_status: {
                    req_other_prof_status:true,
                },
                prof_detail:{
                    req_prof_detail:true,
                },
                position_title:{
                    req_prof_detail:true,
                },
                date_employment:{
                    req_employment:true,
                },
                last_monthly_salary:{
                    req_employment:true,
                    decimal_num:true,
                }
	    },
	    messages:{
	        prof_status: {              
	          required:messages.req_this_field,         
	        },
                
	    }
		  
	});
    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

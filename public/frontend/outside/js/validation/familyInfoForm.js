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
        
        $.validator.addMethod('req_spouse_profession', function (value) { 
            var is_professional =   $("#is_professional_status").val();
            if(is_professional=='1' ||is_professional=='3'){
                if(value==''||value==null || value==undefined){
                    return false;
                }
            }
            
            return true;
        },messages.req_this_field);
        
        $.validator.addMethod('req_spouse_employer', function (value) { 
            var is_professional =   $("#is_professional_status").val();
            if(is_professional=='1'){
                if(value==''||value==null || value==undefined){
                    return false;
                }
            }
            
            return true;
        },messages.req_this_field);

        $('#familyInformationForm').validate({
	   ignore: [], 
	   rules: {
	        spouse_f_name: {
	           required: true,
	           pattern:"^[A-Za-z0-9_-]+$",
	           minlength:3,
	           maxlength:60,
	        }, 
                spouse_m_name: {
	           required: true,
	           pattern:"^[A-Za-z0-9_-]+$",
	           minlength:3,
	           maxlength:60,
	        },
                is_professional_status: {
	            required: true,
	        },
                spouse_profession:{
                   req_spouse_profession:true, 
                }
                ,
                spouse_employer:{
                   req_spouse_employer:true, 
                }
	    },
	    messages:{
	        spouse_f_name: {              
	          required:messages.req_this_field, 
	          pattern:messages.alphnum_hyp_uscore,   
	          minlength:messages.least_3_chars,
	          maxlength:messages.max_60_chars,          
	        },
                spouse_f_name: {              
	          required:messages.req_this_field, 
	          pattern:messages.alphnum_hyp_uscore,   
	          minlength:messages.least_3_chars,
	          maxlength:messages.max_60_chars,          
	        },
                is_professional_status: {
	            required: messages.req_this_field, 
	        } 
	    }
		  
	});
    });

} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

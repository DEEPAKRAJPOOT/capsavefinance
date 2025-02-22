try {
var decimalPlace = 2;
    $(document).ready(function(){
        $("input[type=text].formatNum").each(function( i ) {
            if (this.value != '') {
                if($(this).attr("decimalplace")) {
                        decimalPlaceTemp = parseInt($(this).attr("decimalplace"));
                } else {
                        decimalPlaceTemp = decimalPlace;
                }

                var muliplier = '1';
                while(muliplier.length < decimalPlaceTemp+1) {
                muliplier += '0';
                }
                var muliplier = parseInt(muliplier);
                var val = Math.round(Number(this.value) * muliplier) / muliplier;
                var parts = val.toString().split(".");
                var formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + (parseInt(parts[1]) < 10 ? parts[1]+0 : parts[1] ) : "");			
                this.value = formatted;
            }
        });

        //$("input[type=text].formatNum").on('input', function(){
        $(document).on('input', "input[type=text].formatNum", function(){            
                //this.value = this.value.match(/^\d+\.?\d{0,2}/);	
                if($(this).attr("decimalplace")) {
                        decimalPlaceTemp = parseInt($(this).attr("decimalplace"));
                } else {
                        decimalPlaceTemp = decimalPlace;
                }			
                var expression = "^\\d+\\.?\\d{0,"+decimalPlaceTemp.toString()+"}";
                var rx = new RegExp(expression, 'i');
                this.value = this.value.match(rx);				
        });

        //$("input[type=text].formatNum").on('focus', function(){
        $(document).on('focus', "input[type=text].formatNum", function(){
                this.value = this.value.replace(/,/gi, "");
        });	

        //$("input[type=text].formatNum").on('blur', function(){
        $(document).on('blur', "input[type=text].formatNum", function(){
                if (this.value == '') return false;
                if($(this).attr("decimalplace")) {
                        decimalPlaceTemp = parseInt($(this).attr("decimalplace"));
                } else {
                        decimalPlaceTemp = decimalPlace;
                }			
                //const options = { 
                //  minimumFractionDigits: decimalPlaceTemp,
                //  maximumFractionDigits: decimalPlaceTemp 
                //};	
                //const formatted = Number(this.value).toLocaleString('en', options);	
                var muliplier = '1';
                while(muliplier.length < decimalPlaceTemp+1) {
                muliplier += '0';
                }
                var muliplier = parseInt(muliplier);			
                var val = Math.round(Number(this.value) * muliplier) / muliplier;
                var parts = val.toString().split(".");
                var formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + (parseInt(parts[1]) < 10 ? parts[1]+0 : parts[1] ) : "");			
                this.value = formatted;
        });
        
        $.validator.addMethod("uniqueCharge",
            function(value, element, params) {
                var result = true;
                var data = {chrg_name : value, _token: common_vars.token};
                if (params.chrg_id) {
                    data['chrg_id'] = params.chrg_id;
                }
                $.ajax({
                    type:"POST",
                    async: false,
                    url: common_vars.unique_charge_url, // script to validate in server side
                    data: data,
                    success: function(data) {                        
                        result = (data.status == 1) ? false : true;
                    }
                });                
                return result;                
            },'Charge is already exists'
        );

        $( "form" ).submit(function( event ) {
            $("input[type=text].formatNum").each(function( i ) {
                if (this.value != "") {
                    this.value = this.value.replace(/,/gi, "");
                }
            });
                        
            $('.cls-chrg-type:disabled').removeAttr('disabled');
            $('.cls-is-gst-applicable:disabled').removeAttr('disabled');
            $('.charge_calculation_type:disabled').removeAttr('disabled');
            $('.cls-chrg-applicable-id:disabled').removeAttr('disabled');
            $('.cls-chrg-trigger-id:disabled').removeAttr('disabled');            
            
        });
        
        $(document).on('input', "input[type=text].amtpercnt", function(){     
            if($(this).attr("decimalplace")) {
                    decimalPlaceTemp = parseInt($(this).attr("decimalplace"));
            } else {
                    decimalPlaceTemp = decimalPlace;
            }			
            var expression = "^\\d+\\.?\\d{0,"+decimalPlaceTemp.toString()+"}";
            var rx = new RegExp(expression, 'i');
            this.value = this.value.match(rx);            
            var amt = Number(this.value);            
            if (amt > 100) {
                 this.value = 100;
            }
        });
              
    });    
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
function copyAddress(id,th){
	console.log(id);
	if($(th).is(':checked')){
		$(id+' input[name*=biz_other_address]').val($('input[name=biz_address]').val());
		$(id+' input[name*=biz_other_city]').val($('input[name=biz_city]').val());
		$(id+' select[name*=biz_other_state]').val($('select[name=biz_state]').val());
		$(id+' input[name*=biz_other_pin]').val($('input[name=biz_pin]').val());
	}else{
		$(id+' input[name*=biz_other_address]').val('');
		$(id+' input[name*=biz_other_city]').val('');
		$(id+' select[name*=biz_other_state]').val('');
		$(id+' input[name*=biz_other_pin]').val('');
	}
}

$(document).ready(function(){
	$('.pan-verify').on('click',function(){
		let pan_no = $('input[name=biz_pan_number]').val().trim();
		if(pan_no.length != 10){
			$('input[name=biz_pan_number] +span').remove();
			$('input[name=biz_pan_number]').after('<span class="text-danger error">Enter valid PAN Number</span>');
			return false;
		}
		$('.isloader').show();
		$.ajax({
			url: "https://gst.karza.in/prod/v1/search",//https://gst.karza.in/uat/v1/search
			type: "POST",
			data: JSON.stringify({"consent": "Y","pan": pan_no}),
			dataType:'json',
			headers:{"Content-Type": "application/json", "x-karza-key": "NX1nBICr7TNEisJ"},
			error:function (xhr, status, errorThrown) {
				$('.isloader').hide();
    			alert(errorThrown);
			},
			success: function(res){
			    if(res['statusCode'] == 101){
			    	//$('#pan-msg').show();
			    	$('.pan-verify').text('Verified');
			    	$('.pan-verify').css('pointer-events','none');
			    	$('input[name=biz_pan_number]').attr('readonly',true);
			    	$('input[name=biz_pan_number] +span').remove();
			    	fillGSTinput(res.result);
			    }else{
			    	alert('No GST associated with the entered PAN.');
			    }
			    $('.isloader').hide();
			  }
		});
	})
})

function fillGSTinput(datas){
	let res ='';
	let option_html = '<option value="">Select GST Number</option>';
	$(datas).each(function(i,data){
		if(data.authStatus == 'Active'){
			res += data.gstinId+',';
			option_html += '<option value="'+data.gstinId+'">'+data.gstinId+'</option>';
		}
	})
	$('select[name=biz_gst_number]').html(option_html);
	$('input[name=pan_api_res]').val(res);
	//$('#business_information_form input[type=submit]').prop("disabled", false);
}

function fillEntity(gstinId){
	if(gstinId == ''){
		return false;
	}
	$('.isloader').show();
	$.ajax({
			url: "https://gst.karza.in/uat/v1/gst-verification",//https://gst.karza.in/prod/v1/gst-verification
			type: "POST",
			data: JSON.stringify({"consent": "Y","gstin": gstinId}),
			dataType:'json',
			headers:{"Content-Type": "application/json", "x-karza-key": "h3JOdjfOvay7J8SF"},
			error:function (xhr, status, errorThrown) {
				$('.isloader').hide();
    			alert(errorThrown);
			},
			success: function(res){
			    if(res['statusCode'] == 101){
			    	$('input[name=biz_entity_name]').val(res.result.lgnm);
			    	getCIN(res.result.lgnm);
			    	fillRegisteredAddress(res.result.pradr.adr);
			    }else{
			    	alert('Something went wrong, Try again later');
			    }
			    $('.isloader').hide();
			}
		});
}

function getCIN(entityName){
	$.ajax({
		url: "https://api.karza.in/v2/compsearch-lite",//https://testapi.karza.in/v2/compsearch-lite
		type: "POST",
		data: JSON.stringify({"consent": "Y","companyName": entityName}),
		dataType:'json',
		headers:{"Content-Type": "application/json", "x-karza-key": "NX1nBICr7TNEisJ"},
		error:function (xhr, status, errorThrown) {
			alert(errorThrown);
		},
		success: function(res){
		    if(res['status-code'] == 101){
		    	$('input[name=biz_cin]').val(res.result[0].cin);
		    }else{
		    	alert('Something went wrong, Try again later');
		    }
		}
	});
}

function checkValidation(){
	if($('.pan-verify').text() == 'Verify' || $('biz_cin').val() == ''){
		alert('Please fill and verify Business PAN First');
		return false;
	}else if($('biz_cin').val()  == ''){
		alert('Service unavailable!');
		return false;
	}else{
		return true;
	}
}

function fillRegisteredAddress(addr_str){
	try {
		let addr_array = addr_str.split(',');
		let pin = addr_array.pop().replace(/pin:/,'').trim();
		let state = addr_array.pop().trim();
		let city = addr_array.pop().trim();
		let address = addr_array.toString().trim();
		$('input[name=biz_address]').val(address);
		$('select[name=biz_state] option:contains('+state+')').attr('selected', true);
		$('input[name=biz_city]').val(city);
		$('input[name=biz_pin]').val(pin);
		//return {'address': address, 'city': city, 'state': state, 'pin': pin};
	}
	catch(err) {
	  console.error(err);
	}
}

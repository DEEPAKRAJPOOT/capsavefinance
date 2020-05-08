$(document).ready(function() {
    $('#addressForm').validate({ // initialize the plugin
        rules: {
            'addr_1': {
                required: true,
            },
            'city_name': {
                required: true,
            },
            'state_id': {
                required: true,
            },
            'pin_code': {
                required: true,
                digits: true,
            },
            'is_active': {
                required: true,
            },
        },
        messages: {
            'addr_1': {
                required: "Please enter Address",
            },
            'city_name': {
                required: "Please enter city name",
            },
            'state_id': {
                required: "Please enter state name",
            },
            'pin_code': {
                required: "Please enter pincode",
            },
            'is_active': {
                required: "Please select status",
            },
        }
    });
});

let pincode = document.getElementById('pin_code');

pincode.addEventListener('input', function() {
    let pinVal =  document.getElementById('pin_code').value;
        let pinStr = pinVal.toString();
    if (isNaN(pincode.value) || pinStr.length >= 7) {
        pincode.value = "";
    }
});

function fillAddress(gstinId){
    $('input[name=addr_1]').val('');
    $('select[name=state_id] option:selected').prop('selected', true);
    $('input[name=city_name]').val('');
    $('input[name=pin_code]').val('');

    if(gstinId == ''){
        return false;
    }
    parent.$('.isloader').show();
    $.ajax({
        url: messages.get_address_by_gst,
        type: "POST",
        data: {"consent": "Y","gstin": gstinId, "_token":messages.token},
        dataType:'json',
        error:function (xhr, status, errorThrown) {
            parent.$('.isloader').hide();
            alert(errorThrown);
        },
        success: function(res){
            let temp = res;
            let bizPanGstId = $('#gst_no option:selected').data('id');
            res = res.response;
            if(res == null){
                parent.$('.isloader').hide();
                alert('No Address associated with the entered GST.');
            }else if(res['statusCode'] == 101){
                fillRegisteredAddress(res.result.pradr.adr);
                $('input[name=biz_pan_gst_api_id]').val(temp.pgapiId);
                $('input[name=biz_pan_gst_id]').val(bizPanGstId);
                parent.$('.isloader').hide();
            }else{
                parent.$('.isloader').hide();
                alert('No Address associated with the entered GST.');
            }
        }
    });
}

function fillRegisteredAddress(addr_str){
    try {
        let addr_array = addr_str.split(',');
        let pin = addr_array.pop().replace(/pin:/,'').trim();
        let state = addr_array.pop().trim();
        let city = addr_array.pop().trim();
        let address = addr_array.toString().trim();
        $('input[name=addr_1]').val(address);
        $('select[name=state_id] option:contains('+state+')').prop('selected', true);
        $('input[name=city_name]').val(city);
        $('input[name=pin_code]').val(pin);
        //return {'address': address, 'city': city, 'state': state, 'pin': pin};
    }
    catch(err) {
      console.error(err);
    }
}
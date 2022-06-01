
    $(document).on('click', '.disburseClickBtn', function(){
        var invoiceIds = $('#invoice_ids').val().trim();
        var databankType = $(this).attr('data-bankType');
        if (invoiceIds.length == 0) {
            replaceAlert('Please select atleast one invoice', 'error');
            return false;
        }
        if (databankType === '2') {
            let allInvIds = invoiceIds.split(',');
            if (allInvIds.length > 1) {
                replaceAlert('Please select only one invoice', 'error');
                return false;
            }
        }
        var dataUrl = $(this).attr('data-url');
        var newUrl = dataUrl+'&invoice_ids='+invoiceIds;
        $('#openDisburseInvoice').attr('data-url', newUrl);
        $('#openDisburseInvoice').trigger('click');
    });

    $(document).on('click', '.invoice_id', function(){
        let current_id = $(this).val();
        if($(this).is(':checked')){
            let parent_inv_ids = $('#invoice_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            if(allInvIds.length != 0){
                allInvIds.push(current_id);
                //allInvIds.join();
                $('#invoice_ids').val(allInvIds.join());
            }else{
                $('#invoice_ids').val(current_id);
            }
            
        }else{
            let parent_inv_ids = $('#invoice_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            allInvIds = allInvIds.filter(e => e !== current_id);
            $('#invoice_ids').val(allInvIds.join());
        }
    });
    
    $(document).on('click', '#chkAll', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            let parent_inv_ids = $('#invoice_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if (!parent_inv_ids.length) {
                allInvIds = [];
            }
            $('input:checkbox').attr('checked', 'checked');
            $("input:checkbox[name=checkinvoiceid]:checked").each(function () {
                let current_id = $(this).val();
                allInvIds.push(current_id);
                allInvIds.join();
            });
            $('#invoice_ids').val(allInvIds.join());
        } else {
            let parent_inv_ids = $('#invoice_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if (!parent_inv_ids.length) {
                allInvIds = [];
            }
            $("input:checkbox[name=checkinvoiceid]:checked").each(function () {
                let current_id = $(this).val();
                allInvIds = allInvIds.filter(e => e !== current_id);
            });
            $('#invoice_ids').val(allInvIds.join());
            $('input:checkbox').removeAttr('checked');
        }
    });

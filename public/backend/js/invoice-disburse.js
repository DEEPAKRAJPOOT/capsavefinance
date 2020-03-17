
    $(document).on('click', '.disburseClickBtn', function(){
        var invoiceIds = $('#invoice_ids').val().trim();
        var dataUrl = $(this).attr('data-url');
        var newUrl = dataUrl+'&invoice_ids='+invoiceIds;
        $('#openDisburseInvoice').attr('data-url', newUrl);
        $('#openDisburseInvoice').trigger('click');
    });

    $(document).on('click', '.invoice_id', function(){
        let current_id = $(this).val();
        console.log(current_id);
        if($(this).is(':checked')){
            let parent_inv_ids = $('#invoice_ids').val().trim();
            let allInvIds = parent_inv_ids.split(',');
            if(!parent_inv_ids.length){
                allInvIds = [];
            }
            if(allInvIds.length != 0){
                allInvIds.push(current_id);
                allInvIds.join();
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

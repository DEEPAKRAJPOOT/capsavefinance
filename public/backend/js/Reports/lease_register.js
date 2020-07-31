try {
    var oTable;
    jQuery(document).ready(function ($) {
        oTable = $('#lease_register_report').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 50,
            bSort: false,
            responsive: true,
            searching: false,
            ajax: {
                "url": messages.get_all_lease_registers, // json datasource
                "method": 'POST',
                data: function (d) {
                    d.from_date = $('input[name="from_date"]').val();
                    d.to_date = $('input[name="to_date"]').val();
                    d.user_id = $('input[name=user_id]').val();
                    d._token = messages.token;
                },
                "error": function () {
                    $("#lease_register_report").append('<tbody class="error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                    $("#lease_register_report_processing").css("display", "none");
                }
            },
            "drawCallback": function( settings ) {
                excelUrl = settings.json.excelUrl;
                $('#dwnldEXCEL').attr('href', excelUrl)
                pdfUrl = settings.json.pdfUrl;
                $('#dwnldPDF').attr('href', pdfUrl)
            },
            columns: [
                {data: 'state'},
                {data: 'gstn'},
                {data: 'user_id'},
                {data: 'customer_name'},
                {data: 'customer_address', width:'200px'},
                {data: 'customer_gstn'},
                {data: 'sac_code'},
                // {data: 'contract_no'},
                {data: 'invoice_no'},
                {data: 'invoice_date'},
                {data: 'base_amount'},
                {data: 'sgst_rate'},
                {data: 'sgst_amount'},
                {data: 'cgst_rate'},
                {data: 'cgst_amount'},
                {data: 'igst_rate'},
                {data: 'igst_amount'},
                {data: 'total_amt'},
                {data: 'total_rate'},
                {data: 'total_tax'},
                {data: 'cash_flow'},
                {data: 'considered_in'},
            ],
            aoColumnDefs: [{'bSortable': false, 'aTargets': [1,2,3]}]
        });

        //Search
        $('#searchbtn').on('click', function (e) {
            oTable.draw();
        });

    });

        $('#from_date').datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2, });
        $('#to_date').datetimepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            minView: 2, });


      var sample_data = new Bloodhound({
       datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
       queryTokenizer: Bloodhound.tokenizers.whitespace,
       prefetch:messages.get_customer,
       remote:{
        url:messages.get_customer+'?query=%QUERY',
        wildcard:'%QUERY'
       }
      });
      
    
    $('#prefetch .form-control').typeahead(null, {
        name: 'sample_data',
        display: 'customer_id',
        source:sample_data,
        limit: 'Infinity',
        templates:{
            suggestion:Handlebars.compile(' <div class="row"> <div class="col-md-12" style="padding-right:5px; padding-left:5px;">@{{biz_entity_name}} <small>( @{{customer_id}} )</small></div> </div>') 
        },
    }).bind('typeahead:select', function(ev, suggestion) {
        setClientDetails(suggestion)
    }).bind('typeahead:change', function(ev, suggestion) {
        var customer_id = $.trim($("#customer_id").val());
        if(customer_id != suggestion)
        setClientDetails(suggestion)
    }).bind('typeahead:cursorchange', function(ev, suggestion) {
        setClientDetails(suggestion)
    });
    
    function setClientDetails(data){
        $("#biz_id").val(data.biz_id);
        $("#user_id").val(data.user_id);
        $("#customer_id").val(data.customer_id);
    }

    function download(action){
        url = '';
        from_date = $('input[name="from_date"]').val().trim();
        to_date = $('input[name="to_date"]').val().trim();
        customer_id = $('input[name=customer_id]').val().trim();
        if(action.trim() == 'pdf'){
            url = messages.pdf_soa_url;
        }

        if(action.trim() == 'excel'){
            url = messages.excel_soa_url;
        }

        if(from_date){
            url += '&from_date='+from_date;
        }

        if(to_date){
            url += '&to_date='+to_date;
        }

        window.open(url, '_blank');
    }

    function showClientDetails(data){
        $.ajax({
            type: "POST",
            url: messages.get_soa_client_details,
            data: data,
            dataType: "json",
            success: function (res) {
                var html = `<table class="table " cellpadding="0" cellspacing="0" style="margin-bottom: 22px;border-top-style: none;
                border-left-style: none;
                border-right-style: none;
                border-bottom-style: none;">
                            <tbody>
                                <tr>
                                    <td><b>Client Name</b></td>
                                    <td>`+res.client_name+`</td>
                                    <td><b>Date & Time</b></td>
                                    <td>`+res.datetime+`</td>
                                </tr>
                                <tr>
                                    <td><b>Address</b></td>
                                    <td>`+res.address+`</td>
                                    <td><b>Currency</b></td>
                                    <td>`+res.currency+`</td>
                                </tr>
                                
                                <tr>
                                    <td><b>Limit Amt</b></td>
                                    <td>`+res.limit_amt+`</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>`; 
                        //console.log(html);
                        //$("#client_details").html(html);
            }
        });
    }
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
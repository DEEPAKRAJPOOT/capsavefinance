try {
    var oTable;
    jQuery(document).ready(function ($) {
        
            $('#addAppNote').validate({
                rules: {
                    notes: {
                       required: true
                    }
                },
                messages: {
                }
            });
                     
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

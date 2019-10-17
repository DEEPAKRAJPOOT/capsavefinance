/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        $("#ProBackendUser").validate({
            rules: {
                first_name: {
                    required: true,
                },
                last_name: {
                    required: true,
                },
                phone: {
                    required: true,
                },
                at_phone: {
                    required: true,
                },
                addr1: {
                    required: true,
                    alphanumeric: true,
                },
                addr2: {
                    alphanumeric: true,
                },
                country_id: {
                    required: true,
                },
                zip_code: {
                    required: true,
                    alphanumeric: true,
                },
            },
            messages: {
                first_name: {
                    required: 'First name required.',
                },
                last_name: {
                    required: 'Last name required.',
                }
            }
        });
        
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

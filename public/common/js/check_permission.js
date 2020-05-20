/* global messages */

try {
    jQuery(document).ready(function ($) {

   
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        
        //console.log( "Triggered ajaxComplete handler. The result is ", xhr.responseText, settings.url);
        
        var response = JSON.parse(xhr.responseText);
        //if ( settings.url === "http://admin.rent.local/update_invoice_approve" ) {
            if (response.access_denied == '1') {
                window.location = "/access-denied";
            }
        //}
    });


    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
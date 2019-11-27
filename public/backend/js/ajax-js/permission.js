
/* global messages, message */

try {
        jQuery(document).ready(function ($) {
            $('input:checkbox').click(function()
            {
                //$('#yourid').attr('name')   
                var fname = $(this).attr('name');
                var exist = fname.indexOf('child');
                if(exist == 0){
                    var clsName = (this.className).split(' ')['0'];//c-chk-10
                     var prtClsName = clsName.replace("c-", "p-");
                     let checks = $('.'+clsName).is(':checked');
                     $('.'+prtClsName).prop('checked', checks);

                }else{
                    var clsName = (this.className).split(' ')['0'];//c-chk-10
                     var prtClsName = clsName.replace("p-", "c-");
                     let checks = $('.'+clsName).is(':checked');
                     $('.'+prtClsName).prop('checked', checks);
                }
             }) 
            
        }) 
        
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}

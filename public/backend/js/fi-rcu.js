/* 
 * Using for application FI and RCU 
 * To handle validation and form data
 */

function triggerRCU()
{   
    var docIds = [];
    $. each($("input[name='documentIds']:checked"), function(){
        docIds. push($(this). val());
    });
    
    
}

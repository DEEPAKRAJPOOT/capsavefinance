/* global messages, message */

try {

    var oTable;
    jQuery(document).ready(function ($) {
        
        var APISecret   = messages.APISecret;
        var gatwayurl   = messages.gatwayurl;
        var contentType = messages.contentType;
        var gatwayhost = messages.gatwayhost;
        var apiKey     = messages.apiKey;
        var groupId    = messages.groupId;
        
        var content    = messages.content;
        var token    = messages.token;
        var token2    = messages.token2;
        var get_users_wci    =messages.get_users_wci;
        //console.log(content);
        
         console.log(JSON.stringify(content))
        return false;
        //alert(APISecret);
        
        function generateAuthHeader(dataToSign){
            var hash = CryptoJS.HmacSHA256(dataToSign,APISecret);
            return hash.toString(CryptoJS.enc.Base64);
        }
        
       var date = new Date().toGMTString();
       //var content = request.data;
       var content = '{\n  \"groupId\":\"0a3687cf-68e5-171f-9a3a-1654000000d5\",\n  \"entityType\": \"INDIVIDUAL\",\n  \"providerTypes\": [\n    \"WATCHLIST\"\n  ],\n  \"name\": \"putin\",\n  \"secondaryFields\":[{\"typeId\": \"SFCT_2\",\"dateTimeValue\":\"1952-07-10\"}],\n  \"customFields\":[]\n}';
console.log(content)

var contentLength = unescape(encodeURIComponent(content)).length;

var dataToSign = "(request-target): post " + gatwayurl + "cases/screeningRequest\n" +
        "host: " + gatwayhost + "\n" +
        "date: " + date + "\n" +
        "content-type: " + contentType +"\n" +
        "content-length: " + contentLength + "\n" +
        content;


var hmac = generateAuthHeader(dataToSign);
var authorisation = "Signature keyId=\"" + apiKey + "\",algorithm=\"hmac-sha256\",headers=\"(request-target) host date content-type content-length\",signature=\"" + hmac + "\"";
console.log(authorisation);
       
     
            var myKeyVals = { _token : token, authorisation : authorisation, currentDate : date, Signature : hmac,ContentLength : contentLength,content : content};
            //var get_users_wci = "{{ URL::route('get_users_wci') }}";

           jQuery('.getSimilarWCI').click( function() {
              alert(get_users_wci);


                $.ajax({
                     type: 'POST',
                     url: messages.get_users_wci,
                     data: myKeyVals,
                     dataType: "text",
                     success: function(resultData) {
                        $('#similarRecords').html(resultData);

                    }
             });

    });
       
/////////////////////// based on one click       
        jQuery(document).on('click','.getfullDetail', function() {
              var getfullDetailID = $(this).attr('id');
             var radioValue = $("input[name='kycdetailID']:checked"). val();
             // alert(getfullDetailID);
              var getfullDetailIDArray = getfullDetailID.split('_');
              var DynamicIdval  = getfullDetailIDArray[1];
              var resultDataArray = radioValue.split('#');
              
                var ReferenceId = resultDataArray[0];
                var Name = resultDataArray[1];
                var Category = resultDataArray[2];
                var  providerTypes = resultDataArray[3];
                var Gender = resultDataArray[4];
                var DOB = resultDataArray[5];
                var Country = resultDataArray[6];
                var IdentityType = resultDataArray[7];
                var identityDocumentsNumber = resultDataArray[8];  
                var get_users_wci_single    =messages.get_users_wci_single; 
        

//////////////////////////////
var date = new Date().toGMTString();




var gatwayurl = '/v1/';
var contentType = 'application/json';
var gatwayhost = 'rms-world-check-one-api-pilot.thomsonreuters.com';
var apiKey  = 'c295daa1-c765-4a2e-ae4b-2fcae14b9070';
var groupId  = '0a3687cf-68e5-171f-9a3a-1654000000d5';
var profileId  = ReferenceId;




var date = new Date().toGMTString();

var encoded = encodeURIComponent(profileId);


var dataToSign = "(request-target): get " + gatwayurl + "reference/profile/" + encoded + "\n" +
        "host: " + gatwayhost + "\n" +
        "date: " + date;
var hmac = generateAuthHeader(dataToSign);
var authorisation = "Signature keyId=\"" + apiKey + "\",algorithm=\"hmac-sha256\",headers=\"(request-target) host date\",signature=\"" + hmac + "\"";



/////////////////////////////
var myKeyVals = { _token : token2, authorisation : authorisation, currentDate : date, Signature : hmac,profileID : profileId};
                $.ajax({
                     type: 'POST',
                     url: get_users_wci_single,
                     data: myKeyVals,
                     dataType: "text",
                     success: function(resultData) { //alert(resultData)

                            //jQuery(document).html('#profileDetail_'+DynamicIdval, resultData)





                            $('#profileDetail_'+DynamicIdval).html(resultData);
                    }
             });

          })
       
      
       
/////////////////////////////
    });
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
//////////////////////////
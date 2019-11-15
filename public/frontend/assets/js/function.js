function ModalContract(){
 document.getElementById('CreateContract').classList.add('show'); 
}

function ModalContractClose(){
 document.getElementById('CreateContract').classList.remove('show'); 
}


function db_blockchain(){
 document.getElementById('db_blockchain').classList.add('show'); 
}



function db_blockchainClose(){
 document.getElementById('db_blockchain').classList.remove('show'); 
}

 /* Create Contract Tabs */ 

var a = document.querySelectorAll(".category-ul li span");
for (var i = 0, length = a.length; i < length; i++) {
  a[i].onclick = function() {
    var b = document.querySelector(".category-ul li.active");
    if (b) b.classList.remove("active");
    this.parentNode.classList.add('active');
  };
}




function myCheckbox() {
    
  var checkBox = document.getElementById("checkbox1");
  var text = document.getElementById("Check-box");
  if (checkBox.checked == true){
    text.style.display = "block";
  } else {
     text.style.display = "none";
  }
}


/* Suppliers-modal */

function SuppliersModal(){
 document.getElementById('SuppliersModal').classList.add('show'); 
}

function SuppliersModalClose(){
 document.getElementById('SuppliersModal').classList.remove('show'); 
}


function SelectCart(obj){
     var selectvalue = document.createElement("div");
     selectvalue.setAttribute('class', 'chat-message');
     selectvalue.innerHTML = obj.innerHTML
    var count = document.getElementById('SelectValue').childElementCount; 
    //console.log(count + ' c')
    if(count == 0){
        document.getElementById('SelectValue').appendChild(selectvalue);
    }else {
        var item = document.getElementById("SelectValue").childNodes[1];
        console.log(item)
   document.getElementById('SelectValue').replaceChild(selectvalue, item);
    }
    
  
}



function modalOpen(){
 document.getElementById('myModal').classList.add('show'); 
}

function modalClose(){
 document.getElementById('myModal').classList.remove('show'); 
}



function NewCommodityOpen(){
 document.getElementById('NewCommodity').classList.add('show'); 
}

function NewCommodityClose(){
 document.getElementById('NewCommodity').classList.remove('show'); 
}







/* Bank */

function show1(){
  document.getElementById('Bank1').style.display ='block';
  document.getElementById('Bank2').style.display ='none';
}
function show2(){
  document.getElementById('Bank2').style.display = 'block';
  document.getElementById('Bank1').style.display = 'none';
}


/* Accordion */
	var accItem = document.getElementsByClassName('accordionItem');
    var accHD = document.getElementsByClassName('accordionItemHeading');
    for (i = 0; i < accHD.length; i++) {
        accHD[i].addEventListener('click', toggleItem, false);
    }
    function toggleItem() {
        var itemClass = this.parentNode.className;
        for (i = 0; i < accItem.length; i++) {
            accItem[i].className = 'accordionItem closed';
        }
        if (itemClass == 'accordionItem closed') {
            this.parentNode.className = 'accordionItem open';
        }
    }






/*  Edit Commodity Modal */

function incrementValue()
{
    var value = parseInt(document.getElementById('number').value, 10);
    value = isNaN(value) ? 0 : value;
    if(value<30000){
        value++;
            document.getElementById('number').value = value;
    }
}
function decrementValue()
{
    var value = parseInt(document.getElementById('number').value, 10);
    value = isNaN(value) ? 0 : value;
    if(value>1){
        value--;
            document.getElementById('number').value = value;
    }

}

/* Profile image upload */




window.addEventListener('load', function() {
  document.querySelector('input[type="file"]').addEventListener('change', function() {
      if (this.files && this.files[0]) {
          var img = document.getElementById('myImg');  // $('img')[0]
          img.src = URL.createObjectURL(this.files[0]); // set src to file url
          img.onload = imageIsLoaded; // optional onload event listener
      }
      
  });
});



/* */



   function FileDetails(clicked_id) {
        // GET THE FILE INPUT.
        var fi = document.getElementById('file_'+clicked_id);
        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
        if (fi.files.length > 0) {

            // THE TOTAL FILE COUNT.
        var x = 'filePath_'+clicked_id; 
            //var x = document.getElementById(id);alert(id);
            document.getElementById(x).innerHTML = '';

            // RUN A LOOP TO CHECK EACH SELECTED FILE.
            for (var i = 0; i <= fi.files.length - 1; i++) {

                var fname = fi.files.item(i).name;      // THE NAME OF THE FILE.
                var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.
                // SHOW THE EXTRACTED DETAILS OF THE FILE.
                document.getElementById(x).innerHTML =
                    '<div class="file-name" id="fileId"> ' +
                        fname + '' + '<button type="button"  class="close-file" onclick="myDelete()" > x'+ '</button>' +'</div>';
            }
        }
        else { 
            alert('Please select a file.') ;
        }
    }

function myDelete(){
   document.getElementById("fileId").remove();   
}


function addNewDoc() {
  var doc_name = document.getElementById("doc_name").value;
  if (doc_name === '') {
      alert("Please enter the document name.");
      return false;
  }else{
        var uni_id = Number(document.getElementById("hidden_id").value);
        var n = uni_id + 1;
        var d = document.createElement( 'div' );
        d.id = "new1";
        d.setAttribute('class', 'form-group');
        d.innerHTML = '<div class="justify-content-center d-flex"><label class="mb-0"><span class="file-icon"><img src="../assets/images/onbording/file-icon.svg"> </span>'+doc_name+'<span class="help-icon" flow="right" tooltip="Copy of Latest Telephone bill / Latest Electricity bill / Valid Registered Rental Agreement / Latest bank statements received by customer by post ( Not of co-operative bank and approved by L&amp;C) / Sales Tax Registration Certificate / Factory Registration Certificate / SEBI Registration Certificate / Form 18 &amp; ROC Receipt">i </span></label><div class="ml-auto"><div class="file-browse"><button class="btn btn-primary btn-sm">Add Document</button><input type="file" id="file_'+n+'" dir="'+n+'" onchange="FileDetails('+n+')" multiple></div></div></div><div id="filePath_'+n+'" class="filePath"></div><hr>' ;
document.getElementById("hidden_id").value = n;
        var p = document.getElementById('newDoc');
        p.appendChild(d);
        document.getElementById("doc_name").value = '';
    }
  
}





/* Select Commodities */
var inxArr = []
function getText(inx){
    if(inxArr.indexOf(inx) == -1){
        
  var x = document.getElementById("myBtn"+inx).textContent;
    var dv = document.createElement("div");
    dv.setAttribute('class', 'file-name');
    dv.setAttribute("id", "DelShowName"+inx);
    dv.innerHTML = '<span>'+ x +'<button type="button" class="close-file" onclick="showTextDel('+inx+')"> x'+'</button>'+'</span>';
    document.getElementById('SelectName').appendChild(dv);
    var x = document.getElementById("myBtn"+inx).classList.add('active');
    inxArr.push(inx)
    }else{
        var x = document.getElementById("myBtn"+inx).classList.remove('active');
        showTextDel(inx)
        inxArr.pop(inx)
        
    }
  
}

function showTextDel(inx){
   document.getElementById("DelShowName"+inx).remove();
    
}



/* Other Documents Page */
var inx = 0
function addElement(){
    inx++
    var dv = document.createElement("div");
     dv.setAttribute("id", "DelDocumentShow");
    //dv.setAttribute('class', 'other-documents');
    dv.innerHTML = '<hr><ul class="other-documents"><li><span class="file-icon"><img src="../assets/images/onbording/file-icon.svg"> </span></li><li> <input type="text" class="custum-control"></li><li><div class="document-name-top">Valid Upto</div><div class="document-name-bottom">(If applicable )</div></li><li> <input type="text" class="custum-control year" placeholder="MM" maxlength="2" max="2"> <input type="text" class="custum-control year" placeholder="YYYY" maxlength="4" max="4"></li><li><div class="file-browse"> <button class="btn btn-primary btn-sm">Add Document</button> <input type="file" id="file'+inx+'" onchange="FileBrowse('+inx+')" multiple/></div><button class="delete-icon" onclick="DelDocument()"><i class="fa fa-trash" aria-hidden="true"></i></button></li></ul><div class="form-group pr-10" id="filePath'+inx+'"></div>';
    document.getElementById('main-container').appendChild(dv);
  
}


function DelDocument(){
   document.getElementById("DelDocumentShow").remove();
    
}

 function FileBrowse(inx) {

        // GET THE FILE INPUT.
        var fi = document.getElementById('file'+inx);

        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
        if (fi.files.length > 0) {

            // THE TOTAL FILE COUNT.
            document.getElementById('filePath'+inx).innerHTML ='';

            // RUN A LOOP TO CHECK EACH SELECTED FILE.
            for (var i = 0; i <= fi.files.length - 1; i++) {

                var fname = fi.files.item(i).name;      // THE NAME OF THE FILE.
                var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.
                 
                // SHOW THE EXTRACTED DETAILS OF THE FILE.
                document.getElementById('filePath'+inx).innerHTML =
                document.getElementById('filePath'+inx).innerHTML + '<div class="file-name" id="fileReName"> ' +
                fname + '' + '<button type="button" class="close-file" onclick="DelImageName()"> x'+ '</button>' +'</div>';
            }
        }
        else { 
            alert('Please select a file.') 
        }
    }


function DelImageName(){
   document.getElementById('fileReName').remove();
    
}









/* Hide and show div */

/*
var tog = document.getElementById('ChooseCommodityTop');
var thing = document.getElementById('ChooseCommodityBottom');

tog.addEventListener('click', function(){
  thing.classList.toggle('open');
});

*/





function SelectNewCommodity(obj){
     var newcommodityvalue = document.createElement("li");
     newcommodityvalue.setAttribute('class', 'new-com-value');
     newcommodityvalue.innerHTML = obj.innerHTML
    var count = document.getElementById('NewCommodityFile').childElementCount; 
    //console.log(count + ' c')
    if(count == 0){
        document.getElementById('NewCommodityFile').appendChild(newcommodityvalue);
      //  var element = document.getElementById("ChooseCommodityBottom");
    }else {
        var item = document.getElementById("NewCommodityFile").childNodes[1];
        console.log(item)
   document.getElementById('NewCommodityFile').replaceChild(newcommodityvalue, item);
        //var element.classList.remove("open");
    }
    
  
}







/* Profile */

function AddProfileShow(){
 document.getElementById('AddProfile').classList.add('show'); 
}

function AddProfileClose(){
 document.getElementById('AddProfile').classList.remove('show'); 
}




/* Administrator  */

     function myFunction(){
           var bb =  document.getElementById('SelectBox').value;
           var element = document.createElement('div');
           document.getElementById('SelectBoxValue').appendChild(element);
           element.setAttribute("class", "file-name");
           element.innerHTML = bb;
           document.getElementById('SelectBoxValue').appendChild(element);      
     }



/* Menu Responsive */

var html = document.documentElement; // pega o elemento HTML da página

document.querySelector('.open-menu').onclick = function(){
  html.classList.add('responsive-active');
};

document.querySelector('.close-menu').onclick = function(){
  html.classList.remove('responsive-active'); 
};

html.onclick = function(event){
  if (event.target === html){
    html.classList.remove('responsive-active');
  }
}



function ModalEmailModal(){
 document.getElementById('EmailModal').classList.add('show'); 
}

function EmailModalClose(){
 document.getElementById('EmailModal').classList.remove('show'); 
}



function toggle(ID){
    var element = document.getElementById(ID);
    if(element.style.display === "none"){
        element.style.display = "block";
    }else{
        element.style.display = "none";
    }
}




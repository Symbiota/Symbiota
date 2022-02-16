function isNumeric(inStr){
   	var validChars = "0123456789-.";
   	var isNumber = true;
   	var charVar;

   	for(var i = 0; i < inStr.length && isNumber == true; i++){ 
   		charVar = inStr.charAt(i); 
		if(validChars.indexOf(charVar) == -1){
			isNumber = false;
			break;
      	}
   	}
	return isNumber;
}

function toggle(target){
	var ele = document.getElementById(target);
	if(ele){
		if(ele.style.display=="none"){
			ele.style.display="";
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
	else{
		var divObjs = document.getElementsByTagName("div");
	  	for (i = 0; i < divObjs.length; i++) {
	  		var divObj = divObjs[i];
	  		if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
				if(divObj.style.display=="none"){
					divObj.style.display="";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}
	}
}

function openIndividualPopup(clientRoot, occid,clid){
    var wWidth = 1000;
    if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
	if(wWidth > 1200) wWidth = 1200;
    newWindow = window.open(clientRoot+'/collections/individual/index.php?occid='+occid+'&clid='+clid,'indspec' + occid,'scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=800,left=20,top=20');
    if(newWindow.opener == null) newWindow.opener = self;
    return false;
}

function openPopup(url){
    var wWidth = 1000;
    if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
	if(wWidth > 1200) wWidth = 1200;
    newWindow = window.open(url,'genericPopup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=800,left=20,top=20');
    if(newWindow.opener == null) newWindow.opener = self;
    return false;
}
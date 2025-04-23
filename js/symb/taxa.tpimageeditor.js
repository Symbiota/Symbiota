function toggle(target){
	var obj = document.getElementById(target);
	if(obj){
		if(obj.style.display=="none"){
			obj.style.display="block";
		}
		else {
			obj.style.display="none";
		}
	}
	else{
		var spanObjs = document.getElementsByTagName("span");
		for (i = 0; i < spanObjs.length; i++) {
			var spanObj = spanObjs[i];
			if(spanObj.getAttribute("class") == target || spanObj.getAttribute("className") == target){
				if(spanObj.style.display=="none"){
					spanObj.style.display="inline";
				}
				else {
					spanObj.style.display="none";
				}
			}
		}

		var divObjs = document.getElementsByTagName("div");
		for (var i = 0; i < divObjs.length; i++) {
			var divObj = divObjs[i];
			if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
				if(divObj.style.display=="none"){
					divObj.style.display="block";
				}
				else {
					divObj.style.display="none";
				}
			}
		}
	}
}

function expandImages(){
	var divCnt = 0;
	var divObjs = document.getElementsByTagName("div");
	for (i = 0; i < divObjs.length; i++) {
		var obj = divObjs[i];
		if(obj.getAttribute("class") == "extraimg" || obj.getAttribute("className") == "extraimg"){
			if(obj.style.display=="none"){
				obj.style.display="inline";
				divCnt++;
				if(divCnt >= 5) break;
			}
		}
	}
}

function submitAddForm(f){
	var imgFileInput = f.elements["imgfile"];
    var originalUrlInput = f.elements["originalUrl"];

    var imgUploadPath = imgFileInput && imgFileInput.files.length > 0 ? imgFileInput.files[0].name.trim() : "";
    var originalUrl = originalUrlInput ? originalUrlInput.value.trim() : "";

	if (imgUploadPath === "" && originalUrl === "") {
        alert(translations.FILE_PATH_MISSING);
        return false;
    }

    if(isNumeric(f.sortsequence.value) == false){
		alert(translations.SORT_NOT_NUMBER);
		return false;
    }
    return true;
}

function isNumeric(sText){
   	var ValidChars = "0123456789-.";
   	var IsNumber = true;
   	var Char;
 
   	for (var i = 0; i < sText.length && IsNumber == true; i++){ 
	   Char = sText.charAt(i); 
		if (ValidChars.indexOf(Char) == -1){
			IsNumber = false;
			break;
      	}
   	}
	return IsNumber;
}
		

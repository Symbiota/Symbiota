$(document).ready(function() {
	if(!navigator.cookieEnabled){
		alert("Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.");
	}
	$('#tabs').tabs({ active: tabIndex }).css({
		'min-height': '500px',
		'overflow': 'auto'
	});
});

function openIndPopup(occid){
	openPopup('../individual/index.php?occid=' + occid);
}

function openEditorPopup(occid){
	openPopup('../editor/occurrenceeditor.php?occid=' + occid);
}

function openPopup(urlStr){
	var wWidth = 900;
	if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
	if(wWidth > 1400) wWidth = 1400;
	newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
	window.name = "parentWin";
	if(newWindow.opener == null) newWindow.opener = self;
	return false;
}

function toggle(target){
	var objDiv = document.getElementById(target);
	if(objDiv){
		if(objDiv.style.display=="none"){
			objDiv.style.display = "block";
		}
		else{
			objDiv.style.display = "none";
		}
	}
	else{
	  	var divs = document.getElementsByTagName("div");
	  	for (var h = 0; h < divs.length; h++) {
	  	var divObj = divs[h];
			if(divObj.className == target){
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

// For use with correspondance attachments
function verifyFileSize(inputObj){
	if (!window.FileReader) {
		//alert("The file API isn't supported on this browser yet.");
		return;
	}
	var maxUpload = 10000000; // 10 MB
	var file = inputObj.files[0];
	if(file.size > maxUpload){
		var msg = "Attachment "+file.name+" ("+Math.round(file.size/100000)/10+"MB) is larger than is allowed (current limit: "+(maxUpload/1000000)+"MB).";
		alert(msg);
	}
}

// For use with correspondance attachments
function verifyFileUploadForm(f){
	var fileName = "";
	if(f.uploadfile){
		if(f.uploadfile && f.uploadfile.value){
			 fileName = f.uploadfile.value;
		}
		if(fileName == ""){
			alert("File path is empty. Please select the file to attach.");
			return false;
		}
		else{
			var ext = fileName.split('.').pop();
			if(ext == 'pdf' || ext == 'PDF') return true;
			else if(ext == 'doc' || ext == 'DOC') return true;
			else if(ext == 'docx' || ext == 'DOCX') return true;
			else if(ext == 'xls' || ext == 'XLS') return true;
			else if(ext == 'xlsx' || ext == 'XLSX') return true;
			else if(ext == 'txt' || ext == 'TXT') return true;
			else if(ext == 'csv' || ext == 'CSV') return true;
			else if(ext == 'jpg' || ext == 'JPG') return true;
			else if(ext == 'jpeg' || ext == 'JPEG') return true;
			else if(ext == 'png' || ext == 'PNG') return true;
			else{
				alert("File must be a PDF (.pdf), MS Word document (.doc or .docx), MS Excel file (.xls or .xlsx), image (.jpg, .jpeg, or .png). or a text file (.txt, .csv).");
				return false;
			}
		}
	}
	return true;
}
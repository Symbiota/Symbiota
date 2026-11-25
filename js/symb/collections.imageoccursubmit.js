$(document).ready(function() {

	$("#catalognumber").keydown(function(evt){
		var evt  = (evt) ? evt : ((event) ? event : null);
		if ((evt.keyCode == 13)) { return false; }
	});

});

//Validate forms
function validateImgOccurForm(f){
	if(f.imgurl.value == "" && f.imgfile.value == ""){
		alert("Local image must be select or a image URL entered");
		return false;
	}
	
	return true;
}

//Misc
function dwcDoc(dcTag){
	dwcWindow=open("https://docs.symbiota.org/Editor_Guide/Editing_Searching_Records/symbiota_data_fields#"+dcTag,"dwcaid","width=1250,height=300,left=20,top=20,scrollbars=1");
	//dwcWindow=open("http://rs.tdwg.org/dwc/terms/index.htm#"+dcTag,"dwcaid","width=1250,height=300,left=20,top=20,scrollbars=1");
	if(dwcWindow.opener == null) dwcWindow.opener = self;
	dwcWindow.focus();
	return false;
}
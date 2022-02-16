$('html').hide();
$(document).ready(function() {
	$('html').show();
});

$(document).ready(function() {
	$('#tabs').tabs({ 
		active: tabIndex,
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});
});

function openPointAid(latDef,lngDef) {
	var tid = document.pointaddform.pointtid.value;
	pointWindow=open("tools/mappointaid.php?latcenter="+latDef+"&lngcenter="+lngDef+"&tid="+tid,"pointaid","resizable=0,width=800,height=800,left=20,top=20");
    if(pointWindow.opener == null) pointWindow.opener = self;
}

function togglePoint(f){
	var objDiv = document.getElementById('pointlldiv');
	if(objDiv){
		if(f.pointtid.value == ""){
			objDiv.style.display = "none";
		}
		else{
			objDiv.style.display = "block";
		}
	}
}

function verifyPointAddForm(f){
	if(f.pointtid.value == ""){
		alert("Please select a taxon");
		return false;
	}
	if(f.pointlat.value == "" || f.pointlng.value == ""){
		alert("Please enter coordinates");
		return false;
	}
	return true;
}

function showImagesDefaultChecked(f){
	if(f.dimages.checked){
		f.dvouchers.checked = false;
		f.dvouchers.disabled = true;
		f.dauthors.checked = false;
		f.dauthors.disabled = true;
	}
	else{
		f.dvouchers.disabled = false; 
		f.dauthors.disabled = false; 
	}
}

function validateAddChildForm(f){
	
}
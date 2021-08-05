//Table function only
function detectBatchUpdateField(){
	var fieldSelected = document.getElementById('bufieldname').value;
	if(fieldSelected == "processingstatus"){
		var processingStatus = document.getElementById("processingStatus").innerHTML;
		var buNewValue = '<select name="bunewvalue">';
		buNewValue += processingStatus;
		buNewValue += '</select>';
		document.getElementById("bunewvaluediv").innerHTML = buNewValue;
	}
	else if(!$("input[name='bunewvalue']").val()){
		document.getElementById("bunewvaluediv").innerHTML = '<input name="bunewvalue" type="text" value="" />';
	}
}

function submitBatchUpdate(f){
	var fieldName = f.bufieldname.options[f.bufieldname.selectedIndex].value;
	var oldValue = f.buoldvalue.value;
	var newValue = f.bunewvalue.value;
	var buMatch = 0;
	if(f.bumatch[1].checked) buMatch = 1;
	if(!fieldName){
		alert("Please select a target field name");
		return false;
	}
	if(!oldValue && !newValue){
		alert("Please enter a value in the current or new value fields");
		return false;
	}
	if(oldValue == newValue){
		alert("The values within current and new fields cannot be equal to one another");
		return false;
	}

	$.ajax({
		type: "POST",
		url: "rpc/batchupdateverify.php",
		dataType: "json",
		data: { collid: f.collid.value, fieldname: fieldName, oldvalue: oldValue, bumatch: buMatch }
	}).done(function( retCnt ) {
		if(confirm("You are about to update "+retCnt+" records.\nNote that you won't be able to undo this Replace operation!\nDo you want to continue?")){
			f.submit();
		}
	});
}

function toggleSearch(){
	if(document.getElementById("batchupdatediv")) document.getElementById("batchupdatediv").style.display = "none";
	toggle("querydiv");
}

function toggleBatchUpdate(){
	document.getElementById("querydiv").style.display = "none";
	toggle("batchupdatediv");
}

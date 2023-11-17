$(document).ready(function() {
	$("#acceptedstr").autocomplete({ 
		source: "rpc/getacceptedsuggest.php",
		focus: function( event, ui ){
			$("#tidaccepted").val("");
		},
		select: function( event, ui ){
			if(ui.item) $("#tidaccepted").val(ui.item.id);
		},
		change: function( event, ui ){
			if(!$("#tidaccepted").val()){
				alert("You must select a name from the list. If accepted name is not in the list, it needs to be added or it is in the system as a non-accepted synonym");
			}
		},
		minLength: 2, 
		autoFocus: true 
	});
	
	$("#parentname").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/gettaxasuggest.php", { term: request.term, rhigh: $("#rankid").val() }, response );
		},
		focus: function( event, ui ){
			$("#parenttid").val("");
		},
		select: function( event, ui ){
			if(ui.item) $("#parenttid").val(ui.item.id);
		},
		change: function( event, ui ){
			if(!$("#parenttid").val()){
				alert("You must select a name from the list. If parent name is not in the list, it may need to be added");
			}
		},
		minLength: 2,
		autoFocus: true
	});
});

function verifyLoadForm(f){
	if(f.sciname.value == ""){
		alert("Scientific Name field required.");
		return false;
	}
	if(f.unitname1.value == ""){
		alert("Unit Name 1 (genus or uninomial) field required.");
		return false;
	}
	var rankId = f.rankid.value;
	if(rankId == ""){
		alert("Taxon rank field required.");
		return false;
	}
	if(f.parentname.value == "" && rankId > "10"){
		alert("Parent taxon required");
		return false;
	}
	if(f.parenttid.value == "" && rankId > "10"){
		alert("Parent identifier is not set! Make sure to select parent taxon from the list");
		return false;
	}

	//If name is not accepted, verify accetped name
	var accStatusObj = f.acceptstatus;
	if(accStatusObj[0].checked == false){
		if(f.acceptedstr.value == ""){
			alert("Accepted name needs to have a value");
			return false
		}
	}

	return true;
}

function parseName(f){
	var sciName = f.sciname.value;
	sciName = sciName.replace(/^\s+|\s+$/g,"");
	f.reset();
	f.sciname.value = sciName;
	var sciNameArr = new Array(); 
	var activeIndex = 0;
	var unitName1 = "";
	var rankId = "";
	sciNameArr = sciName.split(' ');

	if(sciNameArr[activeIndex].length == 1){
		f.unitind1.value = sciNameArr[activeIndex];
		if(sciNameArr[activeIndex].toLowerCase() == "x"){
			f.unitind1.selectedIndex = 1;
			f.sciname.value = "×"+f.sciname.value.substring(1);
		}
		f.unitname1.value = sciNameArr[activeIndex+1];
		unitName1 = sciNameArr[activeIndex+1];
		activeIndex = 2;
	}
	else{
		f.unitname1.value = sciNameArr[activeIndex];
		unitName1 = sciNameArr[activeIndex];
		activeIndex = 1;
	}
	if(sciNameArr.length > activeIndex){
		if(sciNameArr[activeIndex].length == 1){
			f.unitind2.value = sciNameArr[activeIndex];
			if(sciNameArr[activeIndex].toLowerCase() == "x"){
				f.unitind2.selectedIndex = 1;
				f.sciname.value = f.sciname.value.replace(" x "," × ");
			}
			f.unitname2.value = sciNameArr[activeIndex+1];
			activeIndex = activeIndex+2;
		}
		else{
			f.unitname2.value = sciNameArr[activeIndex];
			activeIndex = activeIndex+1;
		}
		rankId = 220;
	}
	if(sciNameArr.length > activeIndex){
		if(sciNameArr[activeIndex].substring(sciNameArr[activeIndex].length-1,sciNameArr[activeIndex].length) == "." || sciNameArr[activeIndex].length == 1){
			rankName = sciNameArr[activeIndex];
			f.unitind3.value = sciNameArr[activeIndex];
			f.unitname3.value = sciNameArr[activeIndex+1];
			if(sciNameArr[activeIndex] == "ssp." || sciNameArr[activeIndex] == "subsp.") rankId = 230;
			if(sciNameArr[activeIndex] == "var.") rankId = 240;
			if(sciNameArr[activeIndex] == "f.") rankId = 260;
			if(sciNameArr[activeIndex] == "x" || sciNameArr[activeIndex] == "X") rankId = 220;
		}
		else{
			f.unitname3.value = sciNameArr[activeIndex];
			rankId = 230;
		}
	}
	if(unitName1.length > 4 && (unitName1.indexOf("aceae") == (unitName1.length - 5) || unitName1.indexOf("idae") == (unitName1.length - 4))){
		rankId = 140;
	}
	f.rankid.value = rankId;
	if(rankId > 180){
		let parentName = "";
		if(f.rankid.value == 220) parentName = f.unitname1.value; 
		else if(f.rankid.value > 220) parentName = f.unitname1.value + " " + f.unitname2.value; 
		if(parentName) setParent(parentName, f.unitind1.value);
	}
	checkNameExistence(f);
}

function setParent(parentName, unitind1){
	$.ajax({
		type: "POST",
		url: "rpc/gettid.php",
		async: true,
		data: { sciname: parentName }
	}).done(function( msg ) {
		if(msg == 0){
			if(!unitind1) alert("Parent taxon '"+parentName+"' does not exist. Please first add parent to system.");
			else{
				setParent(unitind1 + " " + parentName, "");
			}
		}
		else{
			if(msg.indexOf(",") == -1){
				document.getElementById("parentname").value = parentName;
				document.getElementById("parenttid").value = msg;
			}
			else alert("Parent taxon '"+parentName+"' is matching two different names in the thesaurus. Please select taxon with the correct author.");;
		}
	});
}

function updateFullname(f){
	let sciname = "";
	if(f.unitind1.value) sciname = f.unitind1.value+" ";
	sciname = sciname + f.unitname1.value+" ";
	if(f.unitind2.value) sciname = sciname + f.unitind2.value+" ";
	if(f.unitname2.value) sciname = sciname + f.unitname2.value+" ";
	if(f.unitind3.value) sciname = sciname + f.unitind3.value+" ";
	if(f.unitname3.value) sciname = sciname + f.unitname3.value;
	f.sciname.value = sciname.trim();
	checkNameExistence(f);
}

function checkNameExistence(f){
	$.ajax({
		type: "POST",
		url: "rpc/gettid.php",
		async: false,
		data: { sciname: f.sciname.value, rankid: f.rankid.value, author: f.author.value }
	}).done(function( msg ) {
		if(msg != '0'){
			alert("Taxon "+f.sciname.value+" "+f.author.value+" ("+msg+") already exists in database");
			return false;
		}
	});
}

function acceptanceChanged(f){
	var accStatusObj = f.acceptstatus;
	if(accStatusObj[0].checked) document.getElementById("accdiv").style.display = "none";
	else document.getElementById("accdiv").style.display = "block";
}

// listener for taxon rank

document.getElementById('rankid').addEventListener('change', function () {
	let selectedValue = this.value; // Get the chosen value
	$rankId = selectedValue;

	let div1 = document.getElementById('div1hide');
	let div2 = document.getElementById('div2hide');
	let label = document.getElementById('unitind1label');

	if (selectedValue > 150) {
		div1.style.display = 'block';
		div2.style.display = 'block';
	} else {
		div1.style.display = 'none';
		div2.style.display = 'none';
	}
	if (selectedValue <= 180) {
		// Get the name of selected option
		let selectedOption = this.options[this.selectedIndex];
		let selectedOptionText = selectedOption.textContent.trim();

		// Set the label for "UnitName1" based on the selected option text
		label.textContent = selectedOptionText + ' Name';
	} else {
		label.textContent = 'Genus Name';
	}
});

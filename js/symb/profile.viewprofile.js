$(document).ready(function() {
	$('#tabs').tabs({ 
		active: tabIndex,
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});

});

function initTaxonAutoComplete(){
	$( "#taxoninput" ).autocomplete({
		source: "rpc/taxasuggest.php",
		minLength: 4,
		autoFocus: true
	});
}

function verifyAddTaxonomyForm(f){
	if(f.editorstatus.value == ""){
		alert("Select the Scope of Relationship");
		return false;
	}
	if(f.taxoninput.value == ""){
		alert("Select the Taxonomic Name");
		return false;
	}
	return true;
}

function verifyPwdForm(f){
    var pwd1 = f.newpwd.value;
    var pwd2 = f.newpwd2.value;
    if(pwd1 == "" || pwd2 == ""){
        window.alert("Both password fields must contain a value.");
        return false;
    }
	if(pwd1.charAt(0) == " " || pwd1.slice(-1) == " "){
		alert("Password cannot start or end with a space, but they can include spaces within the password");
		return false;
	}
	if(pwd1.length < 10){
		alert("Password must be 10 characters or more");
		return false;
	}
    if(pwd1 != pwd2){
        window.alert("Password do not match. Please enter again.");
        f.newpwd.value = "";
        f.newpwd2.value = "";
        f.newpwd.focus();
        return false;
    }
    return true;
}

function verifyModifyLoginForm(f){
    var newLogin = f.newlogin.value;
    if(newLogin == ""){
        window.alert("New login must have a value");
        return false;
    }
	if( /[^0-9A-Za-z_!@#$-+.]/.test( newLogin ) ) {
        alert("Login name should only contain 0-9A-Za-z_.!@ (spaces are not allowed)");
        return false;
    }
    if(f.newloginpwd){
		if(f.newloginpwd.value == "") {
			window.alert("Enter your password");
			return false;
		}
	}
    return true;
}

function toggleEditingTools(targetStr){
	document.getElementById("logineditdiv").style.display = "none";
	document.getElementById("pwdeditdiv").style.display = "none";
	document.getElementById("profileeditdiv").style.display = "none";
	toggle(targetStr);
}
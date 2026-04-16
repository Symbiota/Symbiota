$(document).ready(function() {
	if(!navigator.cookieEnabled){
		alert("Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.");
	}

	$('#tabs').tabs({
		active: getTabFromHash(),
		beforeLoad: function(event, ui) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});

	$('form').on('submit', function() {
		var active = $('#tabs').tabs('option', 'active');
		var href = $('#tabs a').eq(active).attr('href');

		this.action = this.action.replace(/#.*$/, '') + href;
	});

	function getTabFromHash() {
		var hash = window.location.hash;
		if (!hash) return 0;

		var index = $('#tabs a').map(function(i, el) {
			return $(el).attr('href');
		}).get().indexOf(hash);

		return index >= 0 ? index : 0;
	}
	
	if(parentChild){
		var url = '';
		if(document.getElementById("ReferenceTypeId").value == 2 || document.getElementById("ReferenceTypeId").value == 4 || document.getElementById("ReferenceTypeId").value == 7 || document.getElementById("ReferenceTypeId").value == 8){
			url = 'rpc/parenttitlelist.php';
		}
		if(document.getElementById("ReferenceTypeId").value == 3 || document.getElementById("ReferenceTypeId").value == 6){
			url = 'rpc/seriestitlelist.php';
		}
		$( "#secondarytitle" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function( request, response ) {
					$.getJSON( url, {
						term: request.term, t: function() { return document.referenceeditform.secondarytitle.value; }
					}, response );
				},
				search: function() {
					// custom minLength
					var term = this.value;
					if ( term.length < 3 ) {
						return false;
					}
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = this.value;
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.label );
					getParentInfo(ui.item.value);
					return false;
				}
			},{});
	}
	
	if(document.getElementById("ReferenceTypeId").value == 4){
		$( "#tertiarytitle" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function( request, response ) {
					$.getJSON( "rpc/seriestitlelist.php", {
						term: request.term, t: function() { return document.referenceeditform.tertiarytitle.value; }
					}, response );
				},
				search: function() {
					// custom minLength
					var term = this.value;
					if ( term.length < 3 ) {
						return false;
					}
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = this.value;
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.label );
					getParentInfo(ui.item.value);
					return false;
				}
			},{});
	}

	$("#taxa").on("autocompleteselect", function(event, ui) {
            $("#taxa_targetid").val(ui.item.id);
    });
});

function selectAll(source) {
	const checkboxes = document.querySelectorAll('input[name="scbox[]"]');
	checkboxes.forEach(cb => {
		cb.checked = source.checked;
	});
}

function getParentInfo(refid){
	var sutXmlHttp=GetXmlHttpObject();
	if (sutXmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	var refType = document.getElementById("ReferenceTypeId").value;
	
	var url="rpc/parentdetails.php?refid="+refid+"&reftype="+refType;
	
	var parentArr = [];
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState==4 && sutXmlHttp.status==200){
			parentArr = JSON.parse(sutXmlHttp.responseText);
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
	document.getElementById("parentRefId").value = parentArr['parentRefId'];
	document.getElementById("parentRefId2").value = parentArr['parentRefId2'];
	if(document.getElementById("secondarytitle")){
		document.getElementById("secondarytitle").value = parentArr['secondarytitle'];
	}
	if(document.getElementById("tertiarytitle")){
		document.getElementById("tertiarytitle").value = parentArr['tertiarytitle'];
	}
	if(document.getElementById("shorttitle")){
		document.getElementById("shorttitle").value = parentArr['shorttitle'];
	}
	if(document.getElementById("alternativetitle")){
		document.getElementById("alternativetitle").value = parentArr['alternativetitle'];
	}
	if(document.getElementById("pubdate")){
		document.getElementById("pubdate").value = parentArr['pubdate'];
	}
	if(document.getElementById("edition")){
		document.getElementById("edition").value = parentArr['edition'];
	}
	if(document.getElementById("volume")){
		document.getElementById("volume").value = parentArr['volume'];
	}
	if(document.getElementById("number")){
		document.getElementById("number").value = parentArr['number'];
	}
	if(document.getElementById("placeofpublication")){
		document.getElementById("placeofpublication").value = parentArr['placeofpublication'];
	}
	if(document.getElementById("publisher")){
		document.getElementById("publisher").value = parentArr['publisher'];
	}
	if(document.getElementById("isbn_issn")){
		document.getElementById("isbn_issn").value = parentArr['isbn_issn'];
	}
	if(document.getElementById("numbervolumnes")){
		document.getElementById("numbervolumnes").value = parentArr['numbervolumnes'];
	}
}

function addAuthorToRef(){
	var refauthid = document.getElementById('refauthorid').value;
	var sutXmlHttp=GetXmlHttpObject();
	if (sutXmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	
	var url="rpc/authormanager.php?refid="+refid+"&action=addauthor&refauthid="+refauthid;
	
	var authorList = '';
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState==4 && sutXmlHttp.status==200){
			authorList = sutXmlHttp.responseText;
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
	document.getElementById("authorlistdiv").innerHTML = authorList;
	document.getElementById("addauthorsearch").value = '';
	document.getElementById("refauthorid").value = '';
}

function deleteRefAuthor(refauthid){
	if (confirm("Are you sure you would like to remove this author from this reference?")) {
		var sutXmlHttp=GetXmlHttpObject();
		if (sutXmlHttp==null){
			alert ("Your browser does not support AJAX!");
			return;
		}
		
		var url="rpc/authormanager.php?refid="+refid+"&action=deleterefauthor&refauthid="+refauthid;
		
		var authorList = '';
		sutXmlHttp.onreadystatechange=function(){
			if(sutXmlHttp.readyState==4 && sutXmlHttp.status==200){
				authorList = sutXmlHttp.responseText;
			}
		};
		sutXmlHttp.open("POST",url,false);
		sutXmlHttp.send(null);
		document.getElementById("authorlistdiv").innerHTML = authorList;
		document.getElementById("addauthorsearch").value = '';
		document.getElementById("refauthorid").value = '';
	}
}

function openNewAuthorWindow(){
	var urlStr = 'authoreditor.php?refid='+refid+'&addauth=1';
	newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width=470,height=300');
	if (newWindow.opener == null) newWindow.opener = self;
	return false;
}

function processNewAuthor(f){
	var firstName = f.firstname.value;
	var middleName = f.middlename.value;
	var lastName = f.lastname.value;
	if(firstName == "" || lastName == ""){
		alert("Please enter the first and last name of the author.");
		return false;
	}
	var sutXmlHttp=GetXmlHttpObject();
	if (sutXmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	
	var url="rpc/authormanager.php?refid="+refid+"&action=createauthor&firstname="+firstName+"&midname="+middleName+"&lastname="+lastName;
	
	var authorList = '';
	
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState==4 && sutXmlHttp.status==200){
			authorList = sutXmlHttp.responseText;
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
	opener.document.getElementById("authorlistdiv").innerHTML = authorList;
	opener.document.getElementById("addauthorsearch").value = '';
	opener.document.getElementById("refauthorid").value = '';
	self.close();
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

function verifyNewAuthForm(f){
	if(document.getElementById("firstname").value == "" || document.getElementById("lastname").value == ""){
		alert("Please enter the first and last name of the author.");
		return false;
	}
	return true;
}

function verifyEditRefForm(f){
	if(!document.getElementById("bibliographicCitation").value){
        alert("Please enter the title of the reference.");
        return false;
    }
    if(!document.getElementById("title").value){
        alert("Please enter the title of the reference.");
        return false;
    }
    return true;
}

function verifyRefTypeChange(){
	if(document.getElementById("ReferenceTypeId").selectedIndex > 1){
		if (confirm("Are you sure you would like to change the reference type?")) {
			document.getElementById("dynamicInput").innerHTML = '<input name="formsubmit" type="hidden" value="Edit Reference" />';
			document.getElementById("referenceeditform").submit();
		}
	}
}

function updateIspublished(f){
	if(document.getElementById("ispublishedcheck").checked==true){
		document.getElementById("ispublished").value = "1";
	}
	else{
		document.getElementById("ispublished").value = "0";
	}
}

function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari, IE 7.x
		xmlHttp=new XMLHttpRequest();
	}
	catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
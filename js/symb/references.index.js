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

    $('#occurrenceSampleTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        stripeClasses: ['odd', 'even'],
		autoWidth: false,
		dom: 'frtip',
		className: 'compact'
	});
	
	

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

function decodeHTML(str){
	const txt = document.createElement("textarea");
	txt.innerHTML = str;
	return txt.value;
}
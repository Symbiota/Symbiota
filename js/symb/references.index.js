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

	$('#tabs form').on('submit', function() {
		var active = $('#tabs').tabs('option', 'active');
		var href = $('#tabs a').eq(active).attr('href');

		if(href){
			this.action = this.action.replace(/#.*$/, '') + href;
		}
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

$(function(){

    $("#checklist_search").autocomplete({
        source: function(request, response){
            $.getJSON("resource_suggest.php", {
                type: "checklist",
                term: request.term
            }, response);
        },
        minLength: 2,
        select: function(event, ui){
            $("#checklist_search").val(ui.item.label);
            $("#checklist_targetid").val(ui.item.value);

            $("#selectedChecklist").text("Selected: " + ui.item.label);

            return false;
        }
    });

	$("#dataset_search").autocomplete({
        source: function(request, response){
            $.getJSON("resource_suggest.php", {
                type: "dataset",
                term: request.term
            }, response);
        },
        minLength: 2,
        select: function(event, ui){
            $("#dataset_search").val(ui.item.label);
            $("#dataset_targetid").val(ui.item.value);

            $("#selectedDataset").text("Selected: " + ui.item.label);

            return false;
        }
    });

	$("#collection_search").autocomplete({
        source: function(request, response){
            $.getJSON("resource_suggest.php", {
                type: "collection",
                term: request.term
            }, response);
        },
        minLength: 2,
        select: function(event, ui){
            $("#collection_search").val(ui.item.label);
            $("#collection_targetid").val(ui.item.value);

            $("#selectedCollection").text("Selected: " + ui.item.label);

            return false;
        }
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
        alert("Please enter a citation for the reference.");
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

function stripHTML(str){
    if(!str || typeof str !== "string") return "";

    try {
        const div = document.createElement("div");
        div.innerHTML = str;
        let text = div.textContent || div.innerText || "";

        if(text) return text.trim();
    } catch(e){
        console.warn("DOM parse failed, falling back:", str);
    }

    return str.replace(/<[^>]*>/g, "").trim();
}

function toTitleCase(str){
    return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
}

function cleanTitle(raw){
	return raw
		.replace(/[\r\n]+/g, ' ')
		.replace(/\s+/g, ' ')
		.trim();
}
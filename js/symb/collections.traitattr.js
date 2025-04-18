$(document).ready(function() {
	setAttributeTree(null);

	$(".trianglediv").each(function(){
		var rootElem = $(this).closest("fieldset");
		var childClassArr = $(rootElem).find("div[class^=child]");
		if(childClassArr.length == 0) $(this).hide();
	});

	$("#taxonfilter").autocomplete({ 
		source: "rpc/getTaxonFilter.php", 
		dataType: "json",
		minLength: 3,
		select: function( event, ui ) {
			$("#tidfilter").val(ui.item.id);
		}
	});

	$("#taxonfilter").change(function(){
		$("#tidfilter").val("");
		if($( this ).val() != ""){
			$( "#filtersubmit" ).prop( "disabled", true );
			$( "#verify-span" ).show();
			$( "#notvalid-span" ).hide();
								
			$.ajax({
				type: "POST",
				url: "rpc/getTaxonFilter.php",
				data: { term: $( this ).val(), exact: 1 }
			}).done(function( msg ) {
				if(msg == ""){
					$( "#notvalid-span" ).show();
				}
				else{
					$("#tidfilter").val(msg[0].id);
				}
				$( "#filtersubmit" ).prop( "disabled", false );
				$( "#verify-span" ).hide();
			});
		}
	});
	
});

function traitChanged(elem){
	var elemType =  elem.getAttribute("type");
	var elemName = elem.getAttribute("name");
	var traitID = elemName.substring(8,elemName.length-2);
	$('input[name="traitid-'+traitID+'[]"]').each(function(){
		if(this.checked == false){
			//Uncheck children to match parent
			$("input:checkbox.child-"+this.value).each(function(){ this.checked = false; });
			$("input:radio.child-"+this.value).each(function(){ this.checked = false; });
		}
	});
	if((elemType == 'text' && elemType.value.trim() != '') || elem.checked == true){
		var parents = $(elem).parents("div");
		for (var i = 0; i < parents.length; i++) {
			var parDiv = parents[i];
			if($(parDiv).attr("id") == "traitdiv") break;
			var inputElem = $(parDiv).children("input");
			$( inputElem ).prop( "checked", true );
	    }
	}
	if(!sessionStorage.attributeTree || sessionStorage.attributeTree == 0){
		$('input[name="traitid-'+traitID+'[]"]').each(function(){
			if((elemType == 'text' && elemType.value.trim() != '') || this.checked == true){
				if(sessionStorage.attributeTree == 0) $("div.child-"+this.value).show();
			}
			else{
				if(sessionStorage.attributeTree == 0) $("div.child-"+this.value).hide();
			}
		});
	}
	$('button[name="submitform"]').prop('disabled', false);
}

function setAttributeTree(triggerElem){
	var toggleTree = false;
	if(triggerElem) toggleTree = true;
	var treeOpen = false;
	if(sessionStorage.attributeTree && sessionStorage.attributeTree == 1) treeOpen = true;
	if(toggleTree){
		if(treeOpen) treeOpen = false;
		else treeOpen = true;
	}
	var rootElem = $("#traitdiv");
	if(triggerElem) rootElem = $(triggerElem).closest("fieldset");
	if(treeOpen){
		$(rootElem).find("div[class^=child]").show();
		$(rootElem).find(".triangledown").show();
		$(rootElem).find(".triangleright").hide();
		sessionStorage.attributeTree = 1;
	}
	else{
		$(rootElem).find("div[class^=child]").hide();
		$(rootElem).find('.triangledown').hide();
		$(rootElem).find('.triangleright').show();
		sessionStorage.attributeTree = 0;
	}
}

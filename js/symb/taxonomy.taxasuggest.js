var clientRoot = "";
var multipleTermSupport = false;
var minLength = 3;

function initiateTaxaSuggest(inputID, tidID = null, taxonSearchType = 2) {
	const inputElem = $("#" + inputID);
	inputElem
		// don't navigate away from the field on tab when selecting an item
		.on("keydown", function (event) {
			// don't navigate away from the field on tab when selecting an item
			if (event.keyCode === $.ui.keyCode.TAB) {
				if ($(this).autocomplete("widget").is(":visible")) {
					$(this).trigger("select");
					event.preventDefault();
				}
			} 
		})
		.autocomplete({
			source(request, response) {
				$.getJSON(
					clientRoot + "/rpc/taxasuggest.php",
					{
						term: extractLast(request.term),
						searchType: taxonSearchType
					},
					response
				);
			},
			autoFocus: true,
			delay: 200,
			search() {
				//Sets minLength even when there is support for multiple terms	
				const term = extractLast(this.value);
				if(term.length <= minLength) return false;
				return true;
			},
			focus() {
				// prevent value inserted on focus
				return false;
			},
			select(event, ui) {
				if(multipleTermSupport){
					let terms = this.value.split(/,\s*/);
					let targetIndex = terms.length - 1;
					// Replace last term with select item
					terms[targetIndex] = ui.item.value;
					this.value = terms.join(", ");
					if(tidID){
						let tids = $("#" + tidID).val().split(/,\s*/);
						tids[targetIndex] = ui.item.id;
						$("#" + tidID).val(tids.join(","));
					}
				}
				else{
					this.value = ui.item.value;
					if(tidID) $("#" + tidID).val(ui.item.id);
				}
				return false;
			},
			change(event, ui) {
				if(!ui.item || this.value == ""){
					if(tidID){
					if(multipleTermSupport){
							let terms = this.value.split(/,\s*/);
							let targetIndex = terms.length - 1;
							let tids = $("#" + tidID).val().split(/,\s*/);
							tids[targetIndex] = "";
							$("#" + tidID).val(tids.join(","));
						}
					}
					else{
						$("#" + tidID).val("");						
					}
				}
			}
		}
	);
}

function extractLast(term) {
	//Returns the last search term whenever mulitple are entered separeted by a commas
		if(multipleTermSupport) return term.split(/,\s*/).pop();
		return term;
}

//Set base values and options
function setTaxaSuggestRootPath(clientRootPath) {
	clientRoot = clientRootPath;
}

function setMultipleTermSupport(inputBool) {
	if(inputBool) multipleTermSupport = true;
	else multipleTermSupport = false;
}

function setMinLength(inputLength) {
	minLength = inputLength;
}


function initiateTaxonSuggest(inputID, rLow, rHigh) {
	$("#" + inputID).autocomplete(
		{
			source: function (request, response) {
				$.getJSON(
					acUrl,
					{ term: request.term, ranklow: rLow, rankhigh: rHigh },
					response
				);
			},
			autoFocus: true,
		},
		{}
	);
}

function urlExists(url) {
	var http = new XMLHttpRequest();
	http.open("HEAD", url, false);
	http.send();
	return http.status != 404;
}

function verifyQuickSearch(f) {
	if (f.taxa.value == "") {
		alert("Scientific name?");
		return false;
	}
	return true;
}

function validateTaxon(f, submitForm, callback = f => {}) {
	if (f.taxa.value == "") {
		return false;
	} else {
		$.ajax({
			type: "POST",
			url: clientRoot + "/rpc/gettaxon.php",
			dataType: "json",
			data: { sciname: f.taxa.value },
		}).done(function (taxaObj) {
			var retCnt = Object.keys(taxaObj).length;
			if (retCnt == 0) {
				alert(
					"ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, contact your data administrator to add this species to the Taxonomic Thesaurus."
				);
			} else {
				if (retCnt == 1) {
					f.tid.value = Object.keys(taxaObj)[0];
				} else {
					f.tid.value = Object.keys(taxaObj)[0];
					//alert(Object.keys(taxaObj)[0]);
					//alert(Object.keys(taxaObj)[1]);
				}
		if(callback) callback(f)
		if (submitForm) f.submit();
			}
		});
		return false;
	}
}

var validSelection = false;

const taxaSuggestConfig = {
    clientRoot: "",
    taxonSearchType: 2,
    minLength: 3,
    restrictToList: false,
    taxAuthID: 1,
    multipleTermSupport: false,
    rankMinimum: '',
    rankMaximum: ''
};

function initiateTaxaSuggest(inputID, tidInputID = null) {
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
					taxaSuggestConfig.clientRoot + "/rpc/taxasuggest.php",
					{
						term: extractLast(request.term),
						searchType: taxaSuggestConfig.taxonSearchType,
						rankMin: taxaSuggestConfig.rankMinimum,
						rankMax: taxaSuggestConfig.rankMaximum
					},
					response
				);
			},
			autoFocus: true,
			delay: 200,
			search() {
				//Sets acMinLength even when there is support for multiple terms	
				const term = extractLast(this.value);
				if(term.length <= taxaSuggestConfig.minLength) return false;
				return true;
			},
			focus() {
				// prevent value inserted on focus
				return false;
			},
			select(event, ui) {
				validSelection = true;
				if(taxaSuggestConfig.multipleTermSupport){
					let terms = this.value.replace("],", "];").split(/;\s*/);
					let targetIndex = terms.length - 1;
					// Replace last term with select item
					terms[targetIndex] = ui.item.value;
					this.value = terms.join("; ");
				}
				else{
					this.value = ui.item.value;
					if(tidInputID){
						document.getElementById(tidInputID).value = ui.item.id;
					}
				}
				return false;
			},
			change: function(event, ui) {
				if (!ui.item) {
					validSelection = false;
					if(taxaSuggestConfig.restrictToList){
						alert("Selecting taxon from list is required");
					}
				}
			}
		}
	);
}

function extractLast(term) {
	//Returns the last search term whenever mulitple are entered separeted by a commas
	if(taxaSuggestConfig.multipleTermSupport) return term.split(/[;,]{1}\s*/).pop();
	return term;
}

//Misc support functions
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

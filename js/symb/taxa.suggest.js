const taxaSuggestConfig = {
	clientRoot: "",
	minLength: 3,
	includeAuthor: false,
	includeKingdom: false,
	multipleTermSupport: false,
	restrictToList: false,

	taxonSearchType: 2,
	limitToAccepted: false,
	fullOutput: false,
	extendQueryMatch: false,
	taxAuthID: 1,
	rankMinimum: '',
	rankMaximum: ''
};

function initiateTaxaSuggest(inputID, callback = null) {
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
						limitToAccepted: taxaSuggestConfig.limitToAccepted,
						fullOutput: taxaSuggestConfig.fullOutput,
						extendQueryMatch: taxaSuggestConfig.extendQueryMatch,
						taxAuthID: taxaSuggestConfig.taxAuthID,
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
				if(taxaSuggestConfig.multipleTermSupport){
					let terms = this.value.replace("],", "];").split(/;\s*/);
					let targetIndex = terms.length - 1;
					// Replace last term with select item
					terms[targetIndex] = ui.item.value;
					this.value = terms.join("; ");
				}
				else{
					this.value = ui.item.value;
				}
				return false;
			},
			change: function(event, ui) {
				const validSelection = !!ui.item;
				if (!validSelection) {
					if(taxaSuggestConfig.restrictToList){
						let errMsg = 'Selecting taxon name from list is required';
						if(translations.SELECT_FROM_LIST) errMsg = translations.SELECT_FROM_LIST;
						alert(errMsg);
					}
				}
				if(typeof callback === "function"){
					callback({
						valid: validSelection,
						item: ui.item,
						value: this.value,
						input: this
					});
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

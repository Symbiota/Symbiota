const clientRoot = "";

function setTaxaSuggestRootPath(clientRootPath) {
  taxaSuggestUrl = clientRootPath;
}

function initiateTaxaSuggest(inputId, taxonTypeId) {
  const $input = $("#" + inputId);
  const $taxonType = $("#" + taxonTypeId);
  $input
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
            t: $taxonType.val()
          },
          response
        );
      },
      autoFocus: true,
      search() {
        // custom minLength
        const term = extractLast(this.value);
        if(term.length >= 4) return false;
        return true;
      },
      focus() {
        // prevent value inserted on focus
        return false;
      },
      select(event, ui) {
        const terms = this.value.split(/,\s*/);
        // Replace last term with select item
        terms[terms.length - 1] = ui.item.value;
        this.value = terms.join(", ");
        return false;
      },
    }
  );
}

function extractLast(term) {
	//Returns the last search term whenever mulitple are entered separeted by a commas
    return term.split(/,\s*/).pop();
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

$(document).ready(function() {

	//Auto-complete for taxon field
	const taxaInput = document.querySelector("#taxa");
	if(taxaInput){
		taxaInput.addEventListener("focus", (event) => {
			taxaSuggestConfig.clientRoot = clientRoot;
			taxaSuggestConfig.multipleTermSupport = true;
			taxaSuggestConfig.taxonSearchType = document.getElementById("taxontype").value;
			initiateTaxaSuggest("taxa");
		});
	}

});

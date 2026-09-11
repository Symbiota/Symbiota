$(document).ready(function () {

	//Taxon autocomplete functions
	const taxaInput = document.querySelector("#ffsciname");
	if(taxaInput){
		taxaInput.addEventListener("focus", (event) => {
			const f = this.form;
			taxaSuggest.config.clientRoot = CLIENT_ROOT;
			taxaSuggest.config.fullOutput = true;
			taxaSuggest.config.includeAuthor = TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR;
			taxaSuggest.config.includeKingdom = TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM;
			taxaSuggest.initiate("ffsciname", function(result) {
				if(result.valid) {
					f.sciname.value = result.item.sciname;
					f.tidinterpreted.value = result.item.id;
					f.scientificnameauthorship.value = result.item.author;
					f.family.value = result.item.family;
					if( result.item.securityStatus == 1 && !f.cultivationstatus.checked == true) {
						f.recordsecurity.value = 1;
						securityChanged(f);
					}
				}
				else{
					f.tidinterpreted.value = "";
					f.scientificnameauthorship.value = "";
					f.family.value = "";
					if (f.securityreason.value == "") f.recordsecurity.value = 0;
					let msg = "";
					if(translations) msg = translations.SELECT_FROM_LIST;
					alert(msg);
				}

				fieldChanged("sciname");
				fieldChanged("tidinterpreted");
				fieldChanged("scientificnameauthorship");
				fieldChanged("family");
				fieldChanged("recordsecurity");
			});
		});
	}
	
	//Add for all determination form objects
	document.querySelectorAll("form.det-form").forEach(function(form) {
		const taxaInput = form.querySelector('input[name="sciname"]');
		if (taxaInput) {
			taxaInput.addEventListener("focus", function(event) {
				const f = event.currentTarget.form;
 	
				taxaSuggest.config.clientRoot = CLIENT_ROOT;
				taxaSuggest.config.fullOutput = true;
				taxaSuggest.config.includeAuthor = TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR;
				taxaSuggest.config.includeKingdom = TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM;
	
				taxaSuggest.initiate(taxaInput, function(result) {
					if (result.valid) {
						f.sciname.value = result.item.sciname;
						f.tidinterpreted.value = result.item.id;
						f.scientificnameauthorship.value = result.item.author;
						f.family.value = result.item.family;
						if (result.item.securityStatus == 1 && !f.cultivationstatus.checked) {
							f.recordsecurity.value = 1;
							securityChanged(f);
						}
					}
					else {
						f.tidinterpreted.value = "";
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						if (f.securityreason.value == "") f.recordsecurity.value = 0;
						let msg = "";
						if(translations) msg = translations.SELECT_FROM_LIST;
						alert(msg);
					}
				});
			});
		}
	});


	$("textarea[name=associatedtaxa]").autocomplete(
		{
			source: function (request, response) {
				$.getJSON(
					"rpc/getspeciessuggest.php",
					{ term: extractLast(request.term) },
					response
				);
			},
			search: function () {
				// custom minLength
				var term = extractLast(this.value);
				if (term.length < 4) return false;
			},
			focus: function () {
				// prevent value inserted on focus
				return false;
			},
			select: function (event, ui) {
				var terms = split(this.value);
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push(ui.item.value);
				this.value = terms.join(", ");
				return false;
			},
		},
		{ autoFocus: true }
	);

	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}
	
	// Event/locality autocomplete functions
	if (localityAutoLookup) {
		$("#fflocality").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: "rpc/getlocality.php",
					data: {
						recordedby: $("input[name=recordedby]").val(),
						eventdate: $("input[name=eventdate]").val(),
						locality: request.term,
					},
					success: function (data) {
						response(data);
					},
				});
			},
			minLength: 4,
			select: function (event, ui) {
				$.each(ui.item, function (k, v) {
					var elem = $("input[name=" + k + "]");
					if (!elem.length) elem = $("textarea[name=" + k + "]");
					if (elem.val() == "") {
						elem.val(v);
						elem.css("backgroundColor", "lightblue");
						fieldChanged(k);
					}
				});
				ui.item.value = ui.item.locality;
			},
		});
		if ($("input[name=localautodeactivated]").is(":checked")) {
			$("#fflocality").autocomplete("option", "disabled", true);
			$("#fflocality").attr("autocomplete", "on");
		}
	}

	$("#locationid").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: "rpc/getlocality.php",
				data: { locationid: request.term },
				success: function (data) {
					response(data);
				},
			});
		},
		minLength: 3,
		select: function (event, ui) {
			event.preventDefault();
			$.each(ui.item, function (k, v) {
				var elem = $("input[name=" + k + "]");
				if (!elem.length) elem = $("textarea[name=" + k + "]");
				if (elem.val() == "") {
					elem.val(v);
					elem.css("backgroundColor", "lightblue");
					fieldChanged(k);
				}
			});
			let baseValue = ui.item.value;
			baseValue = baseValue.substring(0, baseValue.indexOf(" || "));
			this.value = baseValue;
		},
	});

	window.initLocalitySuggest({
		country: {
			id: "ffcountry",
			change: () => fieldChanged("country"),
		},
		state_province: {
			id: "ffstate",
			change: () => fieldChanged("stateprovince"),
		},
		county: {
			id: "ffcounty",
			change: () => fieldChanged("county"),
		},
		municipality: {
			id: "ffmunicipality",
			change: () => fieldChanged("municipality"),
		},
	});

	$("input[name=country]").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/getGeography.php", { term: request.term }, response );
		},
		minLength: 1,
		autoFocus: true,
		change: function(event, ui){
			if(typeof fieldChanged === "function") fieldChanged("country");
		}
	});

	$("input[name=stateprovince]").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/getGeography.php", { term: request.term, target: "state", parentTerm: $('input[name="country"]').val() }, response );
		},
		minLength: 1,
		autoFocus: true,
		change: function(event, ui){
			if(typeof fieldChanged === "function") fieldChanged("stateprovince");
		}
	});

	$("input[name=county]").autocomplete({ 
		source: function( request, response ) {
			$.getJSON( "rpc/getGeography.php", { term: request.term, target: "county", parentTerm: $('input[name="stateprovince"]').val() }, response );
		},
		minLength: 1,
		autoFocus: true,
		change: function(event, ui){
			if(typeof fieldChanged === "function") fieldChanged("county");
		}
	});

	$("input[name=municipality]").autocomplete({ 
		source: function( request, response ) {
			$.getJSON( "rpc/getGeography.php", { term: request.term, target: "municipality", parentTerm: $('input[name="stateprovince"]').val() }, response );
		},
		minLength: 1,
		autoFocus: true,
		change: function(event, ui){
			if(typeof fieldChanged === "function") fieldChanged("municipality");
		}
	});

	//Misc autocomplete functions 
	$("#catalognumber").keydown(function (evt) {
		var evt = evt ? evt : event ? event : null;
		if (evt.keyCode == 13) return false;
	});

	$("#exstitleinput").autocomplete({
		source: "rpc/exsiccatisuggest.php",
		minLength: 2,
		autoFocus: true,
		select: function (event, ui) {
			if (ui.item) {
				$("#ometidinput").val(ui.item.id);
				fieldChanged("ometid");
			} else {
				$("#ometidinput").val("");
				fieldChanged("ometid");
			}
		},
		change: function (event, ui) {
			if ($(this).val() == "") {
				$("#ometidinput").val("");
			} else {
				if ($("#ometidinput").val() == "") {
					$.ajax({
						type: "POST",
						url: "rpc/exsiccativalidation.php",
						data: { term: $(this).val() },
					}).done(function (msg) {
						if (msg == "") {
							alert("Exsiccati title not found within system");
						} else {
							$("#ometidinput").val(msg);
							fieldChanged("ometid");
						}
					});
				}
			}
		},
	});


	// Running as a function so that it can be activated as new rows are added
	function autocompleteTagNames() {
		$(".idNameInput").autocomplete({
			minLength: 0,
			autoFocus: true,
			source: function( request, response ) {
				let collId = document.fullform.collid.value;
				$.ajax({
					type: "POST",
					url: "rpc/tagnamesuggest.php",
					data: {collid: document.fullform.collid.value, term: request.term},
					success: function( data ){
						response(data);
					}
				});
			},
			select: function(event, ui) {
				fieldChanged('idname');
			}
		}).focus(function() {
			// If the user clicks the tag name box and it's empty, provide possible values
			if ($(this).val() === '') $(this).autocomplete("search", $(this).val());
		});
	}

	if (document.getElementById("hostDiv")) {
		$("#quickhost").autocomplete({
			source: function (request, response) {
				var name = request.term.replace(" ", "+");
				$.getJSON("rpc/getcolspeciessuggest.php", { term: name }, response);
			},
			minLength: 4,
			autoFocus: true,
			change: function (event, ui) {
				fieldChanged("host");
			},
		});
	}

});

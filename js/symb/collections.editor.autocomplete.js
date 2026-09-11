$(document).ready(function () {

	//Add for all determination form objects, including central editor form (fullform)
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
						if(f.cultivationstatus && f.recordsecurity){
							if (result.item.securityStatus == 1 && !f.cultivationstatus.checked) {
								f.recordsecurity.value = 1;
								securityChanged(f);
							}
						}
					}
					else {
						f.tidinterpreted.value = "";
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						if (f.securityreason && f.securityreason.value == ""){
							f.recordsecurity.value = 0;
						}
						let msg = "";
						if(typeof translations !== 'undefined') msg = translations.WARNING_TAXON_NOT_FOUND;
						alert(msg);
					}
					if (typeof fieldChanged === "function") {
						fieldChanged("sciname");
						fieldChanged("tidinterpreted");
						fieldChanged("scientificnameauthorship");
						fieldChanged("family");
						fieldChanged("recordsecurity");
					}
				});
			});
		}
	});

	const assocTaxaInput = document.querySelector('textarea[name=associatedtaxa]');
	if (assocTaxaInput) {
		assocTaxaInput.addEventListener("focus", function(event) {
			const f = event.currentTarget.form;
			taxaSuggest.config.clientRoot = CLIENT_ROOT;
			taxaSuggest.config.multipleTermSupport = true;
			taxaSuggest.initiate(assocTaxaInput);
		});		
	}

	// Event/locality autocomplete functions
	if (typeof localityAutoLookup !== 'undefined' && localityAutoLookup) {
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

	const locationIdInput = document.querySelector('#locationid');
	if(locationIdInput){
		$(locationIdInput).autocomplete({
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
	}

	if (typeof initLocalitySuggest === "function") {
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
	}
	else{
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
	}

	//Misc autocomplete functions 
	const exsiccateInput = document.querySelector('#exstitleinput');
	if(exsiccateInput){
		$(exsiccateInput).autocomplete({
			source: "rpc/exsiccatisuggest.php",
			minLength: 2,
			autoFocus: true,
			select: function (event, ui) {
				if (ui.item) {
					$("#ometidinput").val(ui.item.id);
					if (typeof fieldChanged === "function") fieldChanged("ometid");
				} else {
					$("#ometidinput").val("");
					if (typeof fieldChanged === "function") fieldChanged("ometid");
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
								if (typeof fieldChanged === "function") fieldChanged("ometid");
							}
						});
					}
				}
			},
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

});


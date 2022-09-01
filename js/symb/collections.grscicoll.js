// Code to get institution data from the GBIF GrSciColl API and import it into Symbiota
// In addition to filling out all the Symbiota institution fields, it adds additional info into the notes section:
// incorporated collections, taxonomic focus, geography, links etc. 

// Function to do some pre-checking before querying the API and bringing the data to Symbiota
function grscicoll(form) {

	// Get the form name to add data to
	var form = document.getElementById(form);

	// Get the institution code to look up
	var code = form.elements["institutioncode"].value;

	// Check if the code is filled in, and exit if not
	if(!code) {
		$('#getresult').hide();
		$('#dialogmsg').text('Fill in a code first to get data from GrSciColl');
		$("#dialog").show();
		$("#dialog").dialog({
			width: 'auto', 
			title: 'No institution code provided.',
			buttons: [{
				text: "Ok",
				click: function() {

					// Close the dialog
					$(this).dialog( "close" );
        
				}
			}]
		});

		// Quit
		return;
	}

	// If data already exists (editing an institution), warn the user before continuing
	if(form.name == "insteditform") {
		$('#getresult').hide();
		$('#dialogmsg').text('This will overwrite the existing data. Ok to proceed?');
		$("#dialog").show();
		$("#dialog").dialog({
			width: 'auto', 
			title: 'Warning:',
			buttons: [{
				text: "Ok",
				click: function() {

					// Close the dialog
					$(this).dialog( "close" );

					// Ok to overwrite, get the data
					getData(form, code);
				}
			}, 
			{
				text: "Cancel",
				click: function() {

					// Close the dialog
					$(this).dialog( "close" );

					// Quit
					return;
				}
			}]
		});

	} else {

		// Adding a new institution, so just get the data
		getData(form, code);
	}
}

// Function to query the API and return a collection dataset
function getData(form, code){

	// GrSciColl API Url	
	var GrSciCollURL = 'https://api.gbif.org/v1/grscicoll/collection';

	// Query the GrSciColl API with the code
	$.ajax({
		data: {code: code},
		url: GrSciCollURL,
		dataType: "json",

		// Function to run on a successful API call
		success: function (results, status, xhr) {

			// Show full API result(s) for debugging
			//console.log(results);

			// Check if no hits are found
			if(results.count == 0) {
				alert("No institution found for code: " + code);
				return;
			}

			// Check if more than one result is found. If so, ask the user to pick one
			if(results.count > 1) {

				// Show the result choices
				results.results.forEach(function (result, index) {

					// Get the source of the data
					if (result.masterSource == "GRSCICOLL"){
						source = "GrSciColl";
					} else if(result.masterSourceMetadata.source) {
						if (result.masterSourceMetadata.source == "IH_IRN") {
							source = "Index Herbariorum";
						} else {
							source = result.masterSourceMetadata.source;
						}
					} else {
						source = result.masterSource;
					}
					// Construct a <select> list for the dialog
					$('#getresult').append($('<option>', {
					    value: index,
					    text: result.name + " | " + result.institutionName + " | Source: " + source
					}));
				});
				
				// Make a jQuery UI dialog for the user to pick
				$('#dialogmsg').text('');
				$('#getresult').show();
				$("#dialog").show();
				$("#dialog").dialog({
					width: 'auto', 
					title: 'Multiple results found for code: ' + code + '. Which would you like to use?',
					buttons: [{
						text: "Save",
						click: function() {

							// Close the diagog
							$( this ).dialog( "close" );

							// Populate institution with the result the user picked
							grscicollPopulate(form, code, results.results[$( "#getresult" ).val()]);

							// Remove the result choices
							$('#getresult').children().remove().end()
						}
					}, 
					{
						text: "Cancel",
						click: function() {

							// Return without doing anything
							$( this ).dialog( "close" );

							// Remove the result choices
							$('#getresult').children().remove().end()

							// Quit
							return;
						}
					}]
				});
			} else {

				// Only one result, so use that
				grscicollPopulate(form, code, results.results[0]);
			}
		}
	});
}

// Function to populate the data in Symbiota from a GrSciColl dataset
function grscicollPopulate (form, code, result) {

	// Show full API result for debugging
	//console.log(result);

	// Check if the institution is inactive, and warn if so
	if(!result.active && !confirm("Warning: " + result.institutionName + " " + 
		result.name + " (" + code + ") is marked as inactive. Ok to proceed?")) return;

	// Reset the form
	form.reset();

	// Set Institution Code
	form.elements["institutioncode"].value = code;

	// Check if it's an Index Herbariorum entry, and get the IRN code from machineTags array if so
	if(result.indexHerbariorumRecord) {
		result.irn = result.masterSourceMetadata.sourceId;
	}

	// Set Institution Name
	if(result.institutionName || result.name) form.elements["institutionname"].value = 
		result.institutionName + " " + result.name;

	// Set Institution Name2:
	// If both division and department are present, concatenate the two with a comma
	if(result.division && result.department) {
		form.elements["institutionname2"].value = result.division + ", " + result.department;

	// Otherwise, if only one is present, just use that as institutionname2
	} else if(result.division) {
		form.elements["institutionname2"].value = result.division;
	} else if(result.department) {
		form.elements["institutionname2"].value = result.department;
	}

	// Set Mailing Address
	if(result.mailingAddress.address){

		// Check for multi-line addresses, and split if possible
		// split by semicolon first (least likely to be spurious)
		if(result.mailingAddress.address.includes(";")){

			// Split the street address with semicolons. 
			var addrArr = result.mailingAddress.address.split(";");

			// Set Address 1 to the first part of the address before a semicolon
			form.elements["address1"].value = addrArr[0];

			// Set Address 2 to everything else:
			addrArr.shift();
			form.elements["address2"].value = addrArr.join(", ").trim();

		// Otherwise, try splitting by commas
		} else if(result.mailingAddress.address.includes(",")){

			// Split the street address with commas. 
			var addrArr = result.mailingAddress.address.split(",");

			// Set Address 1 to the first part of the address before a comma
			form.elements["address1"].value = addrArr[0];

			// Set Address 2 to everything else:
			addrArr.shift();
			form.elements["address2"].value = addrArr.join(", ").trim();

		} else {

			// Set Address:
			if(result.mailingAddress.address) form.elements["address1"].value = result.mailingAddress.address;
		}
	}

	// Set City:
	if(result.mailingAddress.city) form.elements["city"].value = result.mailingAddress.city;

	// Set State/Province to its abbreviation, if supported
	if(result.mailingAddress.province) form.elements["stateprovince"].value = abbrState(result.mailingAddress.province);

	// Set Postal Code:
	if(result.mailingAddress.postalCode) form.elements["postalcode"].value = result.mailingAddress.postalCode;

	// Set Country:
	if(result.mailingAddress.country) form.elements["country"].value = result.mailingAddress.country;
	
	// Set Phone, separating multiple phone numbers by commas
	if(result.phone) form.elements["phone"].value = result.phone.join(", ");

	// Compile the contact persons into a list
	if(result.contactPersons) {
		var contacts = "";
		result.contactPersons.forEach(element => {
			// Account for possibility of missing fields
			if(element.firstName) contacts += element.firstName;
			if(element.firstName && element.lastName) contacts += ' '; 
			if(element.lastName) contacts += element.lastName;
			if(element.position){
				contacts += " (" + element.position +"), ";
			} else {
				contacts += ", ";
			}
	 	});

		// Remove trailing comma from the last contact
		contacts = contacts.substring(0, contacts.length - 2)

		// Set Contact
		if(contacts) form.elements["contact"].value = contacts;
	}

	// Set Email, preferring the email field, falling back to the first contactPerson
	if(result.email) {
		if(result.email.length > 0) {
			form.elements["email"].value = result.email.join(", ");
		} else if (result.contactPersons[0].email){
			form.elements["email"].value = result.contactPersons[0].email.join(", ");
		}
	}
	
	// Set URL:
	if(result.homepage) form.elements["url"].value = result.homepage;

	// Set Notes (this includes a number of fields)
	var notes = "";
	if(result.notes) notes += result.notes;

	// Add taxonomic coverage to notes, if included
	if(result.taxonomicCoverage) notes += "<br/><br/><strong>Taxonomic Coverage:</strong> " + result.taxonomicCoverage;

	// Add geography to notes, if included
	if(result.geography) notes += "<br/><br/><strong>Geography:</strong> " + result.geography;

	// Add incorporated herbaria to notes, if included
	if(result.incorporatedCollections && result.incorporatedCollections.length > 0) notes += "<br/><br/><strong>Incorporated Collections:</strong> " + result.incorporatedCollections.join("; ");

	// Add specimen total, if included
	if(result.numberSpecimens) notes += "<br/><br/><strong>Total Specimens:</strong> " + result.numberSpecimens;

	// Add a link to GrSciColl to the notes
	notes += "<br/><br/><strong><a href=https://www.gbif.org/grscicoll/collection/" + result.key + " target=_blank>GrSciColl Link</a></strong>";

	// Add a link to Index Herbariorum to the notes
	if(result.irn) notes += "<br/><br/><strong><a href=http://sweetgum.nybg.org/science/ih/herbarium-details/?irn=" + result.irn + " target=_blank>Index Herbariorum Link</a></strong>";

	// iDigBio Link?

	// Last Modified note?

	// Add notes field
	form.elements["notes"].value = notes;

	// Finiosh
	return;
}

// Function to convert a full state/province name to an abbreviation
// https://gist.github.com/calebgrove/c285a9510948b633aa47
function abbrState(state){

    // United States
    var states = [
        ['Alabama', 'AL'],
        ['Alaska', 'AK'],
        ['American Samoa', 'AS'],
        ['Arizona', 'AZ'],
        ['Arkansas', 'AR'],
        ['Armed Forces Americas', 'AA'],
        ['Armed Forces Europe', 'AE'],
        ['Armed Forces Pacific', 'AP'],
        ['California', 'CA'],
        ['Colorado', 'CO'],
        ['Connecticut', 'CT'],
        ['Delaware', 'DE'],
        ['District Of Columbia', 'DC'],
        ['Florida', 'FL'],
        ['Georgia', 'GA'],
        ['Guam', 'GU'],
        ['Hawaii', 'HI'],
        ['Idaho', 'ID'],
        ['Illinois', 'IL'],
        ['Indiana', 'IN'],
        ['Iowa', 'IA'],
        ['Kansas', 'KS'],
        ['Kentucky', 'KY'],
        ['Louisiana', 'LA'],
        ['Maine', 'ME'],
        ['Marshall Islands', 'MH'],
        ['Maryland', 'MD'],
        ['Massachusetts', 'MA'],
        ['Michigan', 'MI'],
        ['Minnesota', 'MN'],
        ['Mississippi', 'MS'],
        ['Missouri', 'MO'],
        ['Montana', 'MT'],
        ['Nebraska', 'NE'],
        ['Nevada', 'NV'],
        ['New Hampshire', 'NH'],
        ['New Jersey', 'NJ'],
        ['New Mexico', 'NM'],
        ['New York', 'NY'],
        ['North Carolina', 'NC'],
        ['North Dakota', 'ND'],
        ['Northern Mariana Islands', 'NP'],
        ['Ohio', 'OH'],
        ['Oklahoma', 'OK'],
        ['Oregon', 'OR'],
        ['Pennsylvania', 'PA'],
        ['Puerto Rico', 'PR'],
        ['Rhode Island', 'RI'],
        ['South Carolina', 'SC'],
        ['South Dakota', 'SD'],
        ['Tennessee', 'TN'],
        ['Texas', 'TX'],
        ['US Virgin Islands', 'VI'],
        ['Utah', 'UT'],
        ['Vermont', 'VT'],
        ['Virginia', 'VA'],
        ['Washington', 'WA'],
        ['West Virginia', 'WV'],
        ['Wisconsin', 'WI'],
        ['Wyoming', 'WY'],
    ];

    // Canada
    var provinces = [
        ['Alberta', 'AB'],
        ['British Columbia', 'BC'],
        ['Manitoba', 'MB'],
        ['New Brunswick', 'NB'],
        ['Newfoundland', 'NF'],
        ['Northwest Territory', 'NT'],
        ['Nova Scotia', 'NS'],
        ['Nunavut', 'NU'],
        ['Ontario', 'ON'],
        ['Prince Edward Island', 'PE'],
        ['Quebec', 'QC'],
        ['Saskatchewan', 'SK'],
        ['Yukon', 'YT'],
    ];

    // Combine states and provinces
    var regions = states.concat(provinces);

    // Check for a case insensitive match in states and provinces
	const selectedState = regions.find(s =>
		s.find(x => x.toLowerCase() === state.toLowerCase())
	)

	// Return the unabbreviated name if no match is found
	if (!selectedState) return state;

	// Return the abbreviation for the match
	return selectedState
		//.filter(s => s.toLowerCase() !== state.toLowerCase())
		.filter(s => s.length == 2)
		.join("");
}
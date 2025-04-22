function verifyCoordinates(f, client_root) {
	//Used within occurrenceeditor.php and observationsubmit.php
	//Input form contains input elements: country, stateProvince, county, decimalLatitude, and decimalLongitude
	//Function tiggered whenever decimalLatitude or decimalLongitud fields are modified
	//Uses GBIF spacial boundary tool to check if coordinates fall within country/state/county boundaries
	//Following code needs to be modified to use intenal shape spatial indexes associated with the geographic thesaurus 

	var lngValue = f.decimallongitude.value;
	var latValue = f.decimallatitude.value;

	if(latValue && lngValue){
		$.ajax({
			type: "GET",
			url: `${window.location.origin + client_root}/collections/editor/rpc/geocode.php`,
			dataType: "json",
			data: { lat: latValue, lng: lngValue, country: f.country.value, stateprovince: f.stateprovince.value, county: f.county.value, municipality: f.municipality.value}
		}).done(function( data ) {
			if(data.matches){
				let coord_valid = true;
				const geocode_form_map = {
					50: 'country',
					60: 'stateprovince',
					70: 'county',
					80: 'municipality',
				}

				function getAccepted(match) {
					let returnArr = [];

					if(match && typeof match.synonyms === 'string') {
						returnArr = match.synonyms.split(',').map(v => v.trim());
					}

					returnArr.push(match.geoterm);

					if(match.geoLevel === 70) {
						returnArr = [...returnArr, `${match.geoterm} County` ]
					} 

					return returnArr.map(v => v.toLowerCase().trim());
				}

				for(let match of data.matches) {
						if(geocode_form_map[match.geoLevel]) {
							const form_name = geocode_form_map[match.geoLevel];

							if(f[form_name].value === "") {
								f[form_name].value = match.geoterm;
								f[form_name].style.backgroundColor = "lightblue";
							} else if(!getAccepted(match).includes(f[form_name].value.toLowerCase())) {
								coord_valid = false;
							}
						}
				}
				if(!coord_valid) {
					alert("Are the coordinates accurate? They currently map to: " + data.matches.map(d => d.geoterm).join(', ') + " which differs from what is in the form. Click globe symbol to display coordinates in map.");
				} else if(data.matches && data.matches.length === 0 && data.is_registered) {
					alert("Are the coordinates accurate? They are not within the entered locality. Click globe symbol to display coordinates in map.");

				}
			}
		});
	}
}

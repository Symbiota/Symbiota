function verifyCoordinates(f){
	//Used within occurrenceeditor.php and observationsubmit.php
	//Check to see if coordinates are within country/state/county boundaries
	var lngValue = f.decimallongitude.value;
	var latValue = f.decimallatitude.value;
	if(latValue && lngValue){
		$.ajax({
			type: "GET",
			url: "//api.gbif.org/v1/geocode/reverse",
			dataType: "json",
			data: { lat: latValue, lng: lngValue }
		}).done(function( data ) {
			if(data){
				let geoObj = parseGbifGeocode(data);
				if(!geoObj.level0){
					if(geoObj.ocean) alert("Unable to identify country! Appears to be in "+geoObj.ocean+" Click globe symbol to display coordinates in map.");
					else alert("Unable to identify country! Are coordinates accurate? Click globe symbol to display coordinates in map.");
				}
				else{
					let level0 = "";
					let level1 = "";
					let level2 = "";
					if(geoObj.level0) level0 = geoObj.level0.name; 
					if(geoObj.level1) level1 = geoObj.level1.name; 
					if(geoObj.level2) level2 = geoObj.level2.name; 
					let coordValid = true;
					if(f.country.value != ""){
						if(geoObj.level0.iso && geoObj.level0.iso != 'US'){
							if(f.country.value.toLowerCase().indexOf(level0.toLowerCase()) == -1) coordValid = false;
						}
					}
					else{
						f.country.value = level0;
						f.country.style.backgroundColor = "lightblue";
					}
					if(level1 != ""){
						if(f.stateprovince.value != ""){
							let stateForm = f.stateprovince.value.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
							let stateIn = level1.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
							if(stateForm.toLowerCase().indexOf(stateIn.toLowerCase()) == -1) coordValid = false;
						}
						else{
							f.stateprovince.value = level1;
							f.stateprovince.style.backgroundColor = "lightblue";
						}
					}
					if(level2 != ""){
						let countyStr = level2.replace(" County","");
						countyStr = countyStr.replace(" Parish","");
						if(f.county.value != ""){
							if(f.county.value.toLowerCase().indexOf(countyStr.toLowerCase()) == -1){
								if(f.county.value.toLowerCase() != countyStr.toLowerCase()){
									coordValid = false;
								}
							}
						}
						else{
							f.county.value = level2;
							f.county.style.backgroundColor = "lightblue";
						}
					}
					if(!coordValid){
						let msg = "Are coordinates accurate? They currently map to: "+level0+", "+level1;
						if(level2) msg = msg + ", " + level2;
						msg = msg + ", which differs from what is in the form. Click globe symbol to display coordinates in map.";
						alert(msg);
					}
				}
			}
		});
	}
}

function parseGbifGeocode(data){
	let geoObj = {};
	for(var i = 0; i < data.length; i++) {
		let obj = data[i];
		if(obj.type == 'IHO') geoObj.ocean = obj.title;
		else if(obj.type == 'GADM0'){
			geoObj.level0 = {};
			geoObj.level0.name = obj.title;
			if(obj.isoCountryCode2Digit) geoObj.level0.iso = obj.isoCountryCode2Digit;
		}
		else if(obj.type == 'GADM1'){
			geoObj.level1 = {};
			geoObj.level1.name = obj.title;
			if(obj.isoCountryCode2Digit) geoObj.level1.iso = obj.isoCountryCode2Digit;
		} 
		else if(obj.type == 'GADM2'){
			geoObj.level2 = {};
			geoObj.level2.name = obj.title;
			if(obj.isoCountryCode2Digit) geoObj.level2.iso = obj.isoCountryCode2Digit;
		} 
	}
	return geoObj;
}
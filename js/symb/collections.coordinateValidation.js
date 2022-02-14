function verifyCoordinates(f){
	//Used within occurrenceeditor.php and observationsubmit.php
	//Check to see if coordinates are within country/state/county boundaries
	var lngValue = f.decimallongitude.value;
	var latValue = f.decimallatitude.value;
	if(latValue && lngValue){
		
		$.ajax({
			type: "GET",
			url: "//maps.googleapis.com/maps/api/geocode/json?sensor=false",
			dataType: "json",
			data: { latlng: latValue+","+lngValue }
		}).done(function( data ) {
			if(data){
				if(data.status != "ZERO_RESULTS"){
					var result = data.results[0];
					if(result && result.address_components){
						var compArr = result.address_components;
						var coordCountry = "";
						var coordState = "";
						var coordCounty = "";
						for (var p1 in compArr) {
							var compObj = compArr[p1];
							if(compObj.long_name && compObj.types){
								var longName = compObj.long_name;
								var types = compObj.types;
								if(types[0] == "country"){
									var coordCountry = longName;
								}
								else if(types[0] == "administrative_area_level_1"){
									var coordState = longName;
								}
								else if(types[0] == "administrative_area_level_2"){
									var coordCounty = longName;
								}
							}
						}
						var coordValid = true;
						if(f.country.value != ""){
							//if(f.country.value.toLowerCase().indexOf(coordCountry.toLowerCase()) == -1) coordValid = false;
						}
						else if(coordCountry != ""){
							f.country.value = coordCountry;
						}
						if(coordState != ""){
							if(f.stateprovince.value != ""){
								if(f.stateprovince.value.toLowerCase().indexOf(coordState.toLowerCase()) == -1) coordValid = false;
							}
							else{
								f.stateprovince.value = coordState;
							}
						}
						if(coordCounty != ""){
							var coordCountyIn = coordCounty.replace(" County","");
							coordCountyIn = coordCountyIn.replace(" Parish","");
							if(f.county.value != ""){
								var fCounty = f.county.value;
								if(f.county.value.toLowerCase().indexOf(coordCountyIn.toLowerCase()) == -1){
									if(f.county.value.toLowerCase() != coordCountyIn.toLowerCase()){
										coordValid = false;
									}
								}
							}
							else{
								f.county.value = coordCountyIn;
							}
						}
						if(!coordValid){
							var msg = "Are coordinates accurate? They currently map to: "+coordCountry+", "+coordState;
							if(coordCounty) msg = msg + ", " + coordCounty;
							msg = msg + ", which differs from what is in the form. Click globe symbol to display coordinates in map.";
							alert(msg);
						}
					}
				}
				else{
					if(f.country.value != ""){
						alert("Unable to identify country! Are coordinates accurate? Click globe symbol to display coordinates in map.");
					}
				}
			}
		});
	}
}
var polyType = 'POLYGON';
var switchCoord = false;
var trimCoord = false;

function validatePolygon(footprintWkt){
	footprintWkt = footprintWkt.trim();
	footprintWkt = footprintWkt.replace(/\s\s+/g, ' ');
	if(footprintWkt == "" || footprintWkt == "undefined") return "";

	if(footprintWkt.substring(0,2) == "[{"){
		//Translate old json format to polygon wkt string
		try{
			let footPolyArr = JSON.parse(footprintWkt);
			let newStr = '';
			for(i in footPolyArr){
				let keys = Object.keys(footPolyArr[i]);
				if(!isNaN(footPolyArr[i][keys[0]]) && !isNaN(footPolyArr[i][keys[1]])){
					let lat = parseFloat(footPolyArr[i][keys[0]]);
					if(trimCoord) lat = lat.toFixed(6);
					let lng = parseFloat(footPolyArr[i][keys[1]]);
					if(trimCoord) lng = lng.toFixed(6);
					newStr = newStr + "," + lat + " " + lng;
				}
				else{
					alert("The footprint is not in the proper format. Please recreate it using the map tools.");
					break;
				}
			}
			footprintWkt = newStr.substr(1);
		}
		catch(e){
			alert("The footprint is not in the proper format. Please recreate it using the map tools.");
		}
	}

	let patt = new RegExp(/\<kml\s+/);
	if(patt.test(footprintWkt)){
		//KML coordinate format (e.g. -99.238545,47.148081 -99.238545,47.148081 ...)
		let patt = new RegExp(/[\d-\.]+,[\d-\.]+,[\d]+\s+/);
		if(patt.test(footprintWkt)){
			//Format is a KML coordinate tuple (e.g. -99.238545,47.148081,0 -99.238545,47.148081,0 ...)
			let newStr = "";
			while(footprintWkt.substring(0,1) == "("){
				footprintWkt = footprintWkt.slice(1,-1);
			}
			let klmArr = footprintWkt.split(" ");
			for(var i=0; i < klmArr.length; i++){
				let pArr = klmArr[i].split(",");
				let lat = parseFloat(pArr[1]);
				if(trimCoord) lat = lat.toFixed(6);
				let lng = parseFloat(pArr[0]);
				if(trimCoord) lng = lng.toFixed(6);
				newStr = newStr + "," + lat + " " + lng;
			}
			footprintWkt = newStr.substr(1)+"";
		}
		else{
			let coordArr = footprintWkt.match(/[\d-\.]+,[\d-\.]+/g);
			if(coordArr){
				let tempArr = [];
				for (i = 0; i < coordArr.length; i++) {
					tempArr = coordArr[i].split(",");
					let lat = parseFloat(tempArr[1]);
					if(trimCoord) lat = lat.toFixed(6);
					let lng = parseFloat(tempArr[0]);
					if(trimCoord) lng = lng.toFixed(6);
					coordArr[i] = lat + " " + lng;
				}
				footprintWkt = coordArr.join(",");
			}
		}
	}

	let polyType = "POLYGON";
	if(footprintWkt.substring(0,7) == "POLYGON"){
		footprintWkt = footprintWkt.substring(7).trim();
	}
	else if(footprintWkt.substring(0,12) == "MULTIPOLYGON"){
		footprintWkt = footprintWkt.substring(12).trim();
		polyType = "MULTIPOLYGON";
	}
	footprintWkt = trimPoly(footprintWkt);
	let returnPoly = "";
	if(footprintWkt.length > 0){
		let polyArr = footprintWkt.split("))");
		for(let m=0; m < polyArr.length; m++){
			if(polyArr.length == 1) polyType = "POLYGON";
			let polyStr = trimPoly(polyArr[m].trim());
			returnPoly = returnPoly + ",((" + formatPolyFragment(polyStr) + "))";
		}
	}
	if(returnPoly){
		if(polyType == "POLYGON") returnPoly = polyType+" "+returnPoly.substring(1);
		else if(polyType == "MULTIPOLYGON") returnPoly = polyType+" ("+returnPoly.substring(1)+")";
	}
	return returnPoly;
}

function trimPoly(polyStr){
	while(polyStr.substring(0,1) == "(" || polyStr.substring(0,1) == ","){
		polyStr = polyStr.substring(1).trim();
	}
	while(polyStr.substring(polyStr.length-1) == ")" || polyStr.substring(polyStr.length-1) == ","){
		polyStr = polyStr.slice(0,-1).trim();
	}
	return polyStr;
}

function formatPolyFragment(polyStr){
	let newStr = "";
	let patt = new RegExp(/^[\d-\.]+,[\d-\.]+/);
	if(patt.test(polyStr)){
		//Is a GeoLocate polygon type (e.g. 31.6661680128,-110.709762938,31.6669780128,-110.710163938,...)
		let coordArr = polyStr.split(",");
		for(var i=0; i < coordArr.length; i++){
			if((i % 2) == 1){
				let latDec = parseFloat(coordArr[i-1]);
				if(trimCoord) latDec = latDec.toFixed(6);
				let lngDec = parseFloat(coordArr[i]);
				if(trimCoord) lngDec = lngDec.toFixed(6);
				if(Math.abs(latDec) > 90){
					alert("One or more of the latitude values are out of range. Perhaps the coordinates need to be switched?");
					return false;
				} 
				newStr = newStr + "," + latDec + " " + lngDec;
			}
		}
	}
	else{
		if(polyStr.indexOf(')') > -1) polyStr = polyStr.substring(0,polyStr.indexOf(')'));
		let strArr = polyStr.split(",");
		let reductionFactor = Math.floor(strArr.length/3000);
		for(var i=0; i < strArr.length; i++){
			let xy = strArr[i].trim().split(" ");
			if(i<1 || strArr[i-1].trim() != strArr[i].trim()){
				if(!isNaN(xy[0]) && !isNaN(xy[1])){
					let latDec = parseFloat(xy[0]);
					if(trimCoord) latDec = latDec.toFixed(6);
					let lngDec = parseFloat(xy[1]);
					if(trimCoord) lngDec = lngDec.toFixed(6);
					if(Math.abs(latDec) > 90){
						alert("One or more of the latitude values are out of range. Perhaps the coordinates need to be switched?");
						return false;
					} 
					newStr = newStr + "," + latDec + " " + lngDec;
				}
			}
			if(reductionFactor > 0) i = i + reductionFactor;
		}
	}
	if(newStr) polyStr = newStr.substr(1);
	if(switchCoord) polyStr = switchCoordinates(polyStr);

	//Make sure first and last points are the same
	if(polyStr.indexOf(",") > -1){
		let firstSet = polyStr.substr(0,polyStr.indexOf(","));
		let lastSet = polyStr.substr(polyStr.lastIndexOf(",")+1);
		if(firstSet != lastSet) polyStr = polyStr + "," + firstSet;
	}
	return polyStr;
}

function switchCoordinates(polyStr){
	let wktStr = "";
	let wktArr = polyStr.split(",");
	for(var i=0; i < wktArr.length; i++){
		if(i>0 && wktArr[i-1].trim() == wktArr[i].trim()) continue;
		let xy = wktArr[i].trim().split(" ");
		if(!isNaN(xy[0]) && !isNaN(xy[1])) wktStr = wktStr + "," + xy[1] + " " + xy[0];
	}
	if(wktStr) retStr = wktStr.substr(1);
	return retStr;
}

function trimPolygon(footprintWkt){
	footprintWkt = footprintWkt.trim();
	if(footprintWkt != ""){
		if(footprintWkt.substring(0,10) == "POLYGON ((") footprintWkt = footprintWkt.slice(10,-2);
		if(footprintWkt.substring(0,9) == "POLYGON((") footprintWkt = footprintWkt.slice(9,-2);
	}
	return footprintWkt;
}

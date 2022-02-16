function reformCoordinates(f){
	var footprintWkt = trimPolygon(f.footprintwkt.value);
	footprintWkt = validatePolygon(footprintWkt);
	f.footprintwkt.value = footprintWkt;
}

function validatePolygon(footprintWktInput){
	if(footprintWkt == "" || footprintWkt == "undefined") return "";
	var footprintWkt = trimPolygon(footprintWktInput);
	if(footprintWkt.substring(0,2) == "[{"){
		//Translate old json format to polygon wkt string
		try{
			var footPolyArr = JSON.parse(footprintWkt);
			newStr = '';
			for(i in footPolyArr){
				var keys = Object.keys(footPolyArr[i]);
				if(!isNaN(footPolyArr[i][keys[0]]) && !isNaN(footPolyArr[i][keys[1]])){
					newStr = newStr + "," + parseFloat(footPolyArr[i][keys[0]]).toFixed(6) + " " + parseFloat(footPolyArr[i][keys[1]]).toFixed(6);
				}
				else{
					alert("The footprint is not in the proper format. Please recreate it using the map tools.");
					break;
				}
			}
			if(newStr) footprintWkt = newStr.substr(1);
		}
		catch(e){
			alert("The footprint is not in the proper format. Please recreate it using the map tools.");
		}
	}

	//Check to see if input is a KML coordinate tuple (e.g. -99.238545,47.148081,0 -99.238545,47.148081,0 ...)
	var patt = new RegExp(/^[\d-\.]+,[\d-\.]+,[\d]+\s+/);
	if(patt.test(footprintWkt)){
		var newStr = "";
		var klmArr = footprintWkt.split(" ");
		for(var i=0; i < klmArr.length; i++){
			var pArr = klmArr[i].split(",");
			newStr = newStr + "," + parseFloat(pArr[1]).toFixed(6) + " " + parseFloat(pArr[0]).toFixed(6);
		}
		footprintWkt = newStr.substr(1);
	}

	//Check to see if input is another KML coordinate format (e.g. -99.238545,47.148081 -99.238545,47.148081 ...)
	var patt = new RegExp(/\<kml\s+/);
	if(patt.test(footprintWkt)){
		var coordArr = footprintWkt.match(/[\d-\.]+,[\d-\.]+/g);
		if(coordArr){
			var tempArr = [];
			for (i = 0; i < coordArr.length; i++) {
				tempArr = coordArr[i].split(",");
				coordArr[i] = tempArr[1]+" "+tempArr[0];
			}
			footprintWkt = coordArr.join(",");
		}
	}

	//Check to see if it's a GeoLocate polygon (e.g. 31.6661680128,-110.709762938,31.6669780128,-110.710163938,...)
	var patt = new RegExp(/^[\d-\.]+,[\d-\.]+/);
	if(patt.test(footprintWkt)){
		var newStr = "";
		var coordArr = footprintWkt.split(",");
		for(var i=0; i < coordArr.length; i++){
			if((i % 2) == 1){
				newStr = newStr + "," + parseFloat(coordArr[i-1]).toFixed(6) + " " + parseFloat(coordArr[i]).toFixed(6);
			}
		}
		footprintWkt = newStr.substr(1);
	}
	
	//Check point order
	footprintWkt = validatePoints(footprintWkt, false);

	//Make sure first and last points are the same
	if(footprintWkt.indexOf(",") > -1){
		var firstSet = footprintWkt.substr(0,footprintWkt.indexOf(","));
		var lastSet = footprintWkt.substr(footprintWkt.lastIndexOf(",")+1);
		if(firstSet != lastSet) footprintWkt = footprintWkt + "," + firstSet;
	}
	footprintWkt = footprintWkt.trim();
	if(footprintWkt != "") footprintWkt = "POLYGON (("+footprintWkt+"))";
	return footprintWkt;
}

function validatePoints(footprintWkt, switchPoints){
	if(footprintWkt.substring(0,2) == "[{") return footprintWkt;
	var retStr = "";
	var strArr = footprintWkt.split(",");
	if(!switchPoints){
		if(parseInt(Math.abs(strArr[0].substring(0,strArr[0].indexOf(" ")).trim())) > 90) switchPoints = true;
	}
	if(switchPoints) alert("switch points");

	for(var i=0; i < strArr.length; i++){
		var xy = strArr[i].trim().split(" ");
		if(i<1 || strArr[i-1].trim() != strArr[i].trim()){
			if(!isNaN(xy[0]) && !isNaN(xy[1])){
				if(switchPoints){
					retStr = retStr + "," + parseFloat(xy[1]).toFixed(6) + " " + parseFloat(xy[0]).toFixed(6);
				}
				else{
					retStr = retStr + "," + parseFloat(xy[0]).toFixed(6) + " " + parseFloat(xy[1]).toFixed(6);
				}
			}
		}
	}
	if(retStr.substr(0,1) == ",") retStr = retStr.substr(1);
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
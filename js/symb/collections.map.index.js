$(document).ready(function() {	
	setHeight()
	
	$('#tabs1').tabs({
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		},
		active: 1
   });

   var hijax = function(panel) {
      $('.pagination a', panel).click(function(){
         $(panel).load(this.href, {}, function() {
            hijax(this);
         });
         return false;
      });
   };

	$('#tabs2').tabs({
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		},
        load: function(event, ui) {
            hijax(ui.panel);
        }
	});

	$('#tabs3').tabs({
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});

	$("#accordion").accordion({
		collapsible: true,
		heightStyle: "fill"
	});

	$('#loadingOverlay').popup({
		transition: 'all 0.3s',
		scrolllock: true,
		opacity:0.5,
		color:'white'
	});
	
});

$(window).resize(function(){
	setHeight();
	$("#accordion").accordion("refresh");
});

$(document).on("pageloadfailed", function(event, data){
    event.preventDefault();
});

function setHeight(){
   var winHeight = window.innerHeight;
	document.getElementById('mapinterface').style.height = winHeight + "px";
	document.getElementById('loadingOverlay').style.height = winHeight + "px";
}

function checkUpperLat(){
	if(document.mapsearchform.upperlat.value != ""){
		if(document.mapsearchform.upperlat_NS.value=='N'){
			document.mapsearchform.upperlat.value = Math.abs(parseFloat(document.mapsearchform.upperlat.value));
		}
		else{
			document.mapsearchform.upperlat.value = -1*Math.abs(parseFloat(document.mapsearchform.upperlat.value));
		}
	}
}
		
function checkBottomLat(){
	if(document.mapsearchform.bottomlat.value != ""){
		if(document.mapsearchform.bottomlat_NS.value == 'N'){
			document.mapsearchform.bottomlat.value = Math.abs(parseFloat(document.mapsearchform.bottomlat.value));
		}
		else{
			document.mapsearchform.bottomlat.value = -1*Math.abs(parseFloat(document.mapsearchform.bottomlat.value));
		}
	}
}

function checkRightLong(){
	if(document.mapsearchform.rightlong.value != ""){
		if(document.mapsearchform.rightlong_EW.value=='E'){
			document.mapsearchform.rightlong.value = Math.abs(parseFloat(document.mapsearchform.rightlong.value));
		}
		else{
			document.mapsearchform.rightlong.value = -1*Math.abs(parseFloat(document.mapsearchform.rightlong.value));
		}
	}
}

function checkLeftLong(){
	if(document.mapsearchform.leftlong.value != ""){
		if(document.mapsearchform.leftlong_EW.value=='E'){
			document.mapsearchform.leftlong.value = Math.abs(parseFloat(document.mapsearchform.leftlong.value));
		}
		else{
			document.mapsearchform.leftlong.value = -1*Math.abs(parseFloat(document.mapsearchform.leftlong.value));
		}
	}
}

function checkPointLat(){
	if(document.mapsearchform.pointlat.value != ""){
		if(document.mapsearchform.pointlat_NS.value=='N'){
			document.mapsearchform.pointlat.value = Math.abs(parseFloat(document.mapsearchform.pointlat.value));
		}
		else{
			document.mapsearchform.pointlat.value = -1*Math.abs(parseFloat(document.mapsearchform.pointlat.value));
		}
	}
}

function checkPointLong(){
	if(document.mapsearchform.pointlong.value != ""){
		if(document.mapsearchform.pointlong_EW.value=='E'){
			document.mapsearchform.pointlong.value = Math.abs(parseFloat(document.mapsearchform.pointlong.value));
		}
		else{
			document.mapsearchform.pointlong.value = -1*Math.abs(parseFloat(document.mapsearchform.pointlong.value));
		}
	}
}

function reSymbolizeMap(type) {
	var searchForm = document.getElementById("mapsearchform");
	if (type == 'taxa') {
		document.getElementById("mapsymbology").value = 'taxa';
	}
	if (type == 'coll') {
		document.getElementById("mapsymbology").value = 'coll';
	}
	searchForm.submit();
}

function checkRecordLimit(f) {
	var recordLimit = document.getElementById("recordlimit").value;
	if(!isNaN(recordLimit) && recordLimit > 0){
		if (recordLimit > 50000) {
			alert("Record limit cannot exceed 50000.");
			document.getElementById("recordlimit").value = 5000;
			return;
		}
		if (recordLimit <= 50000) {
			if(recordLimit > 5000){
				if(confirm('Increasing the record limit can cause delays in loading the map, or for your browser to crash.')){
					return true;
				}
				else{
					document.getElementById("recordlimit").value = 5000;
				}
			}
		}
	}
	else{
		document.getElementById("recordlimit").value = 5000;
		alert("Record Limit must be set to a whole number greater than zero.");
	}
}

function updateRadius(){
	var radiusUnits = document.getElementById("radiusunits").value;
	var radiusInMiles = document.getElementById("radiustemp").value;
	if(radiusUnits == "km"){
		radiusInMiles = radiusInMiles*0.6214; 
	}
	document.getElementById("radius").value = radiusInMiles;
}

function clearSelection() {
	if (selectedShape) {
		selectedShape.setEditable(false);
		selectedShape = null;
	}
	document.getElementById("pointlat").value = '';
	document.getElementById("pointlong").value = '';
	document.getElementById("radius").value = '';
	document.getElementById("upperlat").value = '';
	document.getElementById("leftlong").value = '';
	document.getElementById("bottomlat").value = '';
	document.getElementById("rightlong").value = '';
	document.getElementById("poly_array").value = '';
	document.getElementById("distFromMe").value = '';
	document.getElementById("noshapecriteria").style.display = "block";
	document.getElementById("polygeocriteria").style.display = "none";
	document.getElementById("circlegeocriteria").style.display = "none";
	document.getElementById("rectgeocriteria").style.display = "none";
	document.getElementById("deleteshapediv").style.display = "none";
}

function setSelection(shape) {
	clearSelection();
	var selectedShapeType = shape.type;
	selectedShape = shape;
	selectedShape.setEditable(true);
	if (selectedShapeType == 'circle') {
		getCircleCoords(shape);
	}
	if (selectedShapeType == 'rectangle') {
		getRectangleCoords(shape);
	}
	if (selectedShapeType == 'polygon') {
		getPolygonCoords(shape);
	}
}

function deleteSelectedShape() {
	if (selectedShape){
		selectedShape.setMap(null);
		clearSelection();
	}
}

function getCircleCoords(circle) {
	var rad = (circle.getRadius());
	var radius = (rad/1000)*0.6214;
	document.getElementById("pointlat").value = (circle.getCenter().lat());
	document.getElementById("pointlong").value = (circle.getCenter().lng());
	document.getElementById("radius").value = radius;
	document.getElementById("upperlat").value = '';
	document.getElementById("leftlong").value = '';
	document.getElementById("bottomlat").value = '';
	document.getElementById("rightlong").value = '';
	document.getElementById("poly_array").value = '';
	document.getElementById("distFromMe").value = '';
	document.getElementById("noshapecriteria").style.display = "none";
	document.getElementById("polygeocriteria").style.display = "none";
	document.getElementById("circlegeocriteria").style.display = "block";
	document.getElementById("rectgeocriteria").style.display = "none";
	document.getElementById("deleteshapediv").style.display = "block";
}
  
function getRectangleCoords(rectangle) {
	document.getElementById("upperlat").value = (rectangle.getBounds().getNorthEast().lat());
	document.getElementById("rightlong").value = (rectangle.getBounds().getNorthEast().lng());
	document.getElementById("bottomlat").value = (rectangle.getBounds().getSouthWest().lat());
	document.getElementById("leftlong").value = (rectangle.getBounds().getSouthWest().lng());
	document.getElementById("pointlat").value = '';
	document.getElementById("pointlong").value = '';
	document.getElementById("radius").value = '';
	document.getElementById("poly_array").value = '';
	document.getElementById("distFromMe").value = '';
	document.getElementById("noshapecriteria").style.display = "none";
	document.getElementById("polygeocriteria").style.display = "none";
	document.getElementById("circlegeocriteria").style.display = "none";
	document.getElementById("rectgeocriteria").style.display = "block";
	document.getElementById("deleteshapediv").style.display = "block";
}
  
function getPolygonCoords(polygon) {
	var coordinates = [];
	var coordinatesMVC = (polygon.getPath().getArray());
	for(var i=0;i<coordinatesMVC.length;i++){
		var mvcString = coordinatesMVC[i].toString();
		mvcString = mvcString.slice(1, -1);
		var latlngArr = mvcString.split(",");
		coordinates.push(parseFloat(latlngArr[0]).toFixed(6)+" "+parseFloat(latlngArr[1]).toFixed(6));
	}
	if(coordinates[0] != coordinates[i]) coordinates.push(coordinates[0]);
	document.getElementById("poly_array").value = "POLYGON(("+coordinates.toString()+"))";
	document.getElementById("pointlat").value = '';
	document.getElementById("pointlong").value = '';
	document.getElementById("radius").value = '';
	document.getElementById("upperlat").value = '';
	document.getElementById("leftlong").value = '';
	document.getElementById("bottomlat").value = '';
	document.getElementById("rightlong").value = '';
	document.getElementById("distFromMe").value = '';
	document.getElementById("noshapecriteria").style.display = "none";
	document.getElementById("polygeocriteria").style.display = "block";
	document.getElementById("circlegeocriteria").style.display = "none";
	document.getElementById("rectgeocriteria").style.display = "none";
	document.getElementById("deleteshapediv").style.display = "block";
}

function generateRandColor(){
	var hexColor = '';
	var x = Math.round(0xffffff * Math.random()).toString(16);
	var y = (6-x.length);
	var z = '000000';
	var z1 = z.substring(0,y);
	hexColor = z1 + x;
	return hexColor;
}

function changeKeyColor(v,markers){
	var newMarkerColor = '#'+v;
	if (markers) {
		for (i in markers) {
			if(markers[i].customInfo=='obs'){
				if(markers[i].selected==true){
					var markerIcon = {path:"m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z",fillColor:newMarkerColor,fillOpacity:1,scale:1,strokeColor:"#10D8E6",strokeWeight:2};
				}
				else{
					var markerIcon = {path:"m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z",fillColor:newMarkerColor,fillOpacity:1,scale:1,strokeColor:"#000000",strokeWeight:1};
				}
				markers[i].color = v;
				markers[i].setIcon(markerIcon);
			}
			if(markers[i].customInfo=='spec'){
				if(markers[i].selected==true){
					var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:newMarkerColor,fillOpacity:1,scale:7,strokeColor:"#10D8E6",strokeWeight:2};
				}
				else{
					var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:newMarkerColor,fillOpacity:1,scale:7,strokeColor:"#000000",strokeWeight:1};
				}
				markers[i].color = v;
				markers[i].setIcon(markerIcon);
			}
		}
	}
}

function selectMarker(marker){
	var markerColor = '#'+marker.color;
	if(marker.customInfo=='obs'){
		var markerIcon = {path:"m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z",fillColor:markerColor,fillOpacity:1,scale:1,strokeColor:"#10D8E6",strokeWeight:2};
		marker.setIcon(markerIcon);
	}
	if(marker.customInfo=='spec'){
		var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:markerColor,fillOpacity:1,scale:7,strokeColor:"#10D8E6",strokeWeight:2};
		marker.setIcon(markerIcon);
	}
	marker.selected = true;
}

function selectDsMarker(marker){
	var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:"#ffffff",fillOpacity:1,scale:5,strokeColor:"#10D8E6",strokeWeight:2};
	marker.setIcon(markerIcon);
	marker.selected = true;
}

function deselectMarker(marker){
	var markerColor = '#'+marker.color;
	if(marker.customInfo=='obs'){
		var markerIcon = {path:"m6.70496,0.23296l-6.70496,13.48356l13.88754,0.12255l-7.18258,-13.60611z",fillColor:markerColor,fillOpacity:1,scale:1,strokeColor:"#000000",strokeWeight:1};
		marker.setIcon(markerIcon);
	}
	if(marker.customInfo=='spec'){
		var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:markerColor,fillOpacity:1,scale:7,strokeColor:"#000000",strokeWeight:1};
		marker.setIcon(markerIcon);
	}
	marker.selected = false;
}

function deselectDsMarker(marker){
	var markerIcon = {path:google.maps.SymbolPath.CIRCLE,fillColor:"#ffffff",fillOpacity:1,scale:5,strokeColor:"#000000",strokeWeight:2};
	marker.setIcon(markerIcon);
	marker.selected = false;
}

/*
function findSelections(c){
	if(c.checked == true){
		var activeTab = $('#tabs2').tabs("option","active");
		if(activeTab==1){
			if($('.occcheck:checked').length==$('.occcheck').length){
				document.getElementById("selectallcheck").checked = true;
			}
		}
		var selectedbox = document.getElementById("selectedpoints");
		selectedbox.value = c.value;
		selectPoints();
	}
	else if(c.checked == false){
		var activeTab = $('#tabs2').tabs("option","active");
		if(activeTab==1){
			document.getElementById("selectallcheck").checked = false;
		}
		removeSelectionRecord(c.value);
		var deselectedbox = document.getElementById("deselectedpoints");
		deselectedbox.value = c.value;
		deselectPoints();
	}
}
*/

/*
function findDsSelections(c){
	if(c.checked == true){
		var activeTab = $('#tabs3').tabs("option","active");
		if(activeTab==1){
			if($('.dsocccheck:checked').length==$('.dsocccheck').length){
				document.getElementById("dsselectallcheck").checked = true;
			}
		}
		var selectedbox = document.getElementById("selecteddspoints");
		selectedbox.value = c.value;
		selectDSPoints();
	}
	else if(c.checked == false){
		var activeTab = $('#tabs3').tabs("option","active");
		if(activeTab==1){
			document.getElementById("dsselectallcheck").checked = false;
		}
		var deselectedbox = document.getElementById("deselecteddspoints");
		deselectedbox.value = c.value;
		deselectDSPoints();
	}
}
*/

function toggleLatLongDivs(){
	var divs = document.getElementsByTagName("div");
	for (i = 0; i < divs.length; i++) {
		var obj = divs[i];
		if(obj.getAttribute("class") == "latlongdiv" || obj.getAttribute("className") == "latlongdiv"){
			if(obj.style.display=="none"){
				obj.style.display="block";
			}
			else{
				obj.style.display="none";
			}
		}
	}
}

function toggle(target){
	var ele = document.getElementById(target);
	if(ele){
		if(ele.style.display=="none"){
			ele.style.display="block";
		}
		else {
			ele.style.display="none";
		}
	}
	else{
		var divObjs = document.getElementsByTagName("div");
		for (i = 0; i < divObjs.length; i++) {
			var divObj = divObjs[i];
			if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
				if(divObj.style.display=="none"){
					divObj.style.display="block";
				}
				else {
					divObj.style.display="none";
				}
			}
		}
	}
}

function toggleCat(catid){
	toggle("minus-"+catid);
	toggle("plus-"+catid);
	toggle("cat-"+catid);
}

function selectAll(cb){
	var boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	var f = cb.form;
	for(var i=0;i<f.length;i++){
		if(f.elements[i].name == "db[]" || f.elements[i].name == "cat[]" || f.elements[i].name == "occid[]"){ 
			f.elements[i].checked = boxesChecked;
		}
		if(f.elements[i].name == "occid[]"){
			f.elements[i].onchange();
		}
	}
}

function uncheckAll(f){
	document.getElementById('dballcb').checked = false;
}

function selectAllCat(cb,target){
	var boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	var inputObjs = document.getElementsByTagName("input");
	for (i = 0; i < inputObjs.length; i++) {
		var inputObj = inputObjs[i];
		if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
			inputObj.checked = boxesChecked;
		}
	}
}

function unselectCat(catTarget){
	var catObj = document.getElementById(catTarget);
	catObj.checked = false;
	uncheckAll();
}

function verifyCollForm(f){
	var formVerified = false;
	for(var h=0;h<f.length;h++){
		if(f.elements[h].name == "db[]" && f.elements[h].checked){
			formVerified = true;
			break;
		}
		if(f.elements[h].name == "cat[]" && f.elements[h].checked){
			formVerified = true;
			break;
		}
	}
	if(!formVerified){
		alert("Please choose at least one collection!");
		return false;
	}
	else{
		for(var i=0;i<f.length;i++){
			if(f.elements[i].name == "cat[]" && f.elements[i].checked){
				if(document.getElementById('cat-'+f.elements[i].value)){
					//Uncheck all db input elements within cat div 
					var childrenEle = document.getElementById('cat-'+f.elements[i].value).children;
					for(var j=0;j<childrenEle.length;j++){
						if(childrenEle[j].tagName == "DIV"){
							var divChildren = childrenEle[j].children;
							for(var k=0;k<divChildren.length;k++){
								var divChildren2 = divChildren[k].children;
								for(var l=0;l<divChildren2.length;l++){
									if(divChildren2[l].tagName == "INPUT"){
										divChildren2[l].checked = false;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	//make sure they have filled out at least one field.
	if((f.taxa.value == '') && (f.country.value == '') && (f.state.value == '') && (f.county.value == '') && 
		(f.locality.value == '') && (f.upperlat.value == '') && (f.pointlat.value == '') && 
		(f.collector.value == '') && (f.collnum.value == '') && (f.eventdate1.value == '')){
        alert("Please fill in at least one search parameter!");
        return false;
    }
 
    if(f.upperlat.value != '' || f.bottomlat.value != '' || f.leftlong.value != '' || f.rightlong.value != ''){
        // if Lat/Long field is filled in, they all should have a value!
        if(f.upperlat.value == '' || f.bottomlat.value == '' || f.leftlong.value == '' || f.rightlong.value == ''){
			alert("Error: Please make all Lat/Long bounding box values contain a value or all are empty");
			return false;
        }

		// Check to make sure lat/longs are valid.
		if(Math.abs(f.upperlat.value) > 90 || Math.abs(f.bottomlat.value) > 90 || Math.abs(f.pointlat.value) > 90){
			alert("Latitude values can not be greater than 90 or less than -90.");
			return false;
		} 
		if(Math.abs(f.leftlong.value) > 180 || Math.abs(f.rightlong.value) > 180 || Math.abs(f.pointlong.value) > 180){
			alert("Longitude values can not be greater than 180 or less than -180.");
			return false;
		} 
		if(parseFloat(f.upperlat.value) < parseFloat(f.bottomlat.value)){
			alert("Your northern latitude value is less then your southern latitude value. Please correct this.");
			return false;
		}
		if(parseFloat(f.leftlong.value) > parseFloat(f.rightlong.value)){
			alert("Your western longitude value is greater then your eastern longitude value. Please correct this. Note that western hemisphere longitudes in the decimal format are negitive.");
			return false;
		}
    }

	//Same with point radius fields
    if(f.pointlat.value != '' || f.pointlong.value != '' || f.radius.value != ''){
    	if(f.pointlat.value == '' || f.pointlong.value == '' || f.radius.value == ''){
    		alert("Error: Please make all Lat/Long point-radius values contain a value or all are empty");
			return false;
		}
	}
    return true;
}

function resetQueryForm(f){
	$('input[name=taxa]').val('');
	$('input[name=country]').val('');
	$('input[name=state]').val('');
	$('input[name=county]').val('');
	$('input[name=local]').val('');
	$('input[name=collector]').val('');
	$('input[name=collnum]').val('');
	$('input[name=eventdate1]').val('');
	$('input[name=eventdate2]').val('');
	$('input[name=catnum]').val('');
	$('input[name=othercatnum]').val('');
	$('input[name=typestatus]').attr('checked', false);
	$('input[name=hasimages]').attr('checked', false);
	$('input[name=hasgenetic]').attr('checked', false);
	$('input[name=includecult]').attr('checked', false);
	deleteSelectedShape();
}

function checkKey(e){
	var key;
	if(window.event){
		key = window.event.keyCode;
	}else{
		key = e.keyCode;
	}
	if(key == 13){
		document.collections.submit();
	}
}

function shiftKeyBox(tid){
	var currentkeys = document.getElementById("symbologykeysbox").innerHTML;
	var keyDivName = tid+"keyrow";
	var colorBoxName = "taxaColor"+tid;
	var newKeyToAdd = document.getElementById(keyDivName).innerHTML;
	document.getElementById(colorBoxName).color.hidePicker();
	document.getElementById("symbologykeysbox").innerHTML = currentkeys+newKeyToAdd;
}

function prepSelectionKml(f){
	if(f.kmltype.value=='dsselectionquery'){
		if(dsselections.length!=0){
			var jsonSelections = JSON.stringify(dsselections);
		}
		else{
			alert("Please select records from the dataset to create KML file.");
			return;
		}
	}
	else{
		var jsonSelections = JSON.stringify(selections);
	}
	f.selectionskml.value = jsonSelections;
	f.starrkml.value = starr;
	f.submit();
}

function closeAllInfoWins(){
	for( var w = 0; w < infoWins.length; w++ ) {
		var win = infoWins[w];
		win.close();
	}
}

function openOccidInfoBox(label,lat,lon){
	var myOptions = {
		content: label,
		boxStyle: {
			border: "1px solid black",
			background: "#ffffff",
			textAlign: "center",
			padding: "2px",
			fontSize: "12px"
		},
		disableAutoPan: true,
		pixelOffset: new google.maps.Size(-25, 0),
		position: new google.maps.LatLng(lat, lon),
		isHidden: false,
		closeBoxURL: "",
		pane: "floatPane",
		enableEventPropagation: false
	};

	ibLabel = new InfoBox(myOptions);
	ibLabel.open(map);
}

function closeOccidInfoBox(){
	if(ibLabel){
        ibLabel.close();
	}
}

function openIndPopup(occid,clid){
	openPopup('../individual/index.php?occid='+occid+'&clid='+clid);
}

function openPopup(urlStr){
	var wWidth = 1000;
	try{
		if(opener.document.body.offsetWidth) wWidth = opener.document.body.offsetWidth*0.95;
		if(wWidth > 1400) wWidth = 1400;
	}
	catch(err){
	}
	newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
	if (newWindow.opener == null) newWindow.opener = self;
	return false;
}

function setClustering(){
	var gridSizeSett = document.getElementById("gridsize").value;
	var minClusterSett = document.getElementById("minclustersize").value;
	if(document.getElementById("clusteroff").checked==true){
		var clstrSwitch = "y";
	}
	else{
		var clstrSwitch = "n";
	}
	document.getElementById("gridSizeSetting").value = gridSizeSett;
	document.getElementById("minClusterSetting").value = minClusterSett;
	document.getElementById("clusterSwitch").value = clstrSwitch;
}

function refreshClustering(){
	var searchForm = document.getElementById("mapsearchform");
	searchForm.submit();
}

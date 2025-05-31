var activeImgIndex = 1;
var ocrFragIndex = 1;

$(document).ready(function() {
	//Remember image popout status 
	var imgTd = getCookie("symbimgtd");
	if(imgTd != "close") toggleImageTdOn();
	//if(imgTd == "open" || csMode == 1) toggleImageTdOn();
	initImgRes();
});

function toggleImageTdOn(){
	var imgSpan = document.getElementById("imgProcOnSpan");
	if(imgSpan){
		imgSpan.style.display = "none";
		document.getElementById("imgProcOffSpan").style.display = "block";
		var imgTdObj = document.getElementById("imgtd");
		if(imgTdObj){
			document.getElementById("imgtd").style.display = "block";
			initImageTool("activeimg-1");
			//Set cookie to tag td as open
	        document.cookie = "symbimgtd=open";
		}
	}
}

function toggleImageTdOff(){
	var imgSpan = document.getElementById("imgProcOnSpan");
	if(imgSpan){
		imgSpan.style.display = "block";
		document.getElementById("imgProcOffSpan").style.display = "none";
		var imgTdObj = document.getElementById("imgtd");
		if(imgTdObj){
			document.getElementById("imgtd").style.display = "none";
			//Set cookie to tag td closed
	        document.cookie = "symbimgtd=close";
		}
	}
}

function initImageTool(imgId){
	var img = document.getElementById(imgId);
	if(!img.complete){
		imgWait=setTimeout(function(){initImageTool(imgId)}, 500);
	}
	else{
		var portWidth = 400;
		var portHeight = 400;
		var portXyCookie = getCookie("symbimgport");
		if(portXyCookie){
			portWidth = parseInt(portXyCookie.substr(0,portXyCookie.indexOf(":")));
			portHeight = parseInt(portXyCookie.substr(portXyCookie.indexOf(":")+1));
		}
		$(function() {
			$(img).imagetool({
				maxWidth: 6000
				,viewportWidth: portWidth
		        ,viewportHeight: portHeight
			});
		});
	}
}

function setPortXY(portWidth,portHeight){
	document.cookie = "symbimgport=" + portWidth + ":" + portHeight;
}

function initImgRes(){
	var imgObj = document.getElementById("activeimg-"+activeImgIndex);
	if(imgObj){
		if(imgLgArr[activeImgIndex]){
			var imgRes = getCookie("symbimgres");
			if(imgRes == 'lg') changeImgRes('lg');
		}
		else{
			imgObj.src = imgArr[activeImgIndex];
			document.getElementById("imgresmed").checked = true;
			var imgResLgRadio = document.getElementById("imgreslg");
			imgResLgRadio.disabled = true;
			imgResLgRadio.title = "Large resolution image not available";
		}
		if(imgArr[activeImgIndex]){
			//Do nothing
		}
		else{
			if(imgLgArr[activeImgIndex]){
				imgObj.src = imgLgArr[activeImgIndex];
				document.getElementById("imgreslg").checked = true;
				var imgResMedRadio = document.getElementById("imgresmed");
				imgResMedRadio.disabled = true;
				imgResMedRadio.title = "Medium resolution image not available";
			}
		}
	}
}

function changeImgRes(resType){
	var imgObj = document.getElementById("activeimg-"+activeImgIndex);
	var oldSrc = imgObj.src;
	if(resType == 'lg'){
        document.cookie = "symbimgres=lg";
    	if(imgLgArr[activeImgIndex]){
    		imgObj.src = imgLgArr[activeImgIndex];
    		document.getElementById("imgreslg").checked = true;
    	}
	}
	else{
        document.cookie = "symbimgres=med";
    	if(imgArr[activeImgIndex]){
    		imgObj.src = imgArr[activeImgIndex];
    		document.getElementById("imgresmed").checked = true;
    	}
	}
}

function rotateImage(rotationAngle){
	var imgObj = document.getElementById("activeimg-"+activeImgIndex);
	var imgAngle = 0;
	if(imgObj.style.transform){
		var transformValue = imgObj.style.transform;
		imgAngle = parseInt(transformValue.substring(7));
	}
	imgAngle = imgAngle + rotationAngle;
	if(imgAngle < 0) imgAngle = 360 + imgAngle;
	else if(imgAngle == 360) imgAngle = 0;
	imgObj.style.transform = "rotate("+imgAngle+"deg)";
	$(imgObj).imagetool("option","rotationAngle",imgAngle);
	$(imgObj).imagetool("reset");
}

function ocrImage(ocrButton, target, imgidVar, imgCnt){
	ocrButton.disabled = true;
	let wcElem = document.getElementById("workingcircle-"+target+"-"+imgCnt);
	wcElem.style.display = "inline";
	
	let imgObj = document.getElementById("activeimg-"+imgCnt);
	let xVar = 0;
	let yVar = 0;
	let wVar = 1;
	let hVar = 1;
	let ocrBestVar = 0;

	if(document.getElementById("ocrfull-"+target).checked == false){
		xVar = $(imgObj).imagetool("properties").x;
		yVar = $(imgObj).imagetool("properties").y;
		wVar = $(imgObj).imagetool("properties").w;
		hVar = $(imgObj).imagetool("properties").h;
	}
	if(document.getElementById("ocrbest").checked == true){
		ocrBestVar = 1;
	}

	$.ajax({
		type: "POST",
		url: "rpc/ocrimage.php",
		data: { imgid: imgidVar, target: target, ocrbest: ocrBestVar, x: xVar, y: yVar, w: wVar, h: hVar }
	}).done(function( msg ) {
		let rawStr = msg;
		document.getElementById("tfeditdiv-"+imgCnt).style.display = "none";
		document.getElementById("tfadddiv-"+imgCnt).style.display = "block";
		let addform = document.getElementById("ocraddform-"+imgCnt);
		addform.rawtext.innerText = rawStr;
		addform.rawtext.textContent = rawStr;
		//Add OCR source with date
		let today = new Date();
		let dd = today.getDate();
		let mm = today.getMonth()+1; //January is 0!
		let yyyy = today.getFullYear();
		if(dd<10) dd='0'+dd;
		if(mm<10) mm='0'+mm;
		if(target == "tess") target = "Tesseract";
		else target = "Digi-Leap";
		addform.rawsource.value = target+": "+yyyy+"-"+mm+"-"+dd;
		
		wcElem.style.display = "none";
		ocrButton.disabled = false;
	});
}

// Function to run OCR via Vouchervision-Go API
async function ocrVV(ocrButton, imgCnt){

	// Get the busy indicator and image url
	const busy = $('#workingcircle-vv-' + imgCnt);
	const imgurl = $('#activeimg-' + imgCnt).attr('src');

	// Get user-selected parameters
	const prompt = $('#prompt').val();
	const llm_model = $('#llm-model').val();
    const engines = [];
    $('input[name="engines"]:checked').each(function() {
        engines.push($(this).attr('id'));
    });
    const ocrOnly = $('#ocrOnly').is(':checked');

    // Show busy indicator
    busy.show();

    // Disable additional OCR Image button presses
    $(ocrButton).prop('disabled', true);

    // Symbiota field mappings for data returned by various prompts
	const vvMapping = {
		SLTPvM_default: {
			// James Note: Catch-all field: Turning this off may be preferred, it accumulates a lot of junk.
			additionalText: "occurrenceremarks",
			// James Note: I think this is currently a bit shaky for an important field
			//catalogNumber: "catalognumber",
			collectedBy: "recordedby",
			collectionDate: "eventdate",
			collectionDateEnd: "eventdate2",
			collectorNumber: "recordnumber",
			continent: "continent",
			country: "country",
			county: "county",
			cultivated: "cultivationstatus", // checkbox
			// James Note: decimal lat/long can sometimes be hallucinated,
			// incorrectly derived from locality,
			// or improperly converted from other coordinates
			decimalLatitude: "decimallatitude",
			decimalLongitude: "decimallongitude",
			//elevationUnits: "",
			//genus: "",
			habitat: "habitat",
			//identificationHistory: "",
			identifiedBy: "identifiedby",
			identifiedConfidence: "identificationqualifier",
			identifiedDate: "dateidentified",
			identifiedRemarks: "identificationremarks",
			locality: "locality",
			maximumElevationInMeters: "maximumelevationinmeters",
			minimumElevationInMeters: "minimumelevationinmeters",
			scientificName: "sciname",
			scientificNameAuthorship: "scientificnameauthorship",
			//specificEpithet: "",
			specimenDescription: "verbatimattributes",
			stateProvince: "stateprovince",
			verbatimCollectionDate: "verbatimeventdate",
			verbatimCoordinates: "verbatimcoordinates"
		},
		OSC_Symbiota: {
			// James Note: I think this is currently a bit shaky for an important field
			//catalogNumber: "catalognumber",
			collector: "recordedby",
			associatedCollectors: "associatedcollectors",
			collectorNumber: "recordnumber",
			verbatimCollectionDate: "verbatimeventdate",
			collectionDate: "eventdate",
			scientificName: "sciname",
			scientificNameAuthorship: "scientificnameauthorship",
			family: "family",
			// James Note: Asking for genus, specific epithet and infra-epithet and
			// constructing a scientific name with that was more reliable than the
			// scientific name returned by Vouchervision for Gemini 1.5 Flash at least
			// Tendency for the full scientific name to include authors
			//genus: "",
			//specificEpithet: "",
			//infraspecificEpithet: "",
			identifiedBy: "identifiedby",
			identifiedConfidence: "identificationqualifier",
			identifiedDate: "dateidentified",
			identifiedRemarks: "identificationremarks",
			continent: "continent",
			country: "country",
			stateProvince: "stateprovince",
			county: "county",
			locality: "locality",
			// James Note: decimal lat/long can sometimes be hallucinated,
			// incorrectly derived from locality,
			// or improperly converted from other coordinates
			decimalLatitude: "decimallatitude",
			decimalLongitude: "decimallongitude",
			verbatimCoordinates: "verbatimcoordinates",
			datum: "geodeticdatum",
			verbatimElevation: "verbatimelevation",
			cultivated: "cultivationstatus",
			habitat: "habitat",
			specimenDescription: "verbatimattributes",
			associatedSpecies: "associatedtaxa",
			// James Note: Catch-all field: Turning this off may be preferred
			additionalText: "occurrenceremarks",
		}
	}

	// Construct a data object with parameters to pass to the API
	const vvData = {
		image_url: imgurl,
		engines: engines,
		prompt: prompt + '.yaml',
		llm_model: llm_model,
		ocr_only: ocrOnly
	};

	// Start a timer to check how long the API call took
	var start = Date.now();

    // Send the request to VoucherVision-Go
	$.ajax({
		type: 'POST',
		url: 'rpc/voucherVisionGo.php',
		data: JSON.stringify(vvData),
		dataType: 'json',
		contentType: 'application/json',
		success: function (data) {

			// Object to store the costs
			let cost = {"ocr": 0, "transcription": 0, "total": 0};

			// Get transcription cost
			cost.transcription = data.parsing_info.cost_in + data.parsing_info.cost_out;

			// Get OCR cost
			Object.keys(data.ocr_info).forEach(key => {
				cost.ocr += data.ocr_info[key].cost_in + data.ocr_info[key].cost_out;
			});

			// Calculate the total cost
			cost.total = cost.transcription + cost.ocr;

			// Format the cost as a currency string
			let costStr = new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD',
				minimumFractionDigits: 6}).format(cost.total);

			// Write out message on success, along with the time, cost, and returned data object
			console.log('VoucherVision-Go returned data after ' + ((Date.now() - start)/1000).toFixed(2) +
				' seconds. Total cost: ' + costStr + '\n', data);

			// Get the OCR text
			let ocr = data.ocr;

			// Hide the edit div (existing content), and show the add div for new OCR content
			$('#tfeditdiv-' + imgCnt).hide();
			$('#tfadddiv-' + imgCnt).show();

			// Add OCR data to the editor
			$('#tfadddiv-' + imgCnt + ' textarea[name="rawtext"]').val(ocr);

			// Construct the OCR source string
			// Add the date
			let today = new Date();
			// Add leading zeros to day and month
			let dd = String(today.getDate()).padStart(2, '0');
			let mm = String(today.getMonth()+1).padStart(2, '0'); //January is 0!
			let yyyy = today.getFullYear();

			// Add Vouchervision-Go, the date, and the OCR/Transcription models to the source string
			let sourceStr = 'Vouchervision-Go: ' + yyyy + '-' + mm + '-' + dd;
			sourceStr += '; OCR Model(s): ' + engines.join("+");
			sourceStr += '; Transcription Model: ' + llm_model + "; Prompt: " + prompt;

			// Put the source string in the OCR source field
			$('input[name="rawsource"]').val(sourceStr);

			// If not just OCRing, add the data to the editor using the field mappings
			if(!ocrOnly){

				// Get the categorized data
				let json = data.formatted_json;

				// Color to highlight the form fields that have been changed by Vouchervision-Go
				let vvColor = 'moccasin';

				// Make sure there is data returned before proceeding
				if(json){
					// Cycle through all the fields in the field mapping object for the given prompt
					for (const [field, mapping] of Object.entries(vvMapping[prompt])) {

						// Get the edit form element
						const elem = $('[name="' + mapping + '"]');

						// First modify cultivationstatus if needed, this is a checkbox. Save status if the element is not disabled
						if(json[field] && mapping == "cultivationstatus" && !elem.prop('disabled')) {

							// Set cultivation status and highlight the checkbox
							elem.prop('checked', true);
							elem.css({"accent-color": vvColor, "box-shadow": "0 0 2px 1px gray"});

							// Trigger a fieldChanged event
							fieldChanged(mapping);

						// For the rest of the fields, avoid saving data into disabled, hidden, or non-empty elements
						} else if (json[field] && !elem.prop('disabled') && elem.attr('type') != 'hidden' && elem.val() === '') {

							// Save data returned from VoucherVision to the mapped form element and highlight the form element
							elem.val(json[field]);
							elem.css('background-color', vvColor);

							// Trigger a fieldChanged event
							fieldChanged(mapping);

						}
					}
				}
			}

			// Stop the busy indicator, and re-enable OCR button
			busy.hide();
			$(ocrButton).prop('disabled', false);
		},
		error: function(xhr, status, error) {
			// Failed to get data back from the Vouchervision-Go API
			console.log("Failed to get an OCR response from Vouchervision-Go", xhr, status, error);
			alert("Failed to get an OCR response from Vouchervision-Go");

			// Stop busy indicator, and re-enable OCR button
			busy.hide();
			$(ocrButton).prop('disabled', false);
		}
	});
}



function nlpLbcc(nlpButton,prlid){
	document.getElementById("workingcircle_lbcc-"+prlid).style.display = "inline";
	nlpButton.disabled = true;
	var f = nlpButton.form;
	var rawOcr = f.rawtext.innerText;
	if(!rawOcr) rawOcr = f.rawtext.textContent;
	var cnumber = f.cnumber.value;
	var collid = f.collid.value;
	//alert("rpc/nlplbcc.php?collid="+collid+"&catnum="+cnumber+"&rawocr="+rawOcr);
	$.ajax({
		type: "POST",
		url: "rpc/nlplbcc.php",
		data: { rawocr: rawOcr, collid: collid, catnum: cnumber }
	}).done(function( msg ) {
		pushDwcArrToForm(msg, "#ebbb7f");
		nlpButton.disabled = false;
		document.getElementById("workingcircle_lbcc-"+prlid).style.display = "none";
	});
}

function nlpSalix(nlpButton,prlid){
	document.getElementById("workingcircle_salix-"+prlid).style.display = "inline";
	nlpButton.disabled = true;
	var f = nlpButton.form;
	var rawOcr = f.rawtext.innerText;
	if(!rawOcr) rawOcr = f.rawtext.textContent;
	//alert("rpc/nlpsalix.php?rawocr="+rawOcr);
	$.ajax({
		type: "POST",
		url: "rpc/nlpsalix.php",
		data: { rawocr: rawOcr }
	}).done(function( msg ) {
		pushDwcArrToForm(msg,"#77dd77");
		nlpButton.disabled = false;
		document.getElementById("workingcircle_salix-"+prlid).style.display = "none";
	});
}

function pushDwcArrToForm(msg,bgColor){
	try{
		var dwcArr = $.parseJSON(msg);
		var f = document.fullform;
		//var fieldsTransfer = "";
		//var fieldsSkip = "";
		var scinameTransferred = false;
		var verbatimElevTransferred = false;
		for(var k in dwcArr){
			try{
				if(k != 'family' && k != 'scientificnameauthorship'){
					var elem = f.elements[k];
					var inVal = dwcArr[k];
					if(inVal && elem && elem.value == "" && elem.disabled == false && elem.type != "hidden"){
						if(k == "sciname") scinameTransferred = true;
						if(k == "verbatimelevation") verbatimElevTransferred = true;
						elem.value = inVal;
						elem.style.backgroundColor = bgColor;
						//fieldsTransfer = fieldsTransfer + ", " + k;
						fieldChanged(k);
					}
					else{
						//fieldsSkip = fieldsSkip + ", " + k;
					}
				}
			}
			catch(err){
				//alert(err);
			}
		}
		if(scinameTransferred) verifyFullFormSciName();
		if(verbatimElevTransferred) parseVerbatimElevation(f);
		//if(fieldsTransfer == "") fieldsTransfer = "none";
		//if(fieldsSkip == "") fieldsSkip = "none";
		//alert("Field parsed: " + fieldsTransfer + "\nFields skipped: " + fieldsSkip);
	}
	catch(err){
		//JSON parsing error
		//alert(msg);
		alert(err);
	}
	
}

function nextLabelProcessingImage(imgCnt){
	document.getElementById("labeldiv-"+(imgCnt-1)).style.display = "none";
	var imgObj = document.getElementById("labeldiv-"+imgCnt);
	if(!imgObj){
		imgObj = document.getElementById("labeldiv-1");
		imgCnt = "1";
	}
	imgObj.style.display = "block";
	
	initImageTool("activeimg-"+imgCnt);
	activeImgIndex = imgCnt;
	
	return false;
}

function nextRawText(imgCnt,fragCnt){
	document.getElementById("tfdiv-"+imgCnt+"-"+(fragCnt-1)).style.display = "none";
	var fragObj = document.getElementById("tfdiv-"+imgCnt+"-"+fragCnt);
	if(!fragObj) fragObj = document.getElementById("tfdiv-"+imgCnt+"-1");
	fragObj.style.display = "block";
	ocrFragIndex = fragCnt;
	return false;
}
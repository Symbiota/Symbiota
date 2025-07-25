// ########################################
// Javascript to handle iNaturalist observation import & other iNat-related functions.
// Depends on the iNatJS class to interface with the iNaturalist API:
// https://github.com/mickley/iNatJS/

// ##### Options for Portal Managers #####


// Choice of google (requires GOOGLE_MAP_KEY in symbini.php), or open-elevation (free, but 1000/month, 1/second)
const elevAPI = "google";

// Choice of google (requires GOOGLE_MAP_KEY in symbini.php), inat (much slower), or none
const geocodeAPI = "google";

// Use iNaturalist's UUID as the associated occurence identifier (if false, uses the older numeric iNaturalist identifier)
const identifierUUID = true

// Query the iNaturalist API for higher taxonomy (family, order, etc.)
// Nice, but a lot of extra API calls, so it is off by default
const addTaxonomy = false;

// Infraspecific abbreviations to use for scientificName
const infraAbbrev = {ssp: "subsp.", var: "var.", form: "f."}

// Set the number of observations to get per page/API callback
// 200 is the max, but slower. Users can scroll to load another page
const per_page = 30

// Array of data/media licenses that are allowed to be imported without any issues
const importLicenses = ["cc-by-nc", "cc-by", "cc0"];


// ##### Define Variables #####


// Make a new class instance
const iNat = new iNatJS();

// Global variables
var iNatData = [];
var records = {};
var page = 1;
var pages = 0;
var ready = true;
var activeAPICalls = 0;

// Keep track of records that are skipped due to permission issues
const skipped = {licenseSkipped: 0, geoprivacySkipped: 0}

// Copyrighted work maybe can't be shown
// Sharealike licenses require the same license
const licenses = {
	"cc-by" : "https://creativecommons.org/licenses/by/4.0/",
	"cc-by-nc" : "https://creativecommons.org/licenses/by-nc/4.0/",
	"cc-by-nd" : "https://creativecommons.org/licenses/by-nd/4.0/",
	"cc-by-sa" : "https://creativecommons.org/licenses/by-sa/4.0/",
	"cc-by-nc-sa" : "https://creativecommons.org/licenses/by-nc-sa/4.0/",
	"cc-by-nc-nd" : "https://creativecommons.org/licenses/by-nc-nd/4.0/",
	"cc0" : "https://creativecommons.org/publicdomain/zero/1.0/",
	"null" : "", // Copyrighted
}

// Fields to harvest from the observations endpoint for po tential import
const obsFields = {
	annotations: {
		controlled_attribute_id: "",
		controlled_value_id: ""
	},
	captive: "",
	created_at: "",
	description: "",
	geojson: "",
	id: "",
	license_code: "",
	//location: "", // needed?
	obscured: "",
	observed_on: "",
	observed_on_string: "",
	ofvs: {
		id: "",
		name: "",
		value: ""
	},
	owners_identification_from_vision: "",
	photos: {
		attribution: "",
		license_code: "",
		url: ""
	},
	place_guess: "",
	place_ids: "",
	positional_accuracy: "",
	private_geojson: "",
	//private_location: "", // Needed?
	private_place_guess: "",
	private_place_ids: "",
	quality_grade: "",
	species_guess: "",
	taxon: {
		name: "",
		rank: ""
	},
	uri: "",
	user: {
		login: "",
		name: ""
	}
}


// ##### Main Functions #####


// Verify that the user is authenticated
function verifyAuthentication() {

	// Get the user-provided API token
	let apiToken = $('#apitoken').val();

	// Exit if there is no API token
	if(apiToken === undefined) {
		return false; // No page load (not authorized)
	}else if (apiToken == "") {
		$("#inat-status").css('color', 'red');
		$("#inat-status").html(lang['AUTH_NONE']);
		$("#form-apitoken").val('');
		return true; 
	}

	// Remove extraneous parts of the API token response (e.g., quotes, braces)
	apiToken = apiToken.replace(/{|}|"|api_token|:|\s/g, '');;
	$("#apitoken").val(apiToken);

	// Check authorization with iNaturalist
	iNat.checkAuthentication(apiToken, function(success, data) {
		if (success) {
			$("#inat-status").css('color', 'green');
			$("#inat-status").html(lang['AUTH_SUCCESS'] + ': ' + iNat.iNatAuthorized);
			$("#form-apitoken").val(apiToken);
			window.localStorage.setItem('iNatAPIKey', apiToken);
		} else {
			$("#inat-status").css('color', 'red');
			$("#inat-status").html(lang['AUTH_FAIL']);
			$("#form-apitoken").val('');
			window.localStorage.removeItem('iNatAPIKey');
		}
	});
	return true;
}


// Get observation search data from iNaturalist using the parameters specified in the GUI
function getiNat(next){

	// Check if authorized
	if(!iNat.iNatAuthorized){
		alert(lang['AUTHORIZE']);
		return;
	}

	// Check for search parameters
	if(!$('#url').val()){

		$('#resultsbox').show();
		$('#status').html('<span style="color: red;">' + lang['NO_SEARCH'] + '</span>');

	} else {

		// Get the search parameters
		let params = iNat.getUrlParams($('#url').val());

		// Reset the search if it's not just the next page of results
		if(!next) resetResults();

		$('#resultsbox').show();
		$('#status').html("<h2 class='loading'>" + lang['LOADING'] + "...</h2>");

		// If the page parameter is already set, use that. Otherwise, use the current results page
		if(params.page) {
			page = params.page;
		} else {
			params.page = page;
		}

		// If per_page isn't specified in the URL parameters, use the number defined in per_page (30 is the iNat default)
		if(!params.per_page) params.per_page = per_page;

		// Get the data from iNaturalist
		iNat.queueINatRequest({
			method: 'GET',
			apiVersion: "v2",
			endpoint: "observations",
			params: params,
			fields: iNat.toRISON(obsFields),
			success: function(data) {

				// Clear status message
				$('#status').html('');

				// Get the total number of results pages
				pages = Math.ceil(data.total_results/data.per_page);

				// Log things
				console.log(pages, data.results);

				// Append the array of results
				iNatData.push(...data.results);

				// Add the new results to the results table and show it
				$('#resultsbox').show();
				buildTable(data.results);

				// Ready for scrolling for more results and running another request
				ready = true;
				
			}, 
			error: function(xhr, status, error) {
				console.log(status + ' ' + xhr.status + ': ' + error);
				$('#status').html('<span style="color: red;">' + lang['SEARCH_ERROR'] + '</span>');
			}
		});
	}
}


// Build a table showing the iNaturalist records returned for users to pick records to import
function buildTable(data) {

	let tbody = '';

	// Add the results table if it doesn't already exist.
	if (!$('#resultstable').length){

		// Construct the results table and append it to the results div
		$('#results').append(
			'<div id="resultstable">' + 
			'<table>' +
			'<thead>' +
			'     <tr>' +
			'        <th><input id="checkall" type="checkbox" checked onClick="toggleCheckboxes(this)"/></th>' +
			'        <th style="width: 75px;">' + lang['HEADER_PHOTO'] + '</th>' +
			'        <th>' + lang['HEADER_SCINAME'] + '</th>' +
			'        <th>' + lang['HEADER_OBSERVED'] + '</th>' +
			'        <th>' + lang['HEADER_UPLOADED'] + '</th>' +
			'        <th>' + lang['HEADER_LOCATION'] + '</th>' +
			'        <th>' + lang['HEADER_OBSERVER'] + '</th>' +
			'     </tr>' +
			'  </thead>' +
			'  <tbody id="resultsbody">' +
			'  </tbody>' +
			'</table>' +
			'<button onclick="$(\'html, body\').scrollTop($(\'#resultsbox\').offset().top); return false;" id="topButton" title="Go to top">' + lang['TOP_BUTTON'] + '</button>' +
			'</div>'
		);
	}

	// Cycle through each record in the data and display it
	for (record of data) {

		// Deal with obscured coordinates

		// iNat Field           Open           Obscured          Taxon         Private   <--- Type of geoprivacy on iNat
		// geoprivacy           null           obscured          obscured      private
		// obscured             false          true              true          true
		// taxon_geoprivacy     null           null              obscured      null
		// geojson              obj            obj               obj           null

		// Record is obscured, but the user has access to the obscured data
		if (record.obscured && record.private_geojson) {

			// Switch to private data
			record.latitude = record.private_geojson.coordinates[1].toFixed(5);
			record.longitude = record.private_geojson.coordinates[0].toFixed(5);
			record.place_guess = record.private_place_guess;
			record.place_ids = record.private_place_ids ? record.private_place_ids : record.place_ids;

		// Record is obscured, and user does not have access to obscured data, or no coordinates are present
		// Don't import, skip it
		} else if (record.obscured || !record.geojson) {


			record.latitude = record.longitude = null;
			skipped.geoprivacySkipped++;

			// Skip to next record
			continue;

		// Record is open and coordinates are present
		} else {

			record.latitude = record.geojson.coordinates[1].toFixed(5);
			record.longitude = record.geojson.coordinates[0].toFixed(5);

		} 
		
		// Check license, don't allow importing unless the authorized user is the observer, or the work is licensed permissively
		// If it fails this check, the record won't be displayed and selectable to import with the table row below
		if(iNat.iNatAuthorized == record.user.login || importLicenses.includes(record.license_code)) {

			// Variables needing special treatment
			let acc = record.positional_accuracy ? ' ±' + record.positional_accuracy + ' m.' : '';

			let rg = record.quality_grade == 'research' ? '<span title="Research Grade">RG</span>' : '';

			// Construct table row for each observation
			tbody += '     <tr>';
			tbody += '        <td class="researchgrade"><input name="id[]" value="' + record.id + '" type="checkbox" checked/>' + rg + '</td>';
			tbody += '        <td>';
			if(record.photos[0]) tbody += '            <a href="' + record.uri + '" target="_blank"><img src="' + (record.photos[0] ? record.photos[0].url : '') + '"></img></a>';
			tbody += '        </td>';
			tbody += '        <td style="font-style: italic;"><a href="' + record.uri + '" target="_blank">' + 
				iNat.formatName(record.taxon, infraAbbrev).scientificName + '</a></td>';
			tbody += '        <td>' + formatDateStr(record.observed_on_string) + '</td>';
			tbody += '        <td>' + formatDateStr(record.created_at) + '</td>';
			tbody += '        <td>' + (record.place_guess ? record.place_guess : '') + '<br/><a href="http://maps.google.com/?q=' + record.latitude + ',' + record.longitude + '" target="blank">' + record.latitude + ', ' + record.longitude + '</a><br/>' + acc + '</td>';
			tbody += '        <td>' + (record.user.name != null ? record.user.name : '') + '<br/><a href="https://www.inaturalist.org/people/' + record.user.login + '" target="_blank">@' + record.user.login + '</a></td>';
			tbody += '     </tr>';

		} else {

			// Skipped due to licensing restrictions
			skipped.licenseSkipped++;
		}
	} 

	if (skipped.licenseSkipped || skipped.geoprivacySkipped) {
		// Show report of skipped records
		$('#status').html('<span style="color: #FFC300;">' + lang['SKIPPED'] + ' ' + (skipped.licenseSkipped + 
			skipped.geoprivacySkipped) + ' ' + lang['OBSCURED_COORDS'] + ' (' + 
			skipped.geoprivacySkipped + ') ' + lang['LICENSING_RESTRICT'] + ' (' + 
			skipped.licenseSkipped + ')</span>');
	}

	// Add the results to the results table
	$('#resultsbody').append(tbody);
}


// Parse the selected iNaturalist records into a JSON format to pass along to Symbiota
function parseSelected(){

	// Get array of observation checkboxes that are checked
	const checked = $("input[name='id[]']").map(function(){
		if($(this).is(":checked")) return $(this).val();
	}).get();

	// Filter the array returned by iNaturalist to only include the selected records
	const selected = iNatData.filter(function(record){
		return checked.indexOf(String(record.id)) > -1;
	});

	// Parse the selected records
	records = selected.map(function(record) {

		// Map the fields from the API to dwC/Symbiota terminology in a 1D array
		// These are the easy ones, we'll add more as we go
		let recordArray = {

			// Use the user's name if set. This will be empty for iNat users who haven't set a name and only have a login
			"collector": record.user.name,

			// DarwinCore
			"eventDate": record.observed_on,
			"description" : record.description,

			// Location data, rounding to 6 decimal places 
			"latitude": record.latitude, 
			"longitude": record.longitude, 
			"coordinateUncertaintyInMeters": record.positional_accuracy, 

			// Presumed to be WGS84 for iNaturalist, though some hand coordinates may not be
			"geodeticDatum": "WGS84",

			// Symbiota fields
			// Change from true/false to 1 and 0
			"cultivationStatus": (record.captive ? 1 : 0),

			// iNaturalist data
			"iNatID": record.id,
			"iNatURL": record.uri, 
			"dbpk": record.uuid,
			"uuid": record.uuid,
			"thumbnailURL": record.photos[0].url, 
			"iNatUsername": record.user.login,
		};

		// Add associated occurrences links as JSON
		const associatedOccurrences = [
			{
				"type": "symbiotaAssociations",
				"version": "1.1",
				"associations": [{
					"associationType": "externalOccurrence",
					"relationship": "iNaturalistObservation",
					"basisOfRecord": 'HumanObservation',
					"resourceUrl": 'https://www.inaturalist.org/observations/' + record.uuid,
					"objectID": identifierUUID ? record.uuid : record.id // Choice of using uuid or numeric id
				}]
			},
			{
				"type": "verbatimText",
				"verbatimText": ""
			}
		];
		recordArray['associatedOccurrences'] = JSON.stringify(associatedOccurrences);

		// Add what we can from the name returned by the taxon
		$.extend(recordArray, iNat.formatName(record.taxon, infraAbbrev));

		// Add family and order fields from iNaturalist, if specified (extra API calls)
		if(addTaxonomy) {
			iNat.getTaxonomy(record.taxon.id, function(success, taxonomy){
				if(success) $.extend(recordArray, taxonomy[0]);
			}, 
			["class", "order", "family"]);
		}
		
		// Add locality admin levels (country, state/province/county) from either Google or iNaturalist
		if(geocodeAPI == "google" && google) {

			// Add locality admin levels using Google's reverse geocode and the lat/long coords if Google API JS is loaded
			activeAPICalls++;
			getGeocode(record.latitude, record.longitude, function(success, adminLevels){
				activeAPICalls--;
				if(success) $.extend(recordArray, adminLevels);
			})
		} else if (geocodeAPI == "inat"){

			// Add locality admin levels from iNaturalist list of place IDs
			iNat.getAdminLevels(record.place_ids, function(success, adminLevels) {
				if(success) $.extend(recordArray, adminLevels);
			});
		}

		// Add elevation from API if enabled in the import options
		if($('#addelev').is(":checked")) {

			// Add elevation from Google API if specified and Google API JS is loaded
			if(elevAPI == "google" && google) {
				activeAPICalls++;
				getElevationGoogle(recordArray.latitude, recordArray.longitude, function(success, elev){
					if(success) recordArray.minimumElevationInMeters = Math.round(elev);
					activeAPICalls--;
				});

			// Otherwise, use Open-Elevation
			} else {
				activeAPICalls++;
				getOpenElevationRateLimited(recordArray.latitude, recordArray.longitude, function(success, elev){
					if(success) recordArray.minimumElevationInMeters = Math.round(elev);;
					activeAPICalls--;
				});
			}
		}

		// Get any annotations: phenology, life stage, sex, etc. 
		getAnnotations(record.annotations, function(annotations){
			 $.extend(recordArray, annotations);
		});

		// Add iNaturalist observation fields, if enabled
		if($('#obsfields').is(":checked")) {

			// Append any and all custom fields to recordArray as key:value pairs
			// Note: this can overwrite existing fields
			record.ofvs.map(function(fld){
				recordArray[fld.name] = fld.value;
			});
		}

		// Add associated taxa from iNaturalist using a radius search if enabled, and the field is not already present
		if($('#assoctaxa').is(":checked") && !recordArray.hasOwnProperty('associatedTaxa')) {
			getAssociatedTaxa(record.user.id, record.observed_on, record.uuid, recordArray['latitude'], recordArray['longitude'], function(success, assocTaxa){
				if(success) recordArray['associatedTaxa'] = assocTaxa;
			});
		}

		// Add common site data, if specified. 
		// Note, this overwrites anything that came from observation fields. 
		if ($('#locality').val()) recordArray['locality'] = $('#locality').val();
		if ($('#habitat').val()) recordArray['Habitat'] = $('#habitat').val();
		if ($('#associatedtaxa').val()) recordArray['associatedTaxa'] = $('#associatedtaxa').val();
		if ($('#associatedcollectors').val()) recordArray['associatedCollectors'] = $('#associatedcollectors').val();

		// Get list of images
		recordArray['images'] = record.photos.map(function(photo) {

			// Check license, don't allow importing unless the authorized user is the observer, or the work has an allowed license
			if(iNat.iNatAuthorized == record.user.login || importLicenses.includes(photo.license_code)) {
		
				// Get the base URL for the image and the extension (can vary)
				const baseurl = photo.url.replace(/(.+)(square\.(jpe?g|png|gif))/, '$1');
				const ext = photo.url.replace(/(.+)(square\.(jpe?g|png|gif))/, '$3');

				const photoObj = {
					"associatedSpecimenReference": 'https://www.inaturalist.org/observations/' + recordArray['uuid'],
					"providerManagedID": photo.id, 
					"goodQualityAccessURI" : baseurl + 'large.' + ext,
					"thumbnailAccessURI" : baseurl + 'small.' + ext,
					"accessURI" : baseurl + 'original.' + ext, 
					"creator" : recordArray['collector'], // recordArray.user.login
					// Construct custom term string instead of using iNat's: photo.attribution
					"usageTerms" : '(c) ' + (recordArray.collector ? recordArray.collector : recordArray.iNatUsername) + ' (' + photo.license_code.toUpperCase() + ')',
					"rights" : licenses[photo.license_code],
					// Other potential fields. See: https://ac.tdwg.org/termlist/
					//"referenceUrl" : "https://www.inaturalist.org/photos/" + photo.photo.id,
					//"initialTimeStamp" : record.observed_on_string // 2021-06-11 18:41:53
					// imagetype
					// format
					// owner
					// sourceUrl
					// accessRights
					// webStatement
				}

				// Return the photo object
				return(photoObj);
			}
		});

		// Return the constructed record object
		return recordArray;
	});

	// Checks whether the iNat API queue is active. If so, waits 100 ms and re-checks
	// This will submit the form when all API calls complete
	iNat.checkiNatQueue(100, function(status) {
		if (status) {

			// Enable progress spinner
			$('#waiting').addClass('spin');
			
		} else {

			// Check for other API calls (non-iNat):
			(function() {
				function checkActiveAPICalls(){
					if(activeAPICalls <= 0) {
						
						// All the API calls have run, so the form can be submitted
						$('#inatimportform').submit();
					} else {
						// check again in 100 ms
						setTimeout(checkActiveAPICalls, 100);
					}
				}
				checkActiveAPICalls();
			})();
		}
	});
}


// ##### Helper Functions to Add Additional Data #####


// Wrapper to modify API functions and rate limit them
// Usage: const funcRateLimited = rateLimit(func, 1000) // 1000 ms rate-limiting
function rateLimit(fn, interval) {
  const queue = [];
  let isRunning = false;

  return function (...args) {
	 queue.push(() => fn.apply(this, args));
	 if (!isRunning) {
		isRunning = true;
		const timer = setInterval(() => {
		  if (queue.length === 0) {
			 clearInterval(timer);
			 isRunning = false;
		  } else {
			 const next = queue.shift();
			 next();
		  }
		}, interval);
	 }
  };
}


// Function to parse the annotations object from iNaturalist into DwC fields lifeStage, Sex, and reproductiveCondition
function getAnnotations(annotations, callback){

	// Make an object to hold our annotations fields
	let annotObj = {}

	// Make arrays for each annotation type, to allow for multiple annotations per type
	let stage = [];
	let phenology = [];
	let sex = [];
	let vitality = [];

	// Parse out each annotation into the correct fields
	annotations.map(function(annot){

		// Life Stage
		if (annot.controlled_attribute_id == 1) {
			// 1: Life Stage - 2: Adult, 3: Teneral, 4: Pupa, 5: Nymph, 6: Larva, 7: Egg, 8: Juvenile, 16: Subimago

			if(annot.controlled_value_id == 2) stage.push("adult");
			if(annot.controlled_value_id == 3) stage.push("teneral");
			if(annot.controlled_value_id == 4) stage.push("pupa");
			if(annot.controlled_value_id == 5) stage.push("nymph");
			if(annot.controlled_value_id == 6) stage.push("larva");
			if(annot.controlled_value_id == 7) stage.push("egg");
			if(annot.controlled_value_id == 8) stage.push("juvenile");
			if(annot.controlled_value_id == 16) stage.push("subimago");

			annotObj.lifeStage = stage.join(',');

		// Sex
		} else if (annot.controlled_attribute_id == 9) {
			// 9: Sex - 10: Female, 11: Male, 20: Cannot be Determined

			if(annot.controlled_value_id == 10) sex.push("female");
			if(annot.controlled_value_id == 11) sex.push("male");

			annotObj.sex = sex.join(',');

		// Plant Phenology
		} else if (annot.controlled_attribute_id == 12) {
			// 12: Plant phenology - 13: Flowering, 14: Fruiting, 15: Flower Budding, 21: No evidence of flowering

			if(annot.controlled_value_id == 13) phenology.push("flowering");
			if(annot.controlled_value_id == 14) phenology.push("fruiting");
			if(annot.controlled_value_id == 15) phenology.push("budding");
			if(annot.controlled_value_id == 21) phenology.push("sterile");

			annotObj.reproductiveCondition = phenology.join(',');

		// Alive or Dead
		} else if (annot.controlled_attribute_id == 17) {
			if(annot.controlled_value_id == 18) vitality.push("alive");
			if(annot.controlled_value_id == 19) vitality.push("dead");
			if(annot.controlled_value_id == 20) vitality.push("uncertain"); // Cannot be Determined

			annotObj.vitality = vitality.join(',');
		}
		
		// Other annotations on iNaturalist
		// 22: Evidence of Presence - 23: Feather, 24: Organism, 25: Scat, 26: Track, 27: Bone, 28: Molt, 29: Gall
		// 36: Leaves - 37: Breaking Leaf Buds, 38: Green Leaves, 39: Colored Leaves, 40: No Live Leaves

	});
	callback(annotObj);
}


// Gets a list of associated taxa within a radius of the focal observation, observed by the same user & on same date
// TODO: is it necessary to have same username???
function getAssociatedTaxa(username, date, id, lat, lng, callback){

	// Get the associated taxa radius, and convert from meters to kilometers
	let assocTaxaRadius = $('#assoctaxaradius').val() / 1000;

	iNat.queueINatRequest({
		method: 'GET',
		apiVersion: "v1", // API v2 doesn't work with the radius parameter
		endpoint: "observations",
		params: {
			user_id: username, // Necesssary to restrict to the same user?
			observed_on: date,
			lat: lat, 
			lng: lng,
			radius: assocTaxaRadius,
			not_id: id,
			hrank: "family" // Restrict to taxa of rank family and below for now
		},
		success: function(data) {

			let observations = data.results
			let assocTaxa = [];

			// Combine all associated taxa observations into a comma-separated list
			observations.map(function(obs){

				let name = '';

				// Get the associated taxon, with any infrataxa ranks included (TODO: add hybrids?)
				if (obs.taxon.rank == "subspecies" || obs.taxon.rank == "variety" || obs.taxon.rank == "form") {
					name = iNat.formatName(obs.taxon, infraAbbrev).scientificName;
				} else {
					name = obs.taxon.name;
				}

				// Add the associated taxon to the list
				assocTaxa.push(name);
			});

			// Return associated taxa as a comma-separated string
			callback(true, assocTaxa.join(', '));
		}, 
		error: function(xhr, status, error) {
			callback(false, {xhr, status, error});
		}
	});
}


// https://developers.google.com/maps/documentation/javascript/elevation
function getElevationGoogle(lat, lng, callback){

	// Create an ElevationService
	const elevator = new google.maps.ElevationService();

	// Query elevation
	elevator.getElevationForLocations(
		{
			'locations': [new google.maps.LatLng(lat,lng)]
		},
		function(results, status) {
			if (status == google.maps.ElevationStatus.OK) {

				// Retrieve the first result, passing it to the callback function
				if (results[0]) {
					callback(true, results[0].elevation);
				}
			} else {
				callback(false, null);
			}
		}
	);
}


// Get Elevation from OpenElevation API
function getOpenElevation(lat, lng, callback) {

	// Query the openElevation API
	$.getJSON( "https://api.open-elevation.com/api/v1/lookup?locations=" + lat + ',' + lng, function(data) {
		callback(true, data.results[0].elevation);
	})
	.fail(function() {
		callback(false, null);
	});
}


// Wrap the getOpenElevation with rate limiting (1 request per second)
const getOpenElevationRateLimited = rateLimit(getOpenElevation, 1000);


// Gets country, state/province, and county from the google reverse geocoding API for a lat/long coordinate
// https://developers.google.com/maps/documentation/javascript/geocoding
function getGeocode(lat, lng, callback){

	const geocoder = new google.maps.Geocoder();
	const latlng = new google.maps.LatLng(lat,lng);

	// Reverse geocode
	geocoder.geocode({location: latlng}, function(results, status){

		if (status == 'OK') {

			let adminLevels = {};

			// Get the address components
			let components = results[0].address_components;

			// If there's only level in the first address component, then it won't have admin areas, so use the next one (less fine resolution)
			if(components.length == 1) components = results[1].address_components;

			// Check all the address components for the admin levels we want
			for (const component of components) {
				if(component.types.includes("country")) {
					adminLevels.country = component.long_name;
				} else  if(component.types.includes("administrative_area_level_1")) {
					adminLevels.stateProvince = component.long_name;
				} else  if(component.types.includes("administrative_area_level_2")) {
				
					// Remove County, Parish, Region, Census Area, District, Borough, Municipality, City and Borough, Regional Municipality, Regional Municipality of
					adminLevels.county = component.long_name.replace(/(^(Regional Municipality of)\s)?(\w*)(\s(County|Parish|Census Area|District|Borough|Municipality|Region|City and Borough|Regional Municipality)$)?/, "$3");
				}
				// Lower levels not implemented
				//else  if(component.types.includes("administrative_area_level_3")) {
				//   // remove ", Unorganized"
				//   adminLevels.municipality = component.long_name.replace(/(\w*)((, Unorganized)$)?/, "$1")
				// } else if(component.types.includes("locality")) {
				//    adminLevels.locality = component.long_name;
				// }
			}

			// Run the callback function and return the admin levels
			callback(true, adminLevels);
		} else {
			callback(false, null);
		}
	});
}


// ##### GUI Manipulation Functions #####


// function to format date & time for display
function formatDateStr(str) {

	// Define days of week
	const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

	// Convert the datetime string to a date
	let d = new Date(str);

	// Get the timezone
	let zone = d.toLocaleTimeString('en-us',{timeZoneName:'short'}).split(' ')[2];

	// Format and return a string
	return days[d.getDay()] + ' ' + d.getFullYear() + '-' + d.getMonth().toString().padStart(2, '0') + '-' +
		d.getDate().toString().padStart(2, '0') + ' ' + d.getHours().toString().padStart(2, '0') + ":" +
		d.getMinutes().toString().padStart(2, '0') + ":" + d.getSeconds().toString().padStart(2, '0') + ' ' + zone;
}


// Update the search url as parameters are added
function buildSearchURL() {

	let searchParams = {}; 

	// Cycle through all the inputs for filtering the iNat search
	// Exclude #place and #taxon, which are only shown for the user, since they use hidden input fields
	$('#iNatSearch input, #iNatSearch select').not('#place, #taxon').each(function(index, element) {

		// Get the value of the input element
		let val = $(element).val();

		// If the input is set, save it as a search parameter
		// Select inputs
		if ($(element).is("select") && val != '') {

			searchParams[element.id] = val;

		// Text, date and hidden inputs
		} else if (Array('text', 'date', 'hidden').includes($(element).attr('type')) && val != '') {

			searchParams[element.id] = val;

		// Radio and checkbox inputs
		} else if(Array('radio', 'checkbox').includes($(element).attr('type')) && $(element).is(':checked')) {

			searchParams[element.name] = val;

		}
		
	});

	// Store the parameters in the search url box
	$('#url').val($.param(searchParams));

	// Save the settings
	saveSettings();
}


// Get a stored iNatAPI key and verify that it can authenticate successfully
function getApiKey(){
	let inatAPIKey = window.localStorage.getItem('iNatAPIKey');
	if(inatAPIKey != ""){
		$('#apitoken').val(inatAPIKey);
		return verifyAuthentication();
	}
}


// Restore saved import and search settings
function getSettings(){

	// First check if settings exist
	if(localStorage.inatSettings  != undefined){

		// Get the settings and parse to an array
		let settings = JSON.parse(localStorage.inatSettings);

		// Cycle through all the settings and restore each element
		settings.forEach(element => {

			// radio button and checkbox, need to handle separately
			if(element.type == "checkbox" || element.type == "radio") {
				$('#' + element.id).prop('checked', element.value);

			// text, date, select
			} else {
				$('#' + element.id).val(element.value);
			}
		});
	}
}


// Save import and search settings
function saveSettings() {

	// Remove the old settings
	localStorage.removeItem('inatSettings');

	// Only save if save Settings is checked
	if($('#savesettings').prop('checked')) {

		let settings = [];
		
		// Cycle through each form element
		$('#options input, #search input, #search select').each(function(index, element) {

			// Save the id, type and value for each element
			settings.push({
				id: $(element).attr('id'), 
				type: $(element).is("select") ? 'select' : $(element).attr('type'), 
				value: Array('radio', 'checkbox').includes($(element).attr('type')) ? $(element).prop('checked') : $(element).val()
			});
		});

		// Save all the settings as JSON
		localStorage.inatSettings = JSON.stringify(settings);
	}
}


// Reset all the search options
function resetSearch() {
	$('#iNatSearch input').val('');
	$('#iNatSearch select').prop('selectedIndex', 0);
	$('#url').val('');
	// Save the settings
	saveSettings();
	resetResults();
}


// Reset all the results parameters when a new search is done. 
function resetResults() {
	iNatData = [];
	page = 1;
	pages = 0;
	skipped.licenseSkipped = 0; 
	skipped.geoprivacySkipped = 0;
	$('#resultstable').remove();
	$('#resultsbox').hide();
}


// Function to toggle all the checkboxes for iNaturalist records
function toggleCheckboxes(check) {
	$("input[name='id[]']").not(check).prop('checked', check.checked);
}


// Function to handle the associated species picker for the site data associated species field
function openAssocSppAid(){
	var assocWindow = open("../editor/assocsppaid.php","assocaid","resizable=0,width=550,height=150,left=20,top=20");
	if(assocWindow != null){
		if (assocWindow.opener == null) assocWindow.opener = self;
		//fieldChanged("associatedtaxa");
		assocWindow.focus();
	}
	else{
		alert("Unable to open associated species tool, which is likely due to your browser blocking popups. Please adjust your browser settings to allow popups from this website.");
	}
}


// Used by dwcDoc() to get the cookie language
function getCookie(cName) {
	var i, x, y;
	var cookieArr = document.cookie.split(";");
	for (i = 0; i < cookieArr.length; i++) {
		x = cookieArr[i].substr(0, cookieArr[i].indexOf("="));
		y = cookieArr[i].substr(cookieArr[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g, "");
		if (x == cName) {
			return unescape(y);
		}
	}
}


// Function to show DwC field help for the site data fields
function dwcDoc(dcTag) {
	var language = getCookie("lang");
	if (language == "es") {
		dwcWindow = open("https://biokic.github.io/symbiota-docs/es/editor/edit/fields/#" + dcTag, "dwcaid", "width=1250,height=300,left=20,top=20,scrollbars=1");
	}
	else {
		dwcWindow = open("https://biokic.github.io/symbiota-docs/editor/edit/fields/#" + dcTag, "dwcaid", "width=1250,height=300,left=20,top=20,scrollbars=1");
	}
	//dwcWindow=open("http://rs.tdwg.org/dwc/terms/index.htm#"+dcTag,"dwcaid","width=1250,height=300,left=20,top=20,scrollbars=1");
	if (dwcWindow.opener == null) dwcWindow.opener = self;
	dwcWindow.focus();
	return false;
}


// ##### Run on Page Load  #####


$(function(){

	// ##### Restore Data, Set Up Listeners #####

	// Get a saved iNaturalist API key and authenticate if possible
	// Quit if it's not possible to get the API key (not authorized)
	if(!getApiKey()) return;

	// Get any stored settings
	getSettings();

	// Add an onChange event to save settings for the import options
	$('#options input').on('change', saveSettings);

	// Show associated taxa radius if Get Associated Taxa is checked
	if($('#assoctaxa').prop('checked')) $('#taxa-radius').toggle();

	// Set up scrolling so that when a user reaches the end of a page, 
	// the next page of results is automatically loaded
	$(window).scroll(function() {

		// Only triggers if the number of pages has been saved, and a request isn't currently running
		if(pages && ready && $(window).scrollTop() + window.innerHeight + 5 >= $(document).height()) {

			// Increment the page and query the API for more results
			if(page < pages) {
				page++;
				ready = false;
				getiNat(true);
			}
		}
	});

	// Search for records when enter is pressed in the search url input box
	$('#url').keyup(function(event){
		 if(event.keyCode == 13){
			  getiNat();
		 }
	});


	// ##### Form Submission Manipulation #####


	// Run on form submit (import selected observations button)
	$('#inatimportform').submit(function(event) {

		// Check to make sure we have records selected before submitting
		if(!records.length > 0) {
			alert(lang['NO_OBS']);
			return false; // Prevent submission
		}

		// Remove the automap option if it's not checked
		if(!$('#automap').is(":checked")) {
			$('input[name="automap"]').remove();
		}

		// Remove the addlink option if it's not checked
		if(!$('#addlink').is(":checked")) {
			$('input[name="addlink"]').remove();
		}

		// Remove the fullimport option if it's not checked
		if(!$('#fullimport').is(":checked")) {
			$('input[name="fullimport"]').remove();
		}

		// Add the parsed iNaturalist data
		$("<input />").attr("type", "hidden")
			 .attr("name", "inatdata")
			 .attr("value", JSON.stringify(records))
			 .appendTo("#inatimportform");

		// For debugging, uncomment these lines to prevent the form from submitting
		//event.preventDefault();
		console.log(records);
		return true;
	});


	// ##### iNaturalist Autocomplete Functions  #####


	// Username autocomplete function
	$("#user_login").autocomplete({
		minLength: 4,
		source: function(request, response) {
			iNat.queueINatRequest({
				method: 'GET',
				apiVersion: "v2",
				endpoint: "users/autocomplete",
				params: {
					//per_page: 10,
					q: request.term
				},
				fields: "icon,name,login",
				success: function(data) {
					response($.map(data.results, function(item) {
						return {
							label : item.login,
							name : item.name,
							name : (item.name ? item.name : ""),
							thumb : (item.icon ? item.icon : "https://www.inaturalist.org/attachment_defaults/users/icons/defaults/thumb.png"),
							value : item.login
						}
					}));
				},
				error: function(xhr, status, error) {
					console.log(status + ' ' + xhr.status + ': ' + error);
				}
			});
		},

		// Runs when the dropdown closes without picking something 
		change: function( event, ui) {
			
			// Did not get an autocomplete result. Unset everything to force use of autocomplete
			if(!ui.item) {
				$("#user_login").val("");
				buildSearchURL();
			}
		},

		// Runs when the dropdown closes
		close: function( event, ui ) {
			buildSearchURL();
		}
		// focus: function( event, ui ) {
		//   $( "#username" ).val( ui.item.value );
		//   return false;
		// },
		 // select: function (event, ui) {
		 //       $("#username").val(ui.item.value);
		 //       return false;
		 // }
	})

	// Customize the dropdown menu
	.data("ui-autocomplete")._renderItem = function(ul, item) {

		// Construct the user row
		let itemHtml = '<div class="ui-menu-picker" style="min-width: 200px;">';
		itemHtml += '<div class="ui-menu-thumb"><img src="' + item.thumb + '"></div>';
		itemHtml += '<div class="ui-menu-label"><span>' + item.label + '</span>';
		itemHtml += '<span class="ui-menu-sublabel">' + item.name + '</span></div>';
		itemHtml += '</div>';

		return $( "<li>" )
			.append( "<a>" + itemHtml + "</a>" )
			.appendTo( ul );
	};


	// Project autocomplete function
	$("#project_id").autocomplete({
		minLength: 4,
		source: function(request, response) {
			iNat.queueINatRequest({
				method: 'GET',
				apiVersion: "v2",
				endpoint: "projects",
				params: {
					// per_page: 10,
					q: request.term
				},
				fields: "id,icon,title,slug",
				success: function(data) {
					response($.map(data.results, function(item) {
						return {
							label : item.title,
							thumb : item.icon,
							value : item.slug
						}
					}));
				},
				error: function(xhr, status, error) {
					console.log(status + ' ' + xhr.status + ': ' + error);
				}
			});
		},

		// Runs when the dropdown closes without picking something 
		change: function( event, ui) {

			// Did not get an autocomplete result. Unset everything to force use of autocomplete
			if(!ui.item) {
				$('#project_id').val(""); 
				buildSearchURL();
			}
		},

		// Runs when the dropdown closes
		close: function( event, ui ) {
			buildSearchURL();
		}
	})

	// Customize the dropdown menu
	.data("ui-autocomplete")._renderItem = function(ul, item) {

		// Construct the taxon row
		let itemHtml = '<div class="ui-menu-picker" style="min-width: 200px;">';
		itemHtml += '<div class="ui-menu-thumb"><img src="' + item.thumb + '"></div>';
		itemHtml += '<div class="ui-menu-label" ><span class="ui-menu-project">' + item.label + '</span></div>';
		itemHtml += '</div>';

		return $( "<li>" )
			.append( "<a>" + itemHtml + "</a>" )
			.appendTo( ul );
	};


	// Place autocomplete function
	$("#place").autocomplete({
		minLength: 4,
		source: function(request, response) {
			iNat.queueINatRequest({
				method: 'GET',
				apiVersion: "v2",
				endpoint: "places",
				params: {
					// per_page: 10,
					q: request.term
				},
				fields: "id,display_name,name,place_type,uuid",
				success: function(data) {
					response($.map(data.results, function(item) {
						return {
							label : item.display_name,
							type : iNat.place_types[item.place_type],
							uuid : item.uuid,
							value : item.id
						}
					}));
				}, 
				error: function(xhr, status, error) {
					console.log(status + ' ' + xhr.status + ': ' + error);
				}
			});
		},

		// Runs when something from the dropdown is selected
		select: function(event, ui) {

			// Prevent a change event for place, and set its value to the label, not the id
			event.preventDefault(); 
			$("#place").val(ui.item.label);

			// Set the hidden place_id field and trigger a change event for it to update the URL
			$('#place_id').val(ui.item.value); 
			buildSearchURL();
		
		},

		// Runs when the dropdown closes without picking something 
		change: function( event, ui) {

			// Did not get an autocomplete result. Unset everything to force use of autocomplete
			if(!ui.item) {
				$("#place").val("");
				$('#place_id').val(""); 
				//$('#place_id').trigger("change");
				buildSearchURL();
			}
		},

		// Runs when the dropdown closes
		close: function( event, ui ) {
			//buildSearchURL();
		}
	})

	// Customize the dropdown menu
	.data("ui-autocomplete")._renderItem = function(ul, item) {
		// Add the place type to the dropdown, if available
		if (item.type) {
			return $( "<li>" )
			  .append( "<a>" + item.label + ' <span class="ui-menu-placetype">' + item.type + "</span></a>" )
			  .appendTo( ul );
		} else {
			return $( "<li>" )
				.append( "<a>" + item.label + "</a>" )
				.appendTo( ul );
		}
	};


	// Identifier username autocomplete function
	$("#ident_user_id").autocomplete({
		minLength: 4,
		source : function( request, response ) {
			iNat.queueINatRequest({
				method: 'GET',
				apiVersion: "v2",
				endpoint: "users/autocomplete",
				params: {
					//per_page: 10,
					q: request.term
				},
				fields: "icon,name,login",
				success: function(data) {
					response($.map(data.results, function(item) {
						return {
							label : item.login,
							name : item.name,
							name : (item.name ? item.name : ""),
							thumb : (item.icon ? item.icon : "https://www.inaturalist.org/attachment_defaults/users/icons/defaults/thumb.png"),
							value : item.login
						}
					}));
				},
				error: function(xhr, status, error) {
					console.log(status + ' ' + xhr.status + ': ' + error);
				}
			});
		},

		// Runs when the dropdown closes without picking something 
		change: function( event, ui) {

			// Did not get an autocomplete result. Unset everything to force use of autocomplete
			if(!ui.item) {
				$("#ident_user_id").val("");
				buildSearchURL();
			}
		},

		// Runs when the dropdown closes
		close: function( event, ui ) {
			//buildSearchURL();
		}
	})

	// Customize the dropdown menu
	.data("ui-autocomplete")._renderItem = function(ul, item) {

		// Construct the user row
		let itemHtml = '<div class="ui-menu-picker" style="min-width: 200px;">';
		itemHtml += '<div class="ui-menu-thumb"><img src="' + item.thumb + '"></div>';
		itemHtml += '<div class="ui-menu-label"><span>' + item.label + '</span>';
		itemHtml += '<span class="ui-menu-sublabel">' + item.name + '</span></div>';
		itemHtml += '</div>';

		return $( "<li>" )
			.append( "<a>" + itemHtml + "</a>" )
			.appendTo( ul );
	};

	// Taxon autocomplete function
	$("#taxon").autocomplete({
		minLength: 4,
		source : function( request, response ) {
			iNat.queueINatRequest({
				method: 'GET',
				apiVersion: "v2",
				endpoint: "taxa/autocomplete",
				params: {
					// per_page: 10,
					q: request.term
				},
				fields: "(name:!t,preferred_common_name:!t,rank:!t,default_photo:(url:!t))",
				// is_active might be needed
				success: function(data) {

					// Make an object of subtaxa abbreviations
					let subtaxa = {subspecies: "ssp.", variety: "var.",form: "f."}

					response($.map(data.results, function(item) {
						// Defaults
						let binomial = subtaxon = infra_rank = "";
						let name = item.name;
						let rank = item.rank;
						
						// Format subtaxon components if needed
						if(item.rank == "subspecies" | item.rank == "variety" | item.rank == "form") {
							let parts = item.name.split(" ");
							binomial = parts.slice(0, 2).join(" ");
							subtaxon = parts[2];
							rank = subtaxa[item.rank];
							name = parts.toSpliced(2, 0, rank).join(" ");
						}

						return {
							label : name,
							rank : rank,
							binomial : binomial,
							subtaxon : subtaxon,
							common_name : item.preferred_common_name,
							thumb : (item.default_photo ? item.default_photo.url : "../../images/plantae-icon.png"),
							value : item.id
						}
					}));
				},
				error: function(xhr, status, error) {
					console.log(status + ' ' + xhr.status + ': ' + error);
				}
			});
		},

		// Runs when something from the dropdown is selected
		select: function(event, ui) {

			// Prevent a change event for taxon, and set its value to the label, not the id
			event.preventDefault(); 
			$("#taxon").val(ui.item.label);

			// Set the hidden taxon_id field and trigger a change event for it to update the URL
		  $('#taxon_id').val(ui.item.value); 
		  //$('#taxon_id').trigger("change");
		  buildSearchURL();
			
		},

		// Runs when the dropdown closes without picking something 
		change: function( event, ui) {

			// Did not get an autocomplete result. Unset everything to force use of autocomplete
			if(!ui.item) {
				$("#taxon").val("");
				$('#taxon_id').val(""); 
				buildSearchURL();
			}
		},

		// Runs when the dropdown closes
		close: function( event, ui ) {
			//buildSearchURL();
		},
	})

	// Customize the dropdown menu
	.data("ui-autocomplete")._renderItem = function(ul, item) {

		// Handle different taxon ranks
		switch(item.rank) {

			// Normal species names
			case "species":
				taxonName = '<i>' + item.label + '</i>';
				break;

			// Format infraspecific names
			case "ssp.":
			case "var.":
			case "f.":
				taxonName = '<i>' + item.binomial + '</i> ' + item.rank + ' <i>' + item.subtaxon + '</i>';
				break;

			// All other ranks
			default:
				taxonName = item.rank[0].toUpperCase() + item.rank.slice(1) + ' <i>' + item.label + '</i>';
				break;
		}

		// Construct the taxon row
		let itemHtml = '<div class="ui-menu-picker">';
		itemHtml += '<div class="ui-menu-thumb"><img src="' + item.thumb + '"></div>';
		itemHtml += '<div class="ui-menu-label"><span>' + taxonName + '</span>';
		itemHtml += '<span class="ui-menu-sublabel">' + (item.common_name ? item.common_name : "") + '</span></div>';
		itemHtml += '</div>';

		return $( "<li>" )
			.append( "<a>" + itemHtml + "</a>" )
			.appendTo( ul );

	};


	// Associated taxa site data autocomplete function. Adapted from Symbiota
	$("#associatedtaxa").autocomplete({
		source: function(request, response) {
			$.getJSON("../editor/rpc/getspeciessuggest.php", { term: request.term.split(/,\s*/).pop() }, response);
		},
		search: function() {
			// custom minLength
			var term = this.value.split(/,\s*/).pop()
			console.log(term);
			if (term.length < 4) return false;
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function(event, ui) {
			var terms = this.value.split(/,\s*/)
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push(ui.item.value);
			this.value = terms.join(", ");
			return false;
		}
	}, { autoFocus: true });
});

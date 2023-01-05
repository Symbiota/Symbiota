function processGbifOrgKey(f){
	var status = true;
	$("#workingcircle").show();

	var gbifInstOrgKey = f.gbifInstOrgKey.value;
	var portalName = f.portalname.value;
	var collName = f.collname.value;
	var datasetKey = f.datasetKey.value;
	var organizationKey = f.organizationKey.value;
	var installationKey = f.installationKey.value;
	var dwcUri = f.dwcUri.value;

	if(gbifInstOrgKey && organizationKey){
		var submitForm = false;
		if(!installationKey){
			installationKey = createGbifInstallation(gbifInstOrgKey,portalName);
			if(installationKey){
				f.installationKey.value = installationKey;
				submitForm = true;
			}
		}
		if(installationKey){
			if(!datasetKey){
				datasetExists(f);
				if(f.datasetKey.value){
					alert("Dataset already appears to exist. Updating database.");
					submitForm = true;
				}
				else{
					datasetKey = createGbifDataset(installationKey, organizationKey, collName);
					f.datasetKey.value = datasetKey;
					if(datasetKey){
						if(dwcUri) f.endpointKey.value = createGbifEndpoint(datasetKey, dwcUri);
						else alert('Please create/refresh your Darwin Core Archive and try again.');
						submitForm = true;
					}
					else{
						alert('Invalid Organization Key or insufficient permissions. Please recheck your Organization Key and verify that this portal can create datasets for your organization with GBIF.');
					}
				}
			}
		}
		if(submitForm) f.submit();
		status = true;
	}
	else{
		alert('Please enter an Organization Key.');
		status = false;
	}
	$("#workingcircle").hide();
	return status;
}

function createGbifInstallation(gbifOrgKey,collName){
	var type = 'POST';
	var data = JSON.stringify({
		endpoint: 'installation',
		organizationKey: gbifOrgKey,
		type: "SYMBIOTA_INSTALLATION",
		title: collName
	});
	var instKey = callGbifCurl(type,data);
	if(!instKey){
		alert("ERROR: Contact administrator, creation of GBIF installation failed using data: "+data);
	}
	return instKey;
}

function createGbifDataset(gbifInstKey,gbifOrgKey,collName){
	var type = 'POST';
	var data = JSON.stringify({
		endpoint: 'dataset',
		installationKey: gbifInstKey,
		publishingOrganizationKey: gbifOrgKey,
		title: collName,
		type: "OCCURRENCE"
	});
	return callGbifCurl(type,data);
}

function createGbifEndpoint(gbifDatasetKey,dwcUri){
	var type = 'POST';
	var data = JSON.stringify({
		endpoint: 'dataset',
		type: "DWC_ARCHIVE",
		url: dwcUri,
		datasetkey: gbifDatasetKey
	});
	var retStr = callGbifCurl(type,url,data);
	if(retStr.indexOf(" ") > -1 || retStr.length < 34 || retStr.length > 40) retStr = "";
	return retStr;
}

function callGbifCurl(type,data){
	var key;
	$.ajax({
		type: "POST",
		url: "rpc/getgbifcurl.php",
		data: {type: type, data: data},
		async: false,
		success: function(response) {
			key = response.trim();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
	return key;
}

function datasetExists(f){
	if(f.dwcUri.value != ""){
		var urlStr = f.dwcUri.value;
		if(urlStr.indexOf("/content/") > 0){
			urlStr = urlStr.substring(0,urlStr.indexOf("/content/"));
			urlStr = "https://api.gbif.org/v1/dataset?identifier=" + urlStr + "/collections/misc/collprofiles.php?collid=" + f.collid.value;
			$.ajax({
				method: "GET",
				async: false,
				dataType: "json",
				url: urlStr
			})
			.done(function( retJson ) {
				if(retJson.count > 0){
					var dsKey = retJson.results[0].key.trim();
					if(dsKey.indexOf(" ") > -1 || dsKey.length < 34 || dsKey.length > 40) dsKey = "";
					f.datasetKey.value = dsKey;
					f.endpointKey.value = retJson.results[0].endpoints[0].key;
					return true;
				}
				else{
					return false;
				}
			})
			.fail(function() {
				alert("General error querying datasets. Is your connection to the network stable?");
				return false;
			});
		}
	}
}
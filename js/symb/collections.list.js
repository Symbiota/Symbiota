// Helper function to get currentPage value, initializing if necessary
function getCurrentPage() {
	if (typeof window.currentPage === 'undefined') {
		window.currentPage = JSON.parse(document.getElementById("all_collections_parent_container")?.dataset?.config || "{}")?.CURRENT_URL;
	}
	return window.currentPage;
}

// Initialize currentPage when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
	getCurrentPage();
});

function copyUrl(comingFrom){
	host = window.location.protocol + '//' + window.location.host;
	var $temp = $("<input>");
	$("body").append($temp);
	let activeLink = host + window.location.pathname;
	const calcualtedQueryStr = calculateQueryStr(comingFrom);
	activeLink = activeLink + "?" + calcualtedQueryStr;
	$temp.val(activeLink).select();
	document.execCommand("copy");
	$temp.remove();
}

function calculateQueryStr(comingFrom) {
  let returnVal = '';
  const comingFromMap = {
		"newsearch": "collections/search/index.php",
		"harvestparams": "collections/harvestparams.php"
	};
	if(comingFrom && comingFromMap[comingFrom]){
		const expectedUrlPart = comingFromMap[comingFrom];
		const currentPage = getCurrentPage();
		const targetUrlPart = currentPage.includes("collections/listtabledisplay.php") ? "collections/listtabledisplay.php" : "collections/list.php";
		const pageKey = 'querystr' + getCurrentPage()?.replace(targetUrlPart, expectedUrlPart);
		const sessionStorageKeys = Object.keys(sessionStorage);
		const relevantKeys = sessionStorageKeys.filter(key => key.startsWith(pageKey) && key.value !== "null");
		relevantKeys.forEach((relevantKey) => {
		  let justFormFieldName = relevantKey.replace(pageKey + "/", "");
		  if(justFormFieldName){
			let relevantVal = sessionStorage.getItem(relevantKey);
			if (relevantVal.includes("db=") || justFormFieldName === "db"){ // it looks like db= is in the value rather than the key for harvestparams but not for search/index.php
				justFormFieldName = "db";
				relevantVal = relevantVal.replace("db=", "");
				if(relevantVal.includes("all")){
					relevantVal = "all";
				}
			}
			returnVal += justFormFieldName + "=" + encodeURIComponent(relevantVal) + "&";
		  }
		});
	}
	return returnVal.slice(0, -1);
}

function addVoucherToCl(occidIn,clidIn,tidIn){
	$.ajax({
		type: "POST",
		url: "../checklists/rpc/linkvoucher.php",
		data: { occid: occidIn, clid: clidIn, taxon: tidIn }
	}).done(function( msg ) {
		alert(msg);
	});
}

function toggleFieldBox(target){
	var objDiv = document.getElementById(target);
	if(objDiv){
		if(objDiv.style.display=="none"){
			objDiv.style.display = "block";
		}
		else{
			objDiv.style.display = "none";
		}
	}
	else{
		var divs = document.getElementsByTagName("div");
		for (var h = 0; h < divs.length; h++) {
			var divObj = divs[h];
			if(divObj.className == target){
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

function openIndPU(occId,clid){
	var wWidth = 1100;
	if(document.body.offsetWidth < wWidth) wWidth = document.body.offsetWidth*0.9;
	var newWindow = window.open('individual/index.php?occid='+occId+'&clid='+clid,'indspec' + occId,'scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=700,left=20,top=20');
	if (newWindow.opener == null) newWindow.opener = self;
	return false;
}

function openMapPU(searchParams = "") {
	const map_params = 'gridSizeSetting=60&minClusterSetting=10&clusterSwitch=y&menuClosed';
	if(!searchParams && window.location.search) {
		if(window.location.search) {
			searchParams = window.location.search + '&' + map_params;
		} else {
			searchParams = '?' + map_params;
		}
	} else {
		searchParams = '?' + searchParams + '&' + map_params;
	}

	const baseUrl = location.href.slice(0, location.href.indexOf("list.php"));
	let mapUrl = new URL(baseUrl + 'map/index.php' + searchParams);

	window.open(mapUrl.href,'Map Search','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=1150,height=900,left=20,top=20');
}

function targetPopup(f) {
	window.open('', 'downloadpopup', 'left=100,top=50,width=900,height=700');
	f.target = 'downloadpopup';
}

function setSessionQueryStr() {
	try {
		const data = document.getElementById('service-container');
		const searchVar = data?.getAttribute('data-search-var');
		if(searchVar) {
			sessionStorage["querystr" + getCurrentPage()] = searchVar;
		}
		return searchVar;
	} catch(err) {
		console.log('ERROR Setting session querystr: ' + err);
		return false;
	}
}

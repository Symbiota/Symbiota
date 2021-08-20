<?php
//Enter one to many custom cascading style sheet files 
//const CSSARR = array('example1.css','example2.css');

//Enter one to many custom java script files 
//const JSARR = array('example1.js','example2.js'); 

//Custom Processing Status setting
//const PROCESSINGSTATUS = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending duplicate','pending review-nfn','pending review','expert required','reviewed','closed');

//Uncomment to turn catalogNumber duplicate search check on/off (on by default)
//define('CATNUMDUPECHECK',true); 

//Uncomment to turn otherCatalogNumbers duplicate search check on/off (on by default)
//define('OTHERCATNUMDUPECHECK',true);

//Uncomment to turn duplicate specimen search function on/off (on by default)
//define('DUPESEARCH',false);

//Uncomment to turn locality event auto-lookup (locality field autocomplete) function on/off (on by default)
//0 = off, permanently deactivated, 1 = activated by default (Default), 2 = deactivated by default
//define('LOCALITYAUTOLOOKUP',1);

//Uncomment to turn the Associated Taxa entry aid (popup to enter associated taxa) on/off (on by default)
//define('ACTIVATEASSOCTAXAAID',true);


// FieldLabel text: uncomment variables and add a value to modify field labels 

//define('CATALOGNUMBERLABEL','');
//define('OTHERCATALOGNUMBERSLABEL','');
//define('RECORDEDBYLABEL','');
//define('RECORDNUMBERLABEL','');
//define('EVENTDATELABEL','');
//define('ASSOCIATEDCOLLECTORSLABEL','');
//define('VERBATIMEVENTDATELABEL','');
//define('YYYYMMDDLABEL','');
//define('DAYOFYEARLABEL','');
//define('ENDDATELABEL','');
//define('EXSICCATITITLELABEL','');
//define('EXSICCATINUMBERLABEL','');
//define('SCIENTIFICNAMELABEL','');
//define('SCIENTIFICNAMEAUTHORSHIPLABEL','');
//define('IDCONFIDENCELABEL','');
//define('IDENTIFICATIONQUALIFIERLABEL','');
//define('FAMILYLABEL','');
//define('IDENTIFIEDBYLABEL','');
//define('DATEIDENTIFIEDLABEL','');
//define('IDENTIFICATIONREFERENCELABEL','');
//define('IDENTIFICATIONREMARKSLABEL','');
//define('TAXONREMARKSLABEL','');
//define('COUNTRYLABEL','');
//define('STATEPROVINCELABEL','');
//define('COUNTYLABEL','');
//define('MUNICIPALITYLABEL','');
//define('LOCATIONIDLABEL','');
//define('LOCALITYLABEL','');
//define('LOCATIONREMARKSLABEL','');
//define('LOCALITYSECURITYLABEL','');
//define('LOCALITYSECURITYREASONLABEL','');
//define('DECIMALLATITUDELABEL','');
//define('DECIMALLONGITUDELABEL','');
//define('COORDINATEUNCERTAINITYINMETERSLABEL','');
//define('GEODETICDATUMLABEL','');
//define('VERBATIMCOORDINATESLABEL','');
//define('ELEVATIONINMETERSLABEL','');
//define('VERBATIMELEVATIONLABEL','');
//define('DEPTHINMETERSLABEL','');
//define('VERBATIMDEPTHLABEL','');
//define('GEOREFERENCEBYLABEL','');
//define('GEOREFERENCESOURCESLABEL','');
//define('GEOREFERENCEREMARKSLABEL','');
//define('GEOREFERENCEPROTOCOLLABEL','');
//define('GEOREFERENCEVERIFICATIONSTATUSLABEL','');
//define('FOOTPRINTWKTLABEL','');
//define('HABITATLABEL','');
//define('SUBSTRATELABEL','');
//define('HOSTLABEL','');
//define('ASSOCIATEDTAXALABEL','');
//define('VERBATIMATTRIBUTESLABEL','');
//define('OCCURRENCEREMARKSLABEL','');
//define('DYNAMICPROPERTIESLABEL','');
//define('LIFESTAGELABEL','');
//define('SEXLABEL','');
//define('INDIVIDUALCOUNTLABEL','');
//define('SAMPLINGPROTOCOLLABEL','');
//define('PREPARATIONSLABEL','');
//define('REPRODUCTIVECONDITIONLABEL','');
//define('ESTABLISHMENTMEANSLABEL','');
//define('CULTIVATIONSTATUSLABEL','');
//define('TYPESTATUSLABEL','');
//define('DISPOSITIONLABEL','');
//define('OCCURRENCEIDLABEL','');
//define('FIELDNUMBERLABEL','');
//define('BASISOFRECORDLABEL','');
//define('LANGUAGELABEL','');
//define('LABELPROJECTLABEL','');
//define('DUPLICATEQUANTITYCOUNTLABEL','');
//define('INSTITUTIONCODELABEL','');
//define('COLLECTIONCODELABEL','');
//define('OWNERINSTITUTIONCODELABEL','');
//define('PROCESSINGSTATUSLABEL','');
//define('DATAGENERALIZATIONSLABEL','');
//define('OCRWHOLEIMAGELABEL','');
//define('OCRANALYSISLABEL','');


// Field Tooltip text: uncomment variables and add a value to modify field tooltips that popup on hover

// Collection and Collector info
//define('CATALOGNUMBERTIP','');
//define('OTHERCATALOGNUMBERSTIP','');
//define('RECORDEDBYTIP','');
//define('RECORDNUMBERTIP','');
//define('EVENTDATETIP','');
//define('DUPLICATESTIP','');
//define('ASSOCIATEDCOLLECTORSTIP','');
//define('VERBATIMEVENTDATETIP','');
//define('YYYYMMDDTIP','');
//define('NUMERICYEARTIP','');
//define('NUMERICMONTHTIP','');
//define('NUMERICDAYTIP','');
//define('DAYOFYEARTIP','');
//define('STARTDAYOFYEARTIP','');
//define('ENDDAYOFYEARTIP','');
//define('ENDDATETIP','');

// Exsiccati
//define('EXSICCATITITLETIP','');
//define('EXSICCATINUMBERTIP','');

// Latest Identification
//define('SCIENTIFICNAMETIP','');
//define('SCIENTIFICNAMEAUTHORSHIPTIP','');
//define('IDCONFIDENCETIP','');
//define('IDENTIFICATIONQUALIFIERTIP','');
//define('FAMILYTIP','');
//define('IDENTIFIEDBYTIP','');
//define('DATEIDENTIFIEDTIP','');
//define('IDENTIFICATIONREFERENCETIP','');
//define('IDENTIFICATIONREMARKSTIP','');
//define('TAXONREMARKSTIP','');

// Locality & Georeferencing
//define('COUNTRYTIP','');
//define('STATEPROVINCETIP','');
//define('COUNTYTIP','');
//define('MUNICIPALITYTIP','');
//define('LOCATIONIDTIP','');
//define('LOCALITYTIP','');
//define('LOCATIONREMARKSTIP','');
//define('LOCALITYAUTOLOOKUP','');
//define('LOCALITYSECURITYTIP','');
//define('LOCALITYSECURITYREASONTIP','');
//define('DECIMALLATITUDETIP','');
//define('DECIMALLONGITUDETIP','');
//define('COORDINATEUNCERTAINITYINMETERSTIP','');
//define('GOOGLEMAPSTIP','');
//define('GEOLOCATETIP','');
//define('COORDCLONETIP','');
//define('GEOTOOLSTIP','');
//define('GEODETICDATUMTIP','');
//define('VERBATIMCOORDINATESTIP','');
//define('RECALCULATECOORDSTIP','');
//define('ELEVATIONINMETERSTIP','');
//define('MINELEVATIONINMETERSTIP','');
//define('MAXELEVATIONINMETERSTIP','');
//define('RECALCULATEELEVTIP','');
//define('VERBATIMELEVATIONTIP','');
//define('DEPTHINMETERSTIP','');
//define('MINDEPTHINMETERSTIP','');
//define('MAXDEPTHINMETERSTIP','');
//define('VERBATIMDEPTHTIP','');
//define('GEOREFERENCEBYTIP','');
//define('GEOREFERENCESOURCESTIP','');
//define('GEOREFERENCEREMARKSTIP','');
//define('GEOREFERENCEPROTOCOLTIP','');
//define('GEOREFERENCEVERIFICATIONSTATUSTIP','');
//define('GOOGLEMAPSPOLYGONTIP','');
//define('FOOTPRINTWKTTIP','');

// Misc
//define('HABITATTIP','');
//define('SUBSTRATETIP','');
//define('HOSTTIP','');
//define('ASSOCIATEDTAXATIP','');
//define('ASSOCIATEDTAXAAIDTIP','');
//define('VERBATIMATTRIBUTESTIP','');
//define('OCCURRENCEREMARKSTIP','');
//define('DYNAMICPROPERTIESTIP','');
//define('LIFESTAGETIP','');
//define('SEXTIP','');
//define('INDIVIDUALCOUNTTIP','');
//define('SAMPLINGPROTOCOLTIP','');
//define('PREPARATIONSTIP','');
//define('REPRODUCTIVECONDITIONTIP','');
//define('ESTABLISHMENTMEANSTIP','');
//define('CULTIVATIONSTATUSTIP','');

// Curation
//define('TYPESTATUSTIP','');
//define('DISPOSITIONTIP','');
//define('OCCURRENCEIDTIP','');
//define('FIELDNUMBERTIP','');
//define('BASISOFRECORDTIP','');
//define('LANGUAGETIP','');
//define('LABELPROJECTTIP','');
//define('DUPLICATEQUANTITYCOUNTTIP','');
//define('INSTITUTIONCODETIP','');
//define('COLLECTIONCODETIP','');
//define('OWNERINSTITUTIONCODETIP','');
//define('PROCESSINGSTATUSTIP','');
//define('DATAGENERALIZATIONSTIP','');
//define('STATUSAUTOSETTIP','');

// Record Cloning
//define('CARRYOVERTIP','');
//define('RELATIONSHIPTIP','');
//define('TARGETCOLLECTIONTIP','');
//define('NUMBERRECORDSTIP','');
//define('PREPOPULATETIP','');
//define('CLONECATALOGNUMBERTIP','');

// Determinations
//define('MAKECURRENTDETERMINATIONTIP','');
//define('ANNOTATIONPRINTQUEUETIP','');
//define('SORTSEQUENCETIP','');

// OCR
//define('OCRWHOLEIMAGETIP','');
//define('OCRANALYSISTIP','');

// Batch Determinations
//define('DETERMINATIONTAXONTIP','');
//define('ANNOTATIONTYPETIP','');

// Occurrence Image Submission
//define('OCRTEXTTIP','');

?>
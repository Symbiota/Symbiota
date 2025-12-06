<?php global $LANG;
/*
------------------
Language: English
------------------
*/
$LANG['BATCH_DUPLICATE_HARVESTER'] = 'Duplicate Data Harvester';
$LANG['MUST_BATCH_LINK_DUPLICATES'] = 'For this tool to find duplicate georeferences, your specimens must already be linked as duplicates 
                to other specimens in the portal. It is recommended to run the "Batch link specimen duplicates" process in the Duplicate Clustering Tools 
				before using the georeference harvester.';
$LANG['DUPLICATE_SEARCH_CRITERIA'] = 'Duplicate Search Criteria';
$LANG['MISSING_LAT_LNG'] = 'Only show specimens from my collection without latitude and longitude';
$LANG['HIDE_EXACT_MATCHES'] = 'Only show duplicates with georeferences different than target specimen';
$LANG['NO_DUPLICATES'] = 'There are no duplicate clusters that match this search';
$LANG['FILTER_COLLECTIONS'] = 'Filter Collections';
$LANG['COPY_DUPLICATE_DATA'] = 'Copy Duplicate Data';
$LANG['COPY_DUPLICATE_DATA_EXPLANATION'] = 'Clicking the button above will replace the georeference data in the target (dark grey) record with the data from the checked duplicate record. The following fields will be replaced: decimalLatitude, decimalLongitude, geodeticDatum, footprintWKT, coordinateUncertaintyInMeters, georeferencedBy, georeferenceRemarks, georeferenceSources, georeferenceProtocol, georeferenceVerificationStatus';
$LANG['ENABLE_AUTO_CHECK'] = 'Auto-select duplicates with only one option (note: duplicates within your collection will not be auto-selected)';
$LANG['SEARCH_TO_SEE_DUPLICATES'] = 'Enter search for values to see potential duplicates';
?>

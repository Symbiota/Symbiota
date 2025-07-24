<?php
/*
------------------
Language: English
------------------
*/
$LANG['HOME'] = 'Home';
$LANG['COLLECTION_MANAGEMENT'] = 'Collection Management';

$LANG['COOR_VALIDATOR'] = 'Coordinate Validator';
$LANG['RECOMMEND_USE_GEOGRAPHIC_CLEANER'] = '<b>*Note*</b>: It is recommended to use the Geography Cleaning Tools before validating coordinates. 
    This will ensure that your political units match those in the geographic thesaurus.';
$LANG['TOOL_DESCRIPTION'] = 'Clicking the "Validate All Coordinates" button will loop through all unvalidated georeferenced records to verify that 
    the coordinates actually fall within the defined geographic units, as defined by geographic polygons stored in the geographic thesaurus. 
    Click on the number in the Questionable Records column (available after validating) to view records with the named issue. For more information about this tool, 
    visit <a href="https://docs.symbiota.org/Collection_Manager_Guide/Data_Cleaning/coordinate_validator/" target="_blank">Symbiota Docs</a>.';
$LANG['COORDINATES_OUTSIDE_COUNTY_LIMITS'] = "Coordinates fall outside of geographic unit's limits";
$LANG['WRONG_COUNTY_ENTERED'] = 'Wrong geographic unit was entered';
$LANG['COUNTY_MISSPELLED'] = 'Geographic unit is misspelled';
$LANG['VALIDATION_COUNT_LIMIT'] = 'Coordinate validation is limited to 50000 records at a time, but can be run multiple times.';
$LANG['LAST_VER_DATE'] = 'Last Verification Date';
$LANG['RECORDS_TOOK'] = 'records took';
$LANG['SEC'] = 'seconds';
$LANG['SPEC_RANK_OF'] = 'Record with rank of';
$LANG['CHECKED_BY'] = 'checked by';
 $LANG['NOTHING_TO_DISPLAY'] = 'Nothing to be displayed';
$LANG['POPULATE_COUNTRY'] = 'Populate country if missing and can be inferred from coordinates';
$LANG['POPULATE_STATE_PROVINCE'] = 'Populate state/province if missing and can be inferred from coordinates';
$LANG['POPULATE_COUNTY'] = 'Populate county if missing and can be inferred from coordinates';
$LANG['RE-VALIDATE_ALL_COORDINATES'] = 'Re-Validate All Coordinates';
$LANG['VALIDATE_ALL_COORDINATES'] = 'Validate All Coordinates';
 $LANG['UNVERIFIED_RECORDS'] = 'unverified records';
$LANG['RANKING_STATISTICS'] = 'Ranking Statistics';

$LANG['RANKING'] = 'Ranking';
$LANG['STATUS'] = 'Status';
$LANG['COUNT'] = 'Count';
$LANG['RE-VERIFY'] = 'Re-Verify';
$LANG['UNVERIFIED'] = 'unverified';

$LANG['UNVERIFIED_BY_COUNTRY'] = 'Unverified records listed by country';
$LANG['COUNTRY'] = 'Country';
$LANG['VIEW_SPECIMENS'] = 'View Specimens';
$LANG['NOT_AUTHORIZED'] = 'You are not authorized to access this page';

$LANG['COUNTRY_DOES_NOT_MATCH_COORDS'] = 'Country does not match coordinates';
$LANG['STATE_PROVINCE_DOES_NOT_MATCH_COORDS'] = 'State/Province does not match coordinates';
$LANG['COUNTY_DOES_NOT_MATCH_COORDS'] = 'County does not match coordinates';
$LANG['UNVERIFIABLE_NO_POLYGON'] = 'Failed to validate coordinate based on geographic thesaurus';
$LANG['HAS_POLYGON_FAILED_TO_VERIFY'] = 'Failed to validate coordinate despite known search polygon';
$LANG['NOT_AUTHORIZED'] = 'You are not authorized to access this page';
$LANG['INVALID_RANK'] = 'Invalid coordinate validation ranking';

$LANG['COUNTRY_POPULATED'] = 'Country values populated';
$LANG['STATE_PROVINCE_POPULATED'] = 'State/Province values populated';
$LANG['COUNTY_POPULATED'] = 'County values populated';
?>

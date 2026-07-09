<?php
/*
 * * ****  Accepts  ********************************************
 *
 * POST requests
 *
 * ****  Input Variables  ********************************************
 *
 * un (optional): Username for user.
 * uid (optional): User ID for user.
 * token (optional): Access token for user.
 * collid: Collection ID for occurrence record.
 * occid (optional): occid of occurrence record.
 * dbpk (optional): dbpk of occurrence record.
 * catnum (optional): Catalog number or other catalog number of occurrence record.
 *
 * * ****  Output  ********************************************
 *
 * JSON array of occids (occurrence record ids - hopefully this will only be one, but could be more).
 *
 * Each occid contains a subarray with:
 *  collid: Collection ID of record.
 *  dbpk: dbpk of record.
 *  institutioncode: Institution code of record.
 *  collectioncode: Collection code of record.
 *  catalogNumber: DwC catalogNumber.
 *  otherCatalogNumbers: DwC otherCatalogNumbers.
 *  family: DwC family.
 *  sciname: DwC sciname.
 *  tidinterpreted: Accepted taxon ID for scientific name.
 *  scientificNameAuthorship: DwC scientificNameAuthorship.
 *  recordedBy: DwC recordedBy.
 *  country: DwC country.
 *  stateProvince: DwC stateProvince.
 *  county: DwC county.
 *  observeruid: UID of observer.
 *  locality: Either DwC locality ot indication/explanation of why locality is obscurred for t/e taxa and users who don't have rights.
 ****** Additional fields for non-t/e taxa or users with rights **************
 *  decimallatitude: DwC decimallatitude.
 *  decimallongitude: DwC decimallongitude.
 *  recordNumber: DwC recordNumber.
 *  eventDate: DwC eventDate.
 *  minimumElevationInMeters: DwC minimumElevationInMeters.
 *  maximumElevationInMeters: DwC maximumElevationInMeters.
 *
 */

include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAPIManager.php');

$un = array_key_exists('un',$_POST)?$_POST['un']:'';
$uId = array_key_exists('uid',$_POST)?$_POST['uid']:'';
$token = array_key_exists('token',$_POST)?$_POST['token']:'';
$collid = array_key_exists('collid',$_POST)?$_POST['collid']:0;
$occid = array_key_exists('occid',$_POST)?$_POST['occid']:0;
$dbpk = array_key_exists('dbpk',$_POST)?$_POST['dbpk']:'';
$catnum = array_key_exists('catnum',$_POST)?$_POST['catnum']:'';

$pHandler = new ProfileManager();
$qHandler = new OccurrenceAPIManager();
$occArr = Array();

if(!$un && $uId){
    $un = $pHandler->getUserName($uId);
}

if($un && $token){
    if($pHandler->setUserName($un)){
        $pHandler->setToken($token);
        $pHandler->authenticate();
    }
}

if($collid){
    $qHandler->setCollID($collid);
    $qHandler->setOccID($occid);
    $qHandler->setDBPK($dbpk);
    $qHandler->setCatNum($catnum);

    $qHandler->setOccLookupSQLWhere();
    $occArr = $qHandler->getOccLookupArr();
    echo json_encode($occArr);
}
?>
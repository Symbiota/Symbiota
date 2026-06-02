<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT.'/classes/OccurrenceUtilities.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAccessStats.php');

class EthnoSearchManager {

    private $conn;
    protected $searchTermsArr = Array();
    protected $reset = 0;
    private $clName;
    protected $recordCount = 0;
    protected $sortField1 = '';
    protected $sortField2 = '';
    protected $sortOrder = '';
    protected $localSearchArr = Array();
    protected $taxaArr = Array();
    private $taxaSearchType;
    private $checklistTaxaCnt = 0;
    private $fieldArr = Array();

    public function __construct($readVariables = true){
        $this->conn = MySQLiConnectionFactory::getCon('readonly');
        if(array_key_exists("reset",$_REQUEST) && $_REQUEST["reset"])  $this->reset();
        if($readVariables) {
            $this->readRequestVariables();
        }
    }

    private function readRequestVariables(){
        if(array_key_exists("db",$_REQUEST)){
            $dbStr = '';
            $dbs = $_REQUEST["db"];
            if(is_string($dbs)){
                if(is_numeric($dbs) || $dbs === 'allspec' || $dbs === 'allobs' || $dbs === 'all'){
                    $dbStr = $this->cleanInputStr($dbs).';';
                }
            }
            else{
                $dbStr = $this->cleanInputStr(implode(',',array_unique($dbs))).';';
            }
            if(!preg_match('/^[0-9,;]+$/', $dbStr)) $dbStr = 'all';
            if(strpos($dbStr,'allspec') !== false){
                $dbStr = 'allspec';
            }
            elseif(strpos($dbStr,'allobs') !== false){
                $dbStr = 'allobs';
            }
            elseif(strpos($dbStr,'all') !== false){
                $dbStr = 'all';
            }
            if(strpos($dbStr, 'all') !== 0 && array_key_exists('cat',$_REQUEST)){
                $catid = $_REQUEST['cat'];
                if(is_string($catid)){
                    $catArr = Array($catid);
                }
                else{
                    $catArr = $catid;
                }
                if(!$dbStr) $dbStr = ';';
                $dbStr .= $this->cleanInputStr(implode(",",$catArr));
            }

            if($dbStr){
                $this->searchTermsArr["db"] = $dbStr;
            }
        }
        if(array_key_exists('taxa',$_REQUEST)){
            $taxa = $this->cleanInputStr($_REQUEST["taxa"]);
            $searchType = ((array_key_exists("type",$_REQUEST) && is_numeric($_REQUEST["type"]))?$_REQUEST["type"]:1);
            if($taxa){
                $taxaStr = "";
                if(is_numeric($taxa)){
                    $sql = "SELECT t.sciname ".
                        "FROM taxa t ".
                        "WHERE (t.tid = ".$taxa.')';
                    $rs = $this->conn->query($sql);
                    while($row = $rs->fetch_object()){
                        $taxaStr = $row->sciname;
                    }
                    $rs->free();
                }
                else{
                    $taxaStr = str_replace(",",";",$taxa);
                    $taxaArr = explode(";",$taxaStr);
                    foreach($taxaArr as $key => $sciName){
                        $snStr = trim($sciName);
                        if($searchType != 5) $snStr = ucfirst($snStr);
                        $taxaArr[$key] = $snStr;
                    }
                    $taxaStr = implode(";",$taxaArr);
                }
                $this->searchTermsArr["taxa"] = $taxaStr;
                $useThes = ((array_key_exists("thes",$_REQUEST) && is_numeric($_REQUEST["thes"]))?$_REQUEST["thes"]:0);
                if($useThes){
                    $this->searchTermsArr["usethes"] = true;
                }
                else{
                    $this->searchTermsArr["usethes"] = false;
                }
                if($searchType){
                    $this->searchTermsArr["taxontype"] = $searchType;
                }
            }
            else{
                unset($this->searchTermsArr["taxa"]);
            }
        }
        if(array_key_exists("country",$_REQUEST)){
            $country = $this->cleanInputStr($_REQUEST["country"]);
            if($country){
                $str = str_replace(",",";",$country);
                if(stripos($str, "USA") !== false || stripos($str, "United States") !== false || stripos($str, "U.S.A.") !== false || stripos($str, "United States of America") !== false){
                    if(stripos($str, "USA") === false){
                        $str .= ";USA";
                    }
                    if(stripos($str, "United States") === false){
                        $str .= ";United States";
                    }
                    if(stripos($str, "U.S.A.") === false){
                        $str .= ";U.S.A.";
                    }
                    if(stripos($str, "United States of America") === false){
                        $str .= ";United States of America";
                    }
                }
                $this->searchTermsArr["country"] = $str;
            }
            else{
                unset($this->searchTermsArr["country"]);
            }
        }
        if(array_key_exists("state",$_REQUEST)){
            $state = $this->cleanInputStr($_REQUEST["state"]);
            if($state){
                if(strlen($state) == 2 && (!isset($this->searchTermsArr["country"]) || stripos($this->searchTermsArr["country"],'USA') !== false)){
                    $sql = 'SELECT s.statename, c.countryname '.
                        'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
                        'WHERE c.countryname IN("USA","United States") AND (s.abbrev = "'.$state.'")';
                    $rs = $this->conn->query($sql);
                    if($r = $rs->fetch_object()){
                        $state = $r->statename;
                    }
                    $rs->free();
                }
                $str = str_replace(",",";",$state);
                $this->searchTermsArr["state"] = $str;
            }
            else{
                unset($this->searchTermsArr["state"]);
            }
        }
        if(array_key_exists("county",$_REQUEST)){
            $county = $this->cleanInputStr($_REQUEST["county"]);
            $county = str_ireplace(" Co.","",$county);
            $county = str_ireplace(" County","",$county);
            if($county){
                $str = str_replace(",",";",$county);
                $this->searchTermsArr["county"] = $str;
            }
            else{
                unset($this->searchTermsArr["county"]);
            }
        }
        if(array_key_exists("local",$_REQUEST)){
            $local = $this->cleanInputStr($_REQUEST["local"]);
            if($local){
                $str = str_replace(",",";",$local);
                $this->searchTermsArr["local"] = $str;
            }
            else{
                unset($this->searchTermsArr["local"]);
            }
        }
        if(array_key_exists("elevlow",$_REQUEST)){
            if(is_numeric($_REQUEST["elevlow"])){
                $elevlow = $_REQUEST["elevlow"];
                if($elevlow){
                    $str = str_replace(",",";",$elevlow);
                    $this->searchTermsArr["elevlow"] = $str;
                }
                else{
                    unset($this->searchTermsArr["elevlow"]);
                }
            }
        }
        if(array_key_exists("elevhigh",$_REQUEST)){
            if(is_numeric($_REQUEST["elevhigh"])){
                $elevhigh = $_REQUEST["elevhigh"];
                if($elevhigh){
                    $str = str_replace(",",";",$elevhigh);
                    $this->searchTermsArr["elevhigh"] = $str;
                }
                else{
                    unset($this->searchTermsArr["elevhigh"]);
                }
            }
        }
        if(array_key_exists("assochost",$_REQUEST)){
            $assocHost = $this->cleanInputStr($_REQUEST["assochost"]);
            if($assocHost){
                $str = str_replace(",",";",$assocHost);
                $this->searchTermsArr["assochost"] = $str;
            }
            else{
                unset($this->searchTermsArr["assochost"]);
            }
        }
        if(array_key_exists("collector",$_REQUEST)){
            $collector = $this->cleanInputStr($_REQUEST["collector"]);
            if($collector){
                $str = str_replace(",",";",$collector);
                $this->searchTermsArr["collector"] = $str;
            }
            else{
                unset($this->searchTermsArr["collector"]);
            }
        }
        if(array_key_exists("collnum",$_REQUEST)){
            $collNum = $this->cleanInputStr($_REQUEST["collnum"]);
            if($collNum){
                $str = str_replace(",",";",$collNum);
                $this->searchTermsArr["collnum"] = $str;
            }
            else{
                unset($this->searchTermsArr["collnum"]);
            }
        }
        if(array_key_exists("eventdate1",$_REQUEST)){
            if($eventDate = $this->cleanInputStr($_REQUEST["eventdate1"])){
                $this->searchTermsArr["eventdate1"] = $eventDate;
                if(array_key_exists("eventdate2",$_REQUEST)){
                    if($eventDate2 = $this->cleanInputStr($_REQUEST["eventdate2"])){
                        if($eventDate2 != $eventDate){
                            $this->searchTermsArr["eventdate2"] = $eventDate2;
                        }
                    }
                    else{
                        unset($this->searchTermsArr["eventdate2"]);
                    }
                }
            }
            else{
                unset($this->searchTermsArr["eventdate1"]);
            }
        }
        if(array_key_exists("catnum",$_REQUEST)){
            $catNum = $this->cleanInputStr($_REQUEST["catnum"]);
            if($catNum){
                $str = str_replace(",",";",$catNum);
                $this->searchTermsArr["catnum"] = $str;
                if(array_key_exists("includeothercatnum",$_REQUEST)){
                    $this->searchTermsArr["othercatnum"] = '1';
                }
            }
            else{
                unset($this->searchTermsArr["catnum"]);
            }
        }
        if(array_key_exists("typestatus",$_REQUEST)){
            $typestatus = $_REQUEST["typestatus"];
            if($typestatus){
                $this->searchTermsArr["typestatus"] = true;
            }
            else{
                unset($this->searchTermsArr["typestatus"]);
            }
        }
        if(array_key_exists("hasimages",$_REQUEST)){
            $hasimages = $_REQUEST["hasimages"];
            if($hasimages){
                $this->searchTermsArr["hasimages"] = true;
            }
            else{
                unset($this->searchTermsArr["hasimages"]);
            }
        }
        if(array_key_exists("hasgenetic",$_REQUEST)){
            $hasgenetic = $_REQUEST["hasgenetic"];
            if($hasgenetic){
                $this->searchTermsArr["hasgenetic"] = true;
            }
            else{
                unset($this->searchTermsArr["hasgenetic"]);
            }
        }
        if(array_key_exists("hasethno",$_REQUEST)){
            $hasethno = $_REQUEST["hasethno"];
            if($hasethno){
                $this->searchTermsArr["hasethno"] = true;
            }
            else{
                unset($this->searchTermsArr["hasethno"]);
            }
        }
        if(array_key_exists("hasmultimedia",$_REQUEST)){
            $hasmultimedia = $_REQUEST["hasmultimedia"];
            if($hasmultimedia){
                $this->searchTermsArr["hasmultimedia"] = true;
            }
            else{
                unset($this->searchTermsArr["hasmultimedia"]);
            }
        }
        if(array_key_exists("targetclid",$_REQUEST) && is_numeric($_REQUEST['targetclid'])){
            $this->searchTermsArr["targetclid"] = $_REQUEST["targetclid"];
        }
        $latLongArr = Array();
        if(array_key_exists("upperlat",$_REQUEST)){
            if(is_numeric($_REQUEST["upperlat"]) && is_numeric($_REQUEST["bottomlat"]) && is_numeric($_REQUEST["leftlong"]) && is_numeric($_REQUEST["rightlong"])){
                $upperLat = $_REQUEST["upperlat"];
                if($upperLat || $upperLat === "0") $latLongArr[] = $upperLat;

                $bottomlat = $_REQUEST["bottomlat"];
                if($bottomlat || $bottomlat === "0") $latLongArr[] = $bottomlat;

                $leftLong = $_REQUEST["leftlong"];
                if($leftLong || $leftLong === "0") $latLongArr[] = $leftLong;

                $rightlong = $_REQUEST["rightlong"];
                if($rightlong || $rightlong === "0") $latLongArr[] = $rightlong;

                if(count($latLongArr) == 4){
                    $this->searchTermsArr["llbound"] = implode(";",$latLongArr);
                }
                else{
                    unset($this->searchTermsArr["llbound"]);
                }
            }
        }
        if(array_key_exists("pointlat",$_REQUEST)){
            if(is_numeric($_REQUEST["pointlat"]) && is_numeric($_REQUEST["pointlong"]) && is_numeric($_REQUEST["radius"])){
                $pointLat = $_REQUEST["pointlat"];
                if($pointLat || $pointLat === "0") $latLongArr[] = $pointLat;

                $pointLong = $_REQUEST["pointlong"];
                if($pointLong || $pointLong === "0") $latLongArr[] = $pointLong;

                $radius = $_REQUEST["radius"];
                if($radius) $latLongArr[] = $radius;
                if(count($latLongArr) == 3){
                    $this->searchTermsArr["llpoint"] = implode(";",$latLongArr);
                }
                else{
                    unset($this->searchTermsArr["llpoint"]);
                }
            }
        }
        if(array_key_exists('semantics',$_REQUEST)){
            $semanticsIn = $_REQUEST['semantics'];
            if($semanticsIn){
                $semanticsStr = $this->cleanInputStr(implode(',',array_unique($semanticsIn)));
                if($semanticsStr){
                    $this->searchTermsArr["semantics"] = $semanticsStr;
                }
            }
            else{
                unset($this->searchTermsArr["semantics"]);
            }
        }
        if(array_key_exists("verbatimVernacularName",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["verbatimVernacularName"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["verbatimVernacularName"] = $str;
            }
            else{
                unset($this->searchTermsArr["verbatimVernacularName"]);
            }
        }
        if(array_key_exists("annotatedVernacularName",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["annotatedVernacularName"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["annotatedVernacularName"] = $str;
            }
            else{
                unset($this->searchTermsArr["annotatedVernacularName"]);
            }
        }
        if(array_key_exists("verbatimLanguage",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["verbatimLanguage"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["verbatimLanguage"] = $str;
            }
            else{
                unset($this->searchTermsArr["verbatimLanguage"]);
            }
        }
        if(array_key_exists("languageid",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["languageid"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["languageid"] = $str;
            }
            else{
                unset($this->searchTermsArr["languageid"]);
            }
        }
        if(array_key_exists("otherVerbatimVernacularName",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["otherVerbatimVernacularName"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["otherVerbatimVernacularName"] = $str;
            }
            else{
                unset($this->searchTermsArr["otherVerbatimVernacularName"]);
            }
        }
        if(array_key_exists("otherLangId",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["otherLangId"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["otherLangId"] = $str;
            }
            else{
                unset($this->searchTermsArr["otherLangId"]);
            }
        }
        if(array_key_exists("verbatimParse",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["verbatimParse"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["verbatimParse"] = $str;
            }
            else{
                unset($this->searchTermsArr["verbatimParse"]);
            }
        }
        if(array_key_exists("annotatedParse",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["annotatedParse"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["annotatedParse"] = $str;
            }
            else{
                unset($this->searchTermsArr["annotatedParse"]);
            }
        }
        if(array_key_exists("verbatimGloss",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["verbatimGloss"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["verbatimGloss"] = $str;
            }
            else{
                unset($this->searchTermsArr["verbatimGloss"]);
            }
        }
        if(array_key_exists("annotatedGloss",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["annotatedGloss"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["annotatedGloss"] = $str;
            }
            else{
                unset($this->searchTermsArr["annotatedGloss"]);
            }
        }
        if(array_key_exists("freetranslation",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["freetranslation"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["freetranslation"] = $str;
            }
            else{
                unset($this->searchTermsArr["freetranslation"]);
            }
        }
        if(array_key_exists("taxonomicDescription",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["taxonomicDescription"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["taxonomicDescription"] = $str;
            }
            else{
                unset($this->searchTermsArr["taxonomicDescription"]);
            }
        }
        if(array_key_exists("typology",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["typology"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["typology"] = $str;
            }
            else{
                unset($this->searchTermsArr["typology"]);
            }
        }
        if(array_key_exists('parts',$_REQUEST)){
            $partsIn = $_REQUEST['parts'];
            if($partsIn){
                $partsStr = $this->cleanInputStr(implode(',',array_unique($partsIn)));
                if($partsStr){
                    $this->searchTermsArr["parts"] = $partsStr;
                }
            }
            else{
                unset($this->searchTermsArr["parts"]);
            }
        }
        if(array_key_exists('uses',$_REQUEST)){
            $usesIn = $_REQUEST['uses'];
            if($usesIn){
                $usesStr = $this->cleanInputStr(implode(',',array_unique($usesIn)));
                if($usesStr){
                    $this->searchTermsArr["uses"] = $usesStr;
                }
            }
            else{
                unset($this->searchTermsArr["uses"]);
            }
        }
        if(array_key_exists("consultantComments",$_REQUEST)){
            $input = $this->cleanInputStr($_REQUEST["consultantComments"]);
            if($input){
                $str = str_replace(",",";",$input);
                $this->searchTermsArr["consultantComments"] = $str;
            }
            else{
                unset($this->searchTermsArr["consultantComments"]);
            }
        }
    }

    public function getRecordArr($pageRequest,$cntPerPage){
        global $imageDomain;
        $canReadRareSpp = false;
        if($GLOBALS['USER_RIGHTS']){
            if($GLOBALS['IS_ADMIN'] || array_key_exists("CollAdmin", $GLOBALS['USER_RIGHTS']) || array_key_exists("RareSppAdmin", $GLOBALS['USER_RIGHTS']) || array_key_exists("RareSppReadAll", $GLOBALS['USER_RIGHTS'])){
                $canReadRareSpp = true;
            }
        }
        $returnArr = Array();
        $imageSearchArr = Array();
        $sqlWhere = $this->getSqlWhere();
        if(!$this->recordCount || $this->reset){
            $this->setRecordCnt($sqlWhere);
        }
        $sql = 'SELECT DISTINCT o.occid, c.CollID, c.institutioncode, c.collectioncode, c.collectionname, c.icon, '.
            'CONCAT_WS(":",c.institutioncode, c.collectioncode) AS collection, '.
            'IFNULL(o.CatalogNumber,"") AS catalognumber, o.family, o.sciname, o.tidinterpreted, '.
            'CONCAT_WS(" to ",IFNULL(DATE_FORMAT(o.eventDate,"%d %M %Y"),""),DATE_FORMAT(MAKEDATE(o.year,o.endDayOfYear),"%d %M %Y")) AS date, '.
            'IFNULL(o.scientificNameAuthorship,"") AS author, IFNULL(o.recordedBy,"") AS recordedby, IFNULL(o.recordNumber,"") AS recordnumber, '.
            'o.eventDate, IFNULL(o.country,"") AS country, IFNULL(o.StateProvince,"") AS state, IFNULL(o.county,"") AS county, '.
            'CONCAT_WS(", ",o.locality,CONCAT(ROUND(o.decimallatitude,5)," ",ROUND(o.decimallongitude,5))) AS locality, '.
            'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason, IFNULL(o.habitat,"") AS habitat, '.
            'CONCAT_WS("-",o.minimumElevationInMeters, o.maximumElevationInMeters) AS elev, o.observeruid, '.
            'o.individualCount, o.lifeStage, o.sex, c.sortseq ';
        $sql .= (array_key_exists("assochost",$this->searchTermsArr)?', oas.verbatimsciname ':' ');
        $sql .= 'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.$this->setTableJoins($sqlWhere).$sqlWhere;
        if($this->sortField1 || $this->sortField2 || $this->sortOrder){
            $sortFields = array('Collection' => 'collection','Catalog Number' => 'o.CatalogNumber','Family' => 'o.family',
                'Scientific Name' => 'o.sciname','Collector' => 'o.recordedBy','Number' => 'o.recordNumber','Event Date' => 'o.eventDate',
                'Individual Count' => 'o.individualCount','Life Stage' => 'o.lifeStage','Sex' => 'o.sex',
                'Country' => 'o.country','State/Province' => 'o.StateProvince','County' => 'o.county','Elevation' => 'CAST(elev AS UNSIGNED)');
            if($this->sortField1) $this->sortField1 = $sortFields[$this->sortField1];
            if($this->sortField2) $this->sortField2 = $sortFields[$this->sortField2];
            $sql .= "ORDER BY ";
            if (!$canReadRareSpp) {
                $sql .= "LocalitySecurity ASC,";
            }
            $sql .= $this->sortField1.' '.$this->sortOrder.' ';
            if ($this->sortField2) {
                $sql .= ','.$this->sortField2.' '.$this->sortOrder.' ';
            }
        }
        else{
            $sql .= "ORDER BY c.sortseq, c.collectionname ";
        }
        $pageRequest = ($pageRequest - 1)*$cntPerPage;
        $sql .= "LIMIT ".$pageRequest.",".$cntPerPage;
        //echo "<div>Spec sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $occId = $row->occid;
            $returnArr[$occId]["collid"] = $row->CollID;
            $returnArr[$occId]["institutioncode"] = $this->cleanOutStr($row->institutioncode);
            $returnArr[$occId]["collectioncode"] = $this->cleanOutStr($row->collectioncode);
            $returnArr[$occId]["collectionname"] = $this->cleanOutStr($row->collectionname);
            $returnArr[$occId]["collicon"] = $row->icon;
            $returnArr[$occId]["accession"] = $this->cleanOutStr($row->catalognumber);
            $returnArr[$occId]["family"] = $this->cleanOutStr($row->family);
            $returnArr[$occId]["sciname"] = $this->cleanOutStr($row->sciname);
            $returnArr[$occId]["tid"] = $row->tidinterpreted;
            $returnArr[$occId]["author"] = $this->cleanOutStr($row->author);
            $returnArr[$occId]["collector"] = $this->cleanOutStr($row->recordedby);
            $returnArr[$occId]["country"] = $this->cleanOutStr($row->country);
            $returnArr[$occId]["state"] = $this->cleanOutStr($row->state);
            $returnArr[$occId]["county"] = $this->cleanOutStr($row->county);
            if(array_key_exists("assochost",$this->searchTermsArr)) $returnArr[$occId]["assochost"] = $this->cleanOutStr($row->verbatimsciname);
            $returnArr[$occId]["observeruid"] = $row->observeruid;
            $returnArr[$occId]["individualCount"] = $this->cleanOutStr($row->individualCount);
            $returnArr[$occId]["lifeStage"] = $this->cleanOutStr($row->lifeStage);
            $returnArr[$occId]["sex"] = $this->cleanOutStr($row->sex);
            $localitySecurity = $row->LocalitySecurity;
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists("CollEditor", $GLOBALS['USER_RIGHTS']) && in_array($row->CollID,$GLOBALS['USER_RIGHTS']["CollEditor"]))
                || (array_key_exists("RareSppReader", $GLOBALS['USER_RIGHTS']) && in_array($row->CollID,$GLOBALS['USER_RIGHTS']["RareSppReader"]))){
                $returnArr[$occId]["locality"] = $this->cleanOutStr(str_replace('.,',',',$row->locality));
                $returnArr[$occId]["collnumber"] = $this->cleanOutStr($row->recordnumber);
                $returnArr[$occId]["habitat"] = $this->cleanOutStr($row->habitat);
                $returnArr[$occId]["date"] = $row->date;
                $returnArr[$occId]["eventDate"] = $row->eventDate;
                $returnArr[$occId]["elev"] = $row->elev;
                $imageSearchArr[] = $occId;
            }
            else{
                $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                if($row->localitysecurityreason){
                    $securityStr .= $row->localitysecurityreason;
                }
                else{
                    $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                }
                $returnArr[$occId]["locality"] = $securityStr.'</span>';
            }
        }
        $result->free();
        //Set images
        if($imageSearchArr){
            $sql = 'SELECT o.collid, o.occid, i.thumbnailurl '.
                'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
                'WHERE o.occid IN('.implode(',',$imageSearchArr).') '.
                'ORDER BY o.occid, i.sortsequence';
            $rs = $this->conn->query($sql);
            $previousOccid = 0;
            while($r = $rs->fetch_object()){
                if($r->occid != $previousOccid){
                    $tnUrl = $r->thumbnailurl;
                    if($imageDomain){
                        if($tnUrl && substr($tnUrl,0,1)=="/") $tnUrl = $imageDomain.$tnUrl;
                    }
                    $returnArr[$r->occid]['img'] = $tnUrl;
                }
                $previousOccid = $r->occid;
            }
            $rs->free();
        }
        //Set access statistics
        if($returnArr){
            $statsManager = new OccurrenceAccessStats();
            $statsManager->recordAccessEventByArr(array_keys($returnArr),'list');
        }
        return $returnArr;
    }

    protected function setTableJoins($sqlWhere){
        $sqlJoin = '';
        if(array_key_exists("assochost",$this->searchTermsArr)) $sqlJoin .= "INNER JOIN omoccurassociations AS oas ON o.occid = oas.occid ";
        if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
            $sqlJoin .= "INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ";
        }
        $sqlJoin .= "LEFT JOIN ethnodata AS ed ON o.occid = ed.occid ";
        $sqlJoin .= "LEFT JOIN ethnodatanamesemtaglink AS sl ON ed.ethdid = sl.ethdid ";
        $sqlJoin .= "LEFT JOIN ethnodatausepartslink AS pl ON ed.ethdid = pl.ethdid ";
        $sqlJoin .= "LEFT JOIN ethnodatausetaglink AS ul ON ed.ethdid = ul.ethdid ";
        return $sqlJoin;
    }

    public function getSqlWhere(){
        $sqlWhere = "";
        if(array_key_exists("db",$this->searchTermsArr) && $this->searchTermsArr['db']){
            //Do nothing if db = all
            if($this->searchTermsArr['db'] !== 'all'){
                if($this->searchTermsArr['db'] === 'allspec'){
                    $sqlWhere .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype = "Preserved Specimens")) ';
                }
                elseif($this->searchTermsArr['db'] === 'allobs'){
                    $sqlWhere .= 'AND (o.collid IN(SELECT collid FROM omcollections WHERE colltype IN("General Observations","Observations"))) ';
                }
                else{
                    $dbArr = explode(';',$this->searchTermsArr["db"]);
                    $dbStr = '';
                    if(isset($dbArr[0]) && $dbArr[0]){
                        $dbStr = "(o.collid IN(".$this->cleanInStr($dbArr[0]).")) ";
                    }
                    $sqlWhere .= 'AND ('.$dbStr.') ';
                }
            }
        }
        if(array_key_exists("taxa",$this->searchTermsArr)){
            $sqlWhereTaxa = "";
            $useThes = (array_key_exists("usethes",$this->searchTermsArr)?$this->searchTermsArr["usethes"]:0);
            $this->taxaSearchType = $this->searchTermsArr["taxontype"];
            $taxaArr = explode(";",trim($this->searchTermsArr["taxa"]));
            //Set scientific name
            $this->taxaArr = Array();
            foreach($taxaArr as $sName){
                $this->taxaArr[trim($sName)] = Array();
            }
            if($this->taxaSearchType == 5){
                $this->setSciNamesByVerns();
            }
            else{
                if($useThes){
                    $this->setSynonyms();
                }
            }

            //Build sql
            foreach($this->taxaArr as $key => $valueArray){
                if($this->taxaSearchType == 4){
                    //Class, order, or other higher rank
                    $rs1 = $this->conn->query("SELECT ts.tidaccepted FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid WHERE (t.sciname = '".$this->cleanInStr($key)."')");
                    if($r1 = $rs1->fetch_object()){
                        $sqlWhereTaxa = 'OR ((o.sciname = "'.$this->cleanInStr($key).'") OR (o.tidinterpreted IN(SELECT DISTINCT tid FROM taxaenumtree WHERE taxauthid = 1 AND parenttid IN('.$r1->tidaccepted.')))) ';
                    }
                }
                else{
                    if($this->taxaSearchType == 5){
                        $famArr = array();
                        if(array_key_exists("families",$valueArray)){
                            $famArr = $valueArray["families"];
                        }
                        if(array_key_exists("tid",$valueArray)){
                            $tidArr = $valueArray['tid'];
                            $sql = 'SELECT DISTINCT t.sciname '.
                                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                                'WHERE t.rankid = 140 AND e.taxauthid = 1 AND e.parenttid IN('.implode(',',$tidArr).')';
                            $rs = $this->conn->query($sql);
                            while($r = $rs->fetch_object()){
                                $famArr[] = $r->family;
                            }
                        }
                        if($famArr){
                            $famArr = array_unique($famArr);
                            $sqlWhereTaxa .= 'OR (o.family IN("'.$this->cleanInStr(implode('","',$famArr)).'")) ';
                        }
                        if(array_key_exists("scinames",$valueArray)){
                            foreach($valueArray["scinames"] as $sciName){
                                $sqlWhereTaxa .= "OR (o.sciname Like '".$this->cleanInStr($sciName)."%') ";
                            }
                        }
                        //echo $sqlWhereTaxa; exit;
                    }
                    else{
                        if($this->taxaSearchType == 2 || ($this->taxaSearchType == 1 && (strtolower(substr($key,-5)) === "aceae" || strtolower(substr($key,-4)) === "idae"))){
                            $sqlWhereTaxa .= "OR (o.family = '".$this->cleanInStr($key)."') ";
                        }
                        if($this->taxaSearchType == 3 || ($this->taxaSearchType == 1 && strtolower(substr($key,-5)) !== "aceae" && strtolower(substr($key,-4)) !== "idae")){
                            $sqlWhereTaxa .= "OR (o.sciname LIKE '".$this->cleanInStr($key)."%') ";
                        }
                    }
                    if(array_key_exists("synonyms",$valueArray)){
                        $synArr = $valueArray["synonyms"];
                        if($synArr){
                            if($this->taxaSearchType == 1 || $this->taxaSearchType == 2 || $this->taxaSearchType == 5){
                                foreach($synArr as $synTid => $sciName){
                                    if(strpos($sciName,'aceae') || strpos($sciName,'idae')){
                                        $sqlWhereTaxa .= "OR (o.family = '".$this->cleanInStr($sciName)."') ";
                                    }
                                }
                            }
                            $sqlWhereTaxa .= 'OR (o.tidinterpreted IN('.implode(',',array_keys($synArr)).')) ';
                        }
                    }
                }
            }
            $sqlWhere .= "AND (".substr($sqlWhereTaxa,3).") ";
        }
        if(array_key_exists("country",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["country"]);
            $countryArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($countryArr as $k => $value){
                if($value === 'NULL'){
                    $countryArr[$k] = 'Country IS NULL';
                    $tempArr[] = '(o.Country IS NULL)';
                }
                else{
                    $tempArr[] = '(o.Country = "'.$this->cleanInStr($value).'")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$countryArr);
        }
        if(array_key_exists("state",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["state"]);
            $stateAr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($stateAr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(o.StateProvince IS NULL)';
                    $stateAr[$k] = 'State IS NULL';
                }
                else{
                    $tempArr[] = '(o.StateProvince = "'.$this->cleanInStr($value).'")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$stateAr);
        }
        if(array_key_exists("county",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["county"]);
            $countyArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($countyArr as $k => $value){
                if($value == 'NULL'){
                    $tempArr[] = '(o.county IS NULL)';
                    $countyArr[$k] = 'County IS NULL';
                }
                else{
                    $value = trim(str_ireplace(' county',' ',$value));
                    $tempArr[] = '(o.county LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$countyArr);
        }
        if(array_key_exists("local",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["local"]);
            $localArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($localArr as $k => $value){
                $value = trim($value);
                if($value == 'NULL'){
                    $tempArr[] = '(o.locality IS NULL)';
                    $localArr[$k] = 'Locality IS NULL';
                }
                else{
                    if(strlen($value) < 4 || strtolower($value) == 'best'){
                        $tempArr[] = '(o.municipality LIKE "'.$this->cleanInStr($value).'%" OR o.Locality LIKE "%'.$this->cleanInStr($value).'%")';
                    }
                    else{
                        $tempArr[] = '(MATCH(f.locality) AGAINST(\'"'.$this->cleanInStr($value).'"\' IN BOOLEAN MODE)) ';
                    }
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$localArr);
        }
        if((array_key_exists("elevlow",$this->searchTermsArr) && is_numeric($this->searchTermsArr["elevlow"])) || (array_key_exists("elevhigh",$this->searchTermsArr) && is_numeric($this->searchTermsArr["elevhigh"]))){
            $elevlow = 0;
            $elevhigh = 30000;
            if (array_key_exists("elevlow",$this->searchTermsArr))  { $elevlow = $this->searchTermsArr["elevlow"]; }
            if (array_key_exists("elevhigh",$this->searchTermsArr))  { $elevhigh = $this->searchTermsArr["elevhigh"]; }
            $tempArr = Array();
            $sqlWhere .= "AND ( " .
                "	  ( minimumElevationInMeters >= $elevlow AND maximumElevationInMeters <= $elevhigh ) OR " .
                "	  ( maximumElevationInMeters is null AND minimumElevationInMeters >= $elevlow AND minimumElevationInMeters <= $elevhigh ) ".
                "	) ";
        }
        if(array_key_exists("assochost",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["assochost"]);
            $hostAr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($hostAr as $k => $value){
                if($value == 'NULL'){
                    $tempArr[] = '(o.StateProvince IS NULL)';
                    $hostAr[$k] = 'Host IS NULL';
                }
                else{
                    $tempArr[] = '(oas.relationship = "host" AND oas.verbatimsciname LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$hostAr);
        }
        if(array_key_exists("llbound",$this->searchTermsArr)){
            $llboundArr = explode(";",$this->searchTermsArr["llbound"]);
            if(count($llboundArr) == 4){
                $sqlWhere .= "AND (o.DecimalLatitude BETWEEN ".$this->cleanInStr($llboundArr[1])." AND ".$this->cleanInStr($llboundArr[0])." AND ".
                    "o.DecimalLongitude BETWEEN ".$this->cleanInStr($llboundArr[2])." AND ".$this->cleanInStr($llboundArr[3]).") ";
                $this->localSearchArr[] = "Lat: >".$llboundArr[1].", <".$llboundArr[0]."; Long: >".$llboundArr[2].", <".$llboundArr[3];
            }
        }
        if(array_key_exists("llpoint",$this->searchTermsArr)){
            $pointArr = explode(";",$this->searchTermsArr["llpoint"]);
            if(count($pointArr) == 3){
                //Formula approximates a bounding box; bounding box is for efficiency, will test practicality of doing a radius query in future
                $latRadius = $pointArr[2] / 69.1;
                $longRadius = cos($pointArr[0]/57.3)*($pointArr[2]/69.1);
                $lat1 = $pointArr[0] - $latRadius;
                $lat2 = $pointArr[0] + $latRadius;
                $long1 = $pointArr[1] - $longRadius;
                $long2 = $pointArr[1] + $longRadius;
                $sqlWhere .= "AND ((o.DecimalLatitude BETWEEN ".$this->cleanInStr($lat1)." AND ".$this->cleanInStr($lat2).") AND ".
                    "(o.DecimalLongitude BETWEEN ".$this->cleanInStr($long1)." AND ".$this->cleanInStr($long2).")) ";
            }
            $this->localSearchArr[] = "Point radius: ".$pointArr[0].", ".$pointArr[1].", within ".$pointArr[2]." miles";
        }
        if(array_key_exists("collector",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["collector"]);
            $collectorArr = explode(";",$searchStr);
            $tempArr = Array();
            if(count($collectorArr) == 1){
                if($collectorArr[0] == 'NULL'){
                    $tempArr[] = '(o.recordedBy IS NULL)';
                    $collectorArr[] = 'Collector IS NULL';
                }
                else{
                    $tempInnerArr = array();
                    $collValueArr = explode(" ",trim($collectorArr[0]));
                    foreach($collValueArr as $collV){
                        if(strlen($collV) < 4 || strtolower($collV) == 'best'){
                            //Need to avoid FULLTEXT stopwords interfering with return
                            $tempInnerArr[] = '(o.recordedBy LIKE "%'.$this->cleanInStr($collV).'%")';
                        }
                        else{
                            $tempInnerArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($collV).'")) ';
                        }
                    }
                    $tempArr[] = implode(' AND ', $tempInnerArr);
                }
            }
            elseif(count($collectorArr) > 1){
                $collStr = current($collectorArr);
                if(strlen($collStr) < 4 || strtolower($collStr) == 'best'){
                    //Need to avoid FULLTEXT stopwords interfering with return
                    $tempInnerArr[] = '(o.recordedBy LIKE "%'.$this->cleanInStr($collStr).'%")';
                }
                else{
                    $tempArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($collStr).'")) ';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(', ',$collectorArr);
        }
        if(array_key_exists("collnum",$this->searchTermsArr)){
            $collNumArr = explode(";",$this->searchTermsArr["collnum"]);
            $rnWhere = '';
            foreach($collNumArr as $v){
                $v = trim($v);
                if($p = strpos($v,' - ')){
                    $term1 = trim(substr($v,0,$p));
                    $term2 = trim(substr($v,$p+3));
                    if(is_numeric($term1) && is_numeric($term2)){
                        $rnIsNum = true;
                        $rnWhere .= 'OR (o.recordnumber BETWEEN '.$term1.' AND '.$term2.')';
                    }
                    else{
                        if(strlen($term2) > strlen($term1)) $term1 = str_pad($term1,strlen($term2),"0",STR_PAD_LEFT);
                        $catTerm = '(o.recordnumber BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'")';
                        $catTerm .= ' AND (length(o.recordnumber) <= '.strlen($term2).')';
                        $rnWhere .= 'OR ('.$catTerm.')';
                    }
                }
                else{
                    $rnWhere .= 'OR (o.recordNumber = "'.$this->cleanInStr($v).'") ';
                }
            }
            if($rnWhere){
                $sqlWhere .= "AND (".substr($rnWhere,3).") ";
                $this->localSearchArr[] = implode(", ",$collNumArr);
            }
        }
        if(array_key_exists('eventdate1',$this->searchTermsArr)){
            $dateArr = array();
            if(strpos($this->searchTermsArr['eventdate1'],' to ')){
                $dateArr = explode(' to ',$this->searchTermsArr['eventdate1']);
            }
            elseif(strpos($this->searchTermsArr['eventdate1'],' - ')){
                $dateArr = explode(' - ',$this->searchTermsArr['eventdate1']);
            }
            else{
                $dateArr[] = $this->searchTermsArr['eventdate1'];
                if(isset($this->searchTermsArr['eventdate2'])){
                    $dateArr[] = $this->searchTermsArr['eventdate2'];
                }
            }
            if($dateArr[0] == 'NULL'){
                $sqlWhere .= 'AND (o.eventdate IS NULL) ';
                $this->localSearchArr[] = 'Date IS NULL';
            }
            elseif($eDate1 = $this->formatDate($dateArr[0])){
                $eDate2 = (count($dateArr)>1?$this->formatDate($dateArr[1]):'');
                if($eDate2){
                    $sqlWhere .= 'AND (o.eventdate BETWEEN "'.$this->cleanInStr($eDate1).'" AND "'.$this->cleanInStr($eDate2).'") ';
                }
                else{
                    if(substr($eDate1,-5) == '00-00'){
                        $sqlWhere .= 'AND (o.eventdate LIKE "'.$this->cleanInStr(substr($eDate1,0,5)).'%") ';
                    }
                    elseif(substr($eDate1,-2) == '00'){
                        $sqlWhere .= 'AND (o.eventdate LIKE "'.$this->cleanInStr(substr($eDate1,0,8)).'%") ';
                    }
                    else{
                        $sqlWhere .= 'AND (o.eventdate = "'.$this->cleanInStr($eDate1).'") ';
                    }
                }
                $this->localSearchArr[] = $this->searchTermsArr['eventdate1'].(isset($this->searchTermsArr['eventdate2'])?' to '.$this->searchTermsArr['eventdate2']:'');
            }
        }
        if(array_key_exists('catnum',$this->searchTermsArr)){
            $catStr = $this->searchTermsArr['catnum'];
            $includeOtherCatNum = array_key_exists('othercatnum',$this->searchTermsArr)?true:false;

            $catArr = explode(',',str_replace(';',',',$catStr));
            $betweenFrag = array();
            $inFrag = array();
            foreach($catArr as $v){
                if($p = strpos($v,' - ')){
                    $term1 = trim(substr($v,0,$p));
                    $term2 = trim(substr($v,$p+3));
                    if(is_numeric($term1) && is_numeric($term2)){
                        $betweenFrag[] = '(o.catalogNumber BETWEEN '.$this->cleanInStr($term1).' AND '.$this->cleanInStr($term2).')';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(o.othercatalognumbers BETWEEN '.$this->cleanInStr($term1).' AND '.$this->cleanInStr($term2).')';
                        }
                    }
                    else{
                        $catTerm = 'o.catalogNumber BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'"';
                        if(strlen($term1) == strlen($term2)) $catTerm .= ' AND length(o.catalogNumber) = '.$this->cleanInStr(strlen($term2));
                        $betweenFrag[] = '('.$catTerm.')';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(o.othercatalognumbers BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'")';
                        }
                    }
                }
                else{
                    $vStr = trim($v);
                    $inFrag[] = $this->cleanInStr($vStr);
                    if(is_numeric($vStr) && substr($vStr,0,1) == '0'){
                        $inFrag[] = ltrim($vStr,0);
                    }
                }
            }
            $catWhere = '';
            if($betweenFrag){
                $catWhere .= 'OR '.implode(' OR ',$betweenFrag);
            }
            if($inFrag){
                $catWhere .= 'OR (o.catalogNumber IN("'.implode('","',$inFrag).'")) ';
                if($includeOtherCatNum){
                    $catWhere .= 'OR (o.othercatalognumbers IN("'.implode('","',$inFrag).'")) ';
                    if(strlen($inFrag[0]) == 36){
                        $guidOccid = $this->queryRecordID($inFrag);
                        if($guidOccid){
                            $catWhere .= 'OR (o.occid IN('.implode(',',$guidOccid).')) ';
                            $catWhere .= 'OR (o.occurrenceID IN("'.implode('","',$inFrag).'")) ';
                        }
                    }
                }
            }
            $sqlWhere .= 'AND ('.substr($catWhere,3).') ';
            $this->localSearchArr[] = $this->searchTermsArr['catnum'];
        }
        if(array_key_exists("typestatus",$this->searchTermsArr)){
            $sqlWhere .= "AND (o.typestatus IS NOT NULL) ";
            $this->localSearchArr[] = 'is type';
        }
        if(array_key_exists("hasimages",$this->searchTermsArr)){
            $sqlWhere .= "AND (o.occid IN(SELECT occid FROM images)) ";
            $this->localSearchArr[] = 'has images';
        }
        if(array_key_exists("hasgenetic",$this->searchTermsArr)){
            $sqlWhere .= "AND (o.occid IN(SELECT occid FROM omoccurgenetic)) ";
            $this->localSearchArr[] = 'has genetic data';
        }
        if(array_key_exists("hasethno",$this->searchTermsArr)){
            $sqlWhere .= "AND (o.occid IN(SELECT occid FROM ethnodata)) ";
            $this->localSearchArr[] = 'has ethnobiological data';
        }
        if(array_key_exists("hasmultimedia",$this->searchTermsArr)){
            $sqlWhere .= "AND (o.occid IN(SELECT occid FROM ethnomedocclink)) ";
            $this->localSearchArr[] = 'has multimedia files';
        }
        if(array_key_exists("targetclid",$this->searchTermsArr)){
            $clid = $this->searchTermsArr["targetclid"];
            if(is_numeric($clid)){
                $voucherManager = new ChecklistVoucherAdmin($this->conn);
                $voucherManager->setClid($clid);
                $voucherManager->setCollectionVariables();
                $this->clName = $voucherManager->getClName();
                $sqlWhere .= 'AND ('.$voucherManager->getSqlFrag().') '.
                    'AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid = '.$clid.')) ';
                $this->localSearchArr[] = $voucherManager->getQueryVariableStr();
            }
        }
        if(array_key_exists("semantics",$this->searchTermsArr)){
            $valArr = explode(',',$this->searchTermsArr["semantics"]);
            $valStr = '';
            if(isset($valArr[0]) && $valArr[0]){
                $valStr = "(sl.ethTagId IN(".$this->searchTermsArr["semantics"].")) ";
            }
            $sqlWhere .= 'AND ('.$valStr.') ';
        }
        if(array_key_exists("verbatimVernacularName",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["verbatimVernacularName"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.verbatimVernacularName IS NULL)';
                    $valArr[$k] = 'Verbatim Vernacular Name IS NULL';
                }
                else{
                    $tempArr[] = '(ed.verbatimVernacularName LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("annotatedVernacularName",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["annotatedVernacularName"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.annotatedVernacularName IS NULL)';
                    $valArr[$k] = 'Annotated Vernacular Name IS NULL';
                }
                else{
                    $tempArr[] = '(ed.annotatedVernacularName LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("verbatimLanguage",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["verbatimLanguage"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.verbatimLanguage IS NULL)';
                    $valArr[$k] = 'Verbatim Language IS NULL';
                }
                else{
                    $tempArr[] = '(ed.verbatimLanguage LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("languageid",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["languageid"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.langId IS NULL)';
                    $valArr[$k] = 'Language Id IS NULL';
                }
                else{
                    $tempArr[] = '(ed.langId = "'.$this->cleanInStr($value).'")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("otherVerbatimVernacularName",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["otherVerbatimVernacularName"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.otherVerbatimVernacularName IS NULL)';
                    $valArr[$k] = 'Other Verbatim Vernacular Name IS NULL';
                }
                else{
                    $tempArr[] = '(ed.otherVerbatimVernacularName LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("otherLangId",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["otherLangId"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.otherLangId IS NULL)';
                    $valArr[$k] = 'Other Language Id IS NULL';
                }
                else{
                    $tempArr[] = '(ed.otherLangId = "'.$this->cleanInStr($value).'")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("verbatimParse",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["verbatimParse"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.verbatimParse IS NULL)';
                    $valArr[$k] = 'Verbatim Parse IS NULL';
                }
                else{
                    $tempArr[] = '(ed.verbatimParse LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("annotatedParse",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["annotatedParse"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.annotatedParse IS NULL)';
                    $valArr[$k] = 'Annotated Parse IS NULL';
                }
                else{
                    $tempArr[] = '(ed.annotatedParse LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("verbatimGloss",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["verbatimGloss"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.verbatimGloss IS NULL)';
                    $valArr[$k] = 'Verbatim Gloss IS NULL';
                }
                else{
                    $tempArr[] = '(ed.verbatimGloss LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("annotatedGloss",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["annotatedGloss"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.annotatedGloss IS NULL)';
                    $valArr[$k] = 'Annotated Gloss IS NULL';
                }
                else{
                    $tempArr[] = '(ed.annotatedGloss LIKE "'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("freetranslation",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["freetranslation"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.`translation` IS NULL)';
                    $valArr[$k] = 'Free Translation IS NULL';
                }
                else{
                    $tempArr[] = '(ed.`translation` LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("taxonomicDescription",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["taxonomicDescription"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.taxonomicDescription IS NULL)';
                    $valArr[$k] = 'Taxonomic Description IS NULL';
                }
                else{
                    $tempArr[] = '(ed.taxonomicDescription LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("typology",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["typology"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.typology IS NULL)';
                    $valArr[$k] = 'Typology IS NULL';
                }
                else{
                    $tempArr[] = '(ed.typology = "'.$this->cleanInStr($value).'")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        if(array_key_exists("parts",$this->searchTermsArr)){
            $valArr = explode(',',$this->searchTermsArr["parts"]);
            $valStr = '';
            if(isset($valArr[0]) && $valArr[0]){
                $valStr = "(pl.ethpuid IN(".$this->searchTermsArr["parts"].")) ";
            }
            $sqlWhere .= 'AND ('.$valStr.') ';
        }
        if(array_key_exists("uses",$this->searchTermsArr)){
            $valArr = explode(',',$this->searchTermsArr["uses"]);
            $valStr = '';
            if(isset($valArr[0]) && $valArr[0]){
                $valStr = "(ul.ethuoid IN(".$this->searchTermsArr["uses"].")) ";
            }
            $sqlWhere .= 'AND ('.$valStr.') ';
        }
        if(array_key_exists("consultantComments",$this->searchTermsArr)){
            $searchStr = str_replace("%apos;","'",$this->searchTermsArr["consultantComments"]);
            $valArr = explode(";",$searchStr);
            $tempArr = Array();
            foreach($valArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ed.consultantComments IS NULL)';
                    $valArr[$k] = 'Consultant Comments IS NULL';
                }
                else{
                    $tempArr[] = '(ed.consultantComments LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$valArr);
        }
        $retStr = '';
        if($sqlWhere){
            $retStr = 'WHERE '.substr($sqlWhere,4);
        }
        else{
            //Make the sql valid, but return nothing
            $retStr = 'WHERE o.occid IS NULL ';
        }
        //echo $retStr; exit;
        return $retStr;
    }

    public function setRecordCnt($sqlWhere){
        if($sqlWhere){
            $sql = "SELECT COUNT(o.occid) AS cnt FROM omoccurrences o ".$this->setTableJoins($sqlWhere).$sqlWhere;
            //echo "<div>Count sql: ".$sql."</div>";
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $this->recordCount = $row->cnt;
            }
            $result->free();
        }
        setCookie("collvars","reccnt:".$this->recordCount,time()+64800,($GLOBALS['CLIENT_ROOT']?$GLOBALS['CLIENT_ROOT']:'/'));
    }

    public function getRecordCnt(){
        return $this->recordCount;
    }

    public function reset(){
        global $clientRoot;
        $domainName = $_SERVER['HTTP_HOST'];
        if(!$domainName) $domainName = $_SERVER['SERVER_NAME'];
        $this->reset = 1;
        if(isset($this->searchTermsArr['db']) || isset($this->searchTermsArr['oic'])){
            //reset all other search terms except maintain the db terms
            $dbsTemp = "";
            if(isset($this->searchTermsArr['db'])) $dbsTemp = $this->searchTermsArr["db"];
            $clidTemp = "";
            if(isset($this->searchTermsArr['clid'])) $clidTemp = $this->searchTermsArr["clid"];
            unset($this->searchTermsArr);
            if($dbsTemp) $this->searchTermsArr["db"] = $dbsTemp;
            if($clidTemp) $this->searchTermsArr["clid"] = $clidTemp;
        }
    }

    public function getUseTagArrFull(){
        $returnArr = array();
        $sql = 'SELECT h.tid, h.ethuhid, h.headerText, o.ethuoid, o.optionText '.
            'FROM ethnotaxauseheaders AS h LEFT JOIN ethnouseoptions AS o ON h.ethuhid = o.ethuhid '.
            'WHERE h.tid = 4 AND o.optionText NOT LIKE "Other%" AND o.optionText NOT LIKE "Not %" AND o.optionText NOT LIKE "Use not %" '.
            'ORDER BY h.tid, h.sortsequence, o.sortsequence ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                if(!array_key_exists($row->tid,$returnArr)) $returnArr[$row->tid] = array();
                if(!array_key_exists($row->ethuhid,$returnArr[$row->tid])) $returnArr[$row->tid][$row->ethuhid]['header'] = $row->headerText;
                $returnArr[$row->tid][$row->ethuhid][$row->ethuoid] = $row->optionText;
            }
            $rs->close();
        }
        return $returnArr;
    }

    protected function setSciNamesByVerns(){
        $sql = "SELECT DISTINCT v.VernacularName, t.tid, t.sciname, ts.family, t.rankid ".
            "FROM (taxstatus ts INNER JOIN taxavernaculars v ON ts.TID = v.TID) ".
            "INNER JOIN taxa t ON t.TID = ts.tidaccepted ";
        $whereStr = "";
        foreach($this->taxaArr as $key => $value){
            $whereStr .= "OR v.VernacularName = '".$this->cleanInStr($key)."' ";
        }
        $sql .= "WHERE (ts.taxauthid = 1) AND (".substr($whereStr,3).") ORDER BY t.rankid LIMIT 20";
        //echo "<div>sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        if($result->num_rows){
            while($row = $result->fetch_object()){
                $vernName = strtolower($row->VernacularName);
                if($row->rankid < 140){
                    $this->taxaArr[$vernName]["tid"][] = $row->tid;
                }
                elseif($row->rankid == 140){
                    $this->taxaArr[$vernName]["families"][] = $row->sciname;
                }
                else{
                    $this->taxaArr[$vernName]["scinames"][] = $row->sciname;
                }
            }
        }
        else{
            $this->taxaArr["no records"]["scinames"][] = "no records";
        }
        $result->free();
    }

    protected function setSynonyms(){
        foreach($this->taxaArr as $key => $value){
            if(array_key_exists("scinames",$value)){
                if(!in_array("no records",$value["scinames"])){
                    $synArr = $this->getSynonyms($value["scinames"]);
                    if($synArr) $this->taxaArr[$key]["synonyms"] = $synArr;
                }
            }
            else{
                $synArr = $this->getSynonyms($key);
                if($synArr) $this->taxaArr[$key]["synonyms"] = $synArr;
            }
        }
    }

    private function getSynonyms($searchTarget,$taxAuthId = 1){
        $synArr = array();
        $targetTidArr = array();
        $searchStr = '';
        if(is_array($searchTarget)){
            if(is_numeric(current($searchTarget))){
                $targetTidArr = $searchTarget;
            }
            else{
                $searchStr = implode('","',$searchTarget);
            }
        }
        else{
            if(is_numeric($searchTarget)){
                $targetTidArr[] = $searchTarget;
            }
            else{
                $searchStr = $searchTarget;
            }
        }
        if($searchStr){
            //Input is a string, thus get tids
            $sql1 = 'SELECT tid FROM taxa WHERE sciname IN("'.$searchStr.'")';
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $targetTidArr[] = $r1->tid;
            }
            $rs1->free();
        }

        if($targetTidArr){
            //Get acceptd names
            $accArr = array();
            $rankId = 0;
            $sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.rankid '.
                'FROM taxa t INNER JOIN taxstatus ts ON t.Tid = ts.TidAccepted '.
                'WHERE (ts.taxauthid = '.$taxAuthId.') AND (ts.tid IN('.implode(',',$targetTidArr).')) ';
            $rs2 = $this->conn->query($sql2);
            while($r2 = $rs2->fetch_object()){
                $accArr[] = $r2->tid;
                $rankId = $r2->rankid;
                //Put in synonym array if not target
                $synArr[$r2->tid] = $r2->sciname;
            }
            $rs2->free();

            if($accArr){
                //Get synonym that are different than target
                $sql3 = 'SELECT DISTINCT t.tid, t.sciname ' .
                    'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                    'WHERE (ts.taxauthid = ' . $taxAuthId . ') AND (ts.tidaccepted IN(' . implode('', $accArr) . ')) ';
                $rs3 = $this->conn->query($sql3);
                while ($r3 = $rs3->fetch_object()) {
                    $synArr[$r3->tid] = $r3->sciname;
                }
                $rs3->free();

                //If rank is 220, get synonyms of accepted children
                if ($rankId == 220) {
                    $sql4 = 'SELECT DISTINCT t.tid, t.sciname ' .
                        'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                        'WHERE (ts.parenttid IN(' . implode('', $accArr) . ')) AND (ts.taxauthid = ' . $taxAuthId . ') ' .
                        'AND (ts.TidAccepted = ts.tid)';
                    $rs4 = $this->conn->query($sql4);
                    while ($r4 = $rs4->fetch_object()) {
                        $synArr[$r4->tid] = $r4->sciname;
                    }
                    $rs4->free();
                }
            }
        }
        return $synArr;
    }

    private function queryRecordID($idArr){
        $retArr = array();
        if($idArr){
            $sql = 'SELECT occid FROM guidoccurrences WHERE guid IN("'.implode('","', $idArr).'")';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[] = $r->occid;
            }
            $rs->free();
        }
        return $retArr;
    }

    protected function formatDate($inDate){
        $retDate = OccurrenceUtilities::formatDate($inDate);
        return $retDate;
    }

    public function getSearchTermsArr(){
        return $this->searchTermsArr;
    }

    public function __destruct(){
        if(!($this->conn === false)){
            $this->conn->close();
            $this->conn = null;
        }
    }

    public function setSearchTermsArr($stArr){
        if($stArr) $this->searchTermsArr = $stArr;
    }

    public function getSearchTerm($k){
        if(array_key_exists($k,$this->searchTermsArr)){
            return $this->searchTermsArr[$k];
        }
        else{
            return "";
        }
    }

    public function getDatasetSearchStr(){
        $retStr ="";
        if(!array_key_exists('db',$this->searchTermsArr) || $this->searchTermsArr['db'] == 'all'){
            $retStr = "All Collections";
        }
        elseif($this->searchTermsArr['db'] == 'allspec'){
            $retStr = "All Specimen Collections";
        }
        elseif($this->searchTermsArr['db'] == 'allobs'){
            $retStr = "All Observation Projects";
        }
        else{
            $cArr = explode(';',$this->searchTermsArr['db']);
            if($cArr[0]){
                $sql = 'SELECT collid, CONCAT_WS("-",institutioncode,collectioncode) as instcode '.
                    'FROM omcollections WHERE collid IN('.$cArr[0].') ORDER BY institutioncode,collectioncode';
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $retStr .= '; '.$r->instcode;
                }
                $rs->free();
            }
            $retStr = substr($retStr,2);
        }
        return $retStr;
    }

    public function getTaxaSearchStr(){
        $returnArr = Array();
        foreach($this->taxaArr as $taxonName => $taxonArr){
            $str = $taxonName;
            if(array_key_exists("sciname",$taxonArr)){
                $str .= " => ".implode(", ",$taxonArr["sciname"]);
            }
            if(array_key_exists("synonyms",$taxonArr)){
                $str .= " (".implode(", ",$taxonArr["synonyms"]).")";
            }
            $returnArr[] = $str;
        }
        return implode("; ", $returnArr);
    }

    public function getCloseTaxaMatch($name){
        $retArr = array();
        $searchName = $this->cleanInStr($name);
        $sql = 'SELECT tid, sciname FROM taxa WHERE soundex(sciname) = soundex(?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $searchName);
        $stmt->execute();
        $stmt->bind_result($tid, $sciname);
        while($stmt->fetch()){
            if($searchName != $sciname) $retArr[$tid] = $sciname;
        }
        $stmt->close();
        return $retArr;
    }

    public function getEthnoDataArr(){
        $returnArr = array();
        $sqlWhere = $this->getSqlWhere();
        $sql = 'SELECT DISTINCT ed.ethdid, ed.verbatimVernacularName, ed.langId, g.id, g.iso639P3code, g.`name`, ed.verbatimParse, '.
            'o.occid, c.CollID, c.institutioncode, c.collectioncode, c.collectionname, c.icon, ed.etheventid, '.
            'CONCAT_WS(":",c.institutioncode, c.collectioncode) AS collection, o.tidinterpreted, '.
            'ed.annotatedVernacularName, ed.verbatimLanguage, ed.annotatedParse, ed.typology, ed.`translation`, ed.tid, '.
            'ed.annotatedGloss, ed.taxonomicDescription, ed.refpages, r.title, ed.verbatimGloss, ed.nameDiscussion, t.sciname, '.
            'CONCAT_WS(", ",de.eventdate,de.eventlocation) AS dataEventStr, g2.id AS id2, g2.iso639P3code AS iso639P3code2, '.
            'g2.`name` AS name2, ed.otherVerbatimVernacularName, ed.otherLangId, ed.consultantComments, ed.useDiscussion '.
            'FROM ethnodata AS ed LEFT JOIN glottolog AS g ON ed.langId = g.id '.
            'LEFT JOIN glottolog AS g2 ON ed.otherLangId = g2.id '.
            'LEFT JOIN omoccurrences AS o ON ed.occid = o.occid '.
            'LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'LEFT JOIN ethnodataevent AS de ON ed.etheventid = de.etheventid '.
            'LEFT JOIN ethnodatanamesemtaglink AS sl ON ed.ethdid = sl.ethdid '.
            'LEFT JOIN ethnodatausepartslink AS pl ON ed.ethdid = pl.ethdid '.
            'LEFT JOIN ethnodatausetaglink AS ul ON ed.ethdid = ul.ethdid '.
            'LEFT JOIN taxa AS t ON ed.tid = t.tid '.
            'LEFT JOIN referenceobject AS r ON de.refid = r.refid '.$sqlWhere.' ';
        $sql .= "ORDER BY c.sortseq, c.collectionname ";
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $recId = $row->ethdid;
                $tid = ($row->tid?$row->tid:$row->tidinterpreted);
                $langName = ($row->id?$row->id.' | '.($row->iso639P3code?$row->iso639P3code:'[No ISO code]').' | '.$row->name:'');
                $langName2 = ($row->id2?$row->id2.' | '.($row->iso639P3code2?$row->iso639P3code2:'[No ISO code]').' | '.$row->name2:'');
                $returnArr[$recId]['personnelArr'] = $this->getDataRecordPersonnelArr($recId);
                $returnArr[$recId]['semanticTags'] = $this->getDataRecordSemanticsArr($recId);
                $returnArr[$recId]['partsTags'] = $this->getDataRecordPartsArr($recId);
                $returnArr[$recId]['useTags'] = $this->getDataRecordUsesArr($recId);
                $returnArr[$recId]['verbatimVernacularName'] = $row->verbatimVernacularName;
                $returnArr[$recId]['annotatedVernacularName'] = $row->annotatedVernacularName;
                $returnArr[$recId]['langName'] = $langName;
                $returnArr[$recId]['langId'] = $row->langId;
                $returnArr[$recId]['sciname'] = $row->sciname;
                $returnArr[$recId]['kingdomId'] = $this->getUseEditKingdomId($tid);
                $returnArr[$recId]['otherVerbatimVernacularName'] = $row->otherVerbatimVernacularName;
                $returnArr[$recId]['otherLangId'] = $row->otherLangId;
                $returnArr[$recId]['otherLangName'] = $langName2;
                $returnArr[$recId]['verbatimLanguage'] = $row->verbatimLanguage;
                $returnArr[$recId]['verbatimParse'] = $row->verbatimParse;
                $returnArr[$recId]['verbatimGloss'] = $row->verbatimGloss;
                $returnArr[$recId]['annotatedParse'] = $row->annotatedParse;
                $returnArr[$recId]['annotatedGloss'] = $row->annotatedGloss;
                $returnArr[$recId]['taxonomicDescription'] = $row->taxonomicDescription;
                $returnArr[$recId]['typology'] = $row->typology;
                $returnArr[$recId]['translation'] = $row->translation;
                $returnArr[$recId]['nameDiscussion'] = $row->nameDiscussion;
                $returnArr[$recId]['refpages'] = $row->refpages;
                $returnArr[$recId]['reftitle'] = $row->title;
                $returnArr[$recId]['dataeventstr'] = $row->dataEventStr;
                $returnArr[$recId]['consultantComments'] = $row->consultantComments;
                $returnArr[$recId]['useDiscussion'] = $row->useDiscussion;
                $returnArr[$recId]['collid'] = $row->CollID;
                $returnArr[$recId]['institutioncode'] = $row->institutioncode;
                $returnArr[$recId]['collectioncode'] = $row->collectioncode;
                $returnArr[$recId]['collectionname'] = $row->collectionname;
                $returnArr[$recId]['icon'] = $row->icon;
                $returnArr[$recId]['collection'] = $row->collection;
                $returnArr[$recId]['etheventid'] = $row->etheventid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getMapSpecimenArr($pageRequest,$cntPerPage,$mapWhere){
        global $userRights;
        $retArr = Array();
        if(!$this->recordCount){
            $this->setRecordCnt($mapWhere);
        }
        $sql = 'SELECT o.occid, c.institutioncode, o.catalognumber, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, '.
            'o.eventdate, o.family, o.sciname, CONCAT_WS("; ",o.country, o.stateProvince, o.county) AS locality, o.DecimalLatitude, o.DecimalLongitude, '.
            'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason '.
            'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid ';
        if(array_key_exists("clid",$this->searchTermsArr)) $sql .= "LEFT JOIN fmvouchers AS v ON o.occid = v.occid ";
        if(array_key_exists("polycoords",$this->searchTermsArr)) $sql .= "LEFT JOIN omoccurpoints p ON o.occid = p.occid ";
        $sql .= "LEFT JOIN ethnodata AS ed ON o.occid = ed.occid ";
        $sql .= "LEFT JOIN ethnodatanamesemtaglink AS sl ON ed.ethdid = sl.ethdid ";
        $sql .= "LEFT JOIN ethnodatausepartslink AS pl ON ed.ethdid = pl.ethdid ";
        $sql .= "LEFT JOIN ethnodatausetaglink AS ul ON ed.ethdid = ul.ethdid ";
        $sql .= $mapWhere;
        $sql .= " AND (o.sciname IS NOT NULL AND o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ";
        if(array_key_exists("SuperAdmin",$userRights) || array_key_exists("CollAdmin",$userRights) || array_key_exists("RareSppAdmin",$userRights) || array_key_exists("RareSppReadAll",$userRights)){
            //Is global rare species reader, thus do nothing to sql and grab all records
        }
        elseif(array_key_exists("RareSppReader",$userRights)){
            $sql .= " AND (o.CollId IN (".implode(",",$userRights["RareSppReader"]).") OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ";
        }
        else{
            $sql .= " AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ";
        }
        $bottomLimit = ($pageRequest - 1)*$cntPerPage;
        $sql .= "ORDER BY o.sciname, o.eventdate ";
        $sql .= "LIMIT ".$bottomLimit.",".$cntPerPage;
        //echo "<div>Spec sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        $canReadRareSpp = false;
        if(array_key_exists("SuperAdmin", $userRights) || array_key_exists("CollAdmin", $userRights) || array_key_exists("RareSppAdmin", $userRights) || array_key_exists("RareSppReadAll", $userRights)){
            $canReadRareSpp = true;
        }
        while($r = $result->fetch_object()){
            $occId = $r->occid;
            $retArr[$occId]['i'] = $this->cleanOutStr($r->institutioncode);
            $retArr[$occId]['cat'] = $this->cleanOutStr($r->catalognumber);
            $retArr[$occId]['c'] = $this->cleanOutStr($r->collector);
            $retArr[$occId]['e'] = $this->cleanOutStr($r->eventdate);
            $retArr[$occId]['f'] = $this->cleanOutStr($r->family);
            $retArr[$occId]['s'] = $this->cleanOutStr($r->sciname);
            $retArr[$occId]['l'] = $this->cleanOutStr($r->locality);
            $retArr[$occId]['lat'] = $this->cleanOutStr($r->DecimalLatitude);
            $retArr[$occId]['lon'] = $this->cleanOutStr($r->DecimalLongitude);
            $localitySecurity = $r->LocalitySecurity;
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists("CollEditor", $userRights) && in_array($collIdStr,$userRights["CollEditor"]))
                || (array_key_exists("RareSppReader", $userRights) && in_array($collIdStr,$userRights["RareSppReader"]))){
                $retArr[$occId]['l'] = str_replace('.,',',',$r->locality);
            }
            else{
                $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                if($r->localitysecurityreason){
                    $securityStr .= $r->localitysecurityreason;
                }
                else{
                    $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                }
                $retArr[$occId]['l'] = $securityStr.'</span>';
            }
            //Set access statistics
            if($retArr){
                $statsManager = new OccurrenceAccessStats();
                $statsManager->recordAccessEventByArr(array_keys($retArr),'list');
            }
        }
        $result->close();
        return $retArr;
        //return $sql;
    }

    public function getCollGeoCoords($mapWhere,$pageRequest,$cntPerPage){
        global $userRights, $mappingBoundaries;
        $coordArr = Array();
        $sql = 'SELECT o.occid, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS identifier, '.
            'o.sciname, o.family, o.tidinterpreted, o.DecimalLatitude, o.DecimalLongitude, o.collid, o.catalognumber, '.
            'o.othercatalognumbers, c.institutioncode, c.collectioncode, c.CollectionName ';
        if($this->fieldArr){
            foreach($this->fieldArr as $k => $v){
                $sql .= ", o.".$v." ";
            }
        }
        $sql .= "FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid ";
        if(array_key_exists("clid",$this->searchTermsArr)) $sql .= "LEFT JOIN fmvouchers AS v ON o.occid = v.occid ";
        if(array_key_exists("polycoords",$this->searchTermsArr)) $sql .= "LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ";
        $sql .= "LEFT JOIN ethnodata AS ed ON o.occid = ed.occid ";
        $sql .= "LEFT JOIN ethnodatanamesemtaglink AS sl ON ed.ethdid = sl.ethdid ";
        $sql .= "LEFT JOIN ethnodatausepartslink AS pl ON ed.ethdid = pl.ethdid ";
        $sql .= "LEFT JOIN ethnodatausetaglink AS ul ON ed.ethdid = ul.ethdid ";
        $sql .= $mapWhere;
        $sql .= " AND (o.sciname IS NOT NULL AND o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ";
        if(array_key_exists("SuperAdmin",$userRights) || array_key_exists("CollAdmin",$userRights) || array_key_exists("RareSppAdmin",$userRights) || array_key_exists("RareSppReadAll",$userRights)){
            //Is global rare species reader, thus do nothing to sql and grab all records
        }
        elseif(array_key_exists("RareSppReader",$userRights)){
            $sql .= " AND (o.CollId IN (".implode(",",$userRights["RareSppReader"]).") OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ";
        }
        else{
            $sql .= " AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ";
        }
        if($pageRequest && $cntPerPage){
            $sql .= "LIMIT ".$pageRequest.",".$cntPerPage;
        }
        $collMapper = Array();
        $collMapper["undefined"] = "undefined";
        $usedColors = Array();
        $color = 'e69e67';
        //echo json_encode($this->taxaArr);
        //echo "<div>SQL: ".$sql."</div>";
        $statsManager = new OccurrenceAccessStats();
        $result = $this->conn->query($sql);
        $recCnt = 0;
        while($row = $result->fetch_object()){
            if(($row->DecimalLongitude <= 180 && $row->DecimalLongitude >= -180) && ($row->DecimalLatitude <= 90 && $row->DecimalLatitude >= -90)){
                $occId = $row->occid;
                $collName = $row->CollectionName;
                $family = $row->family;
                $tidInterpreted = $this->xmlentities($row->tidinterpreted);
                $latLngStr = $row->DecimalLatitude.",".$row->DecimalLongitude;
                $coordArr[$collName][$occId]["latLngStr"] = $latLngStr;
                $coordArr[$collName][$occId]["collid"] = $this->xmlentities($row->collid);
                $tidcode = strtolower(str_replace( " ", "",$tidInterpreted.$row->sciname));
                $tidcode = preg_replace( "/[^A-Za-z0-9 ]/","",$tidcode);
                $coordArr[$collName][$occId]["namestring"] = $this->xmlentities($tidcode);
                $coordArr[$collName][$occId]["tidinterpreted"] = $tidInterpreted;
                if($family){
                    $coordArr[$collName][$occId]["family"] = strtoupper($family);
                }
                else{
                    $coordArr[$collName][$occId]["family"] = 'undefined';
                }
                $coordArr[$collName][$occId]["sciname"] = $row->sciname;
                $coordArr[$collName][$occId]["identifier"] = $this->xmlentities($row->identifier);
                $coordArr[$collName][$occId]["institutioncode"] = $this->xmlentities($row->institutioncode);
                $coordArr[$collName][$occId]["collectioncode"] = $this->xmlentities($row->collectioncode);
                $coordArr[$collName][$occId]["catalognumber"] = $this->xmlentities($row->catalognumber);
                $coordArr[$collName][$occId]["othercatalognumbers"] = $this->xmlentities($row->othercatalognumbers);
                $coordArr[$collName]["color"] = $color;
                if($this->fieldArr){
                    foreach($this->fieldArr as $k => $v){
                        $coordArr[$collName][$occId][$v] = $this->xmlentities($row->$v);
                    }
                }
                $statsManager->recordAccessEvent($occId, 'map');
            }
        }
        if(array_key_exists("undefined",$coordArr)){
            $coordArr["undefined"]["color"] = $color;
        }
        $result->close();

        return $coordArr;
        //return $sql;
    }

    public function getUseEditKingdomId($tId){
        $return = 0;
        $sql = 'SELECT t2.TID AS kingdomId '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
            'WHERE te.tid = '.$tId.' AND te.taxauthid = 1 AND t2.RankId = 10 ';
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $return = $row->kingdomId;
            }
            $rs->close();
        }
        return $return;
    }

    public function getDataRecordUsesArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethuoid FROM ethnodatausetaglink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethuoid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordPartsArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethpuid FROM ethnodatausepartslink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethpuid;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordSemanticsArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethTagId FROM ethnodatanamesemtaglink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethTagId;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getDataRecordPersonnelArr($recId){
        $returnArr = array();
        $sql = 'SELECT ethPerID FROM ethnodatapersonnellink WHERE ethdid = '.$recId;
        if($rs = $this->conn->query($sql)){
            while($row = $rs->fetch_object()){
                $returnArr[] = $row->ethPerID;
            }
            $rs->close();
        }
        return $returnArr;
    }

    public function getLocalSearchStr(){
        return implode("; ", $this->localSearchArr);
    }

    public function getClName(){
        return $this->clName;
    }

    public function getSearchResultUrl(){
        $url = '?';
        $stPieces = Array();
        foreach($this->searchTermsArr as $i => $v){
            if($v){
                $stPieces[] = $i.'='.$v;
            }
        }
        $url .= implode("&",$stPieces);
        $url = str_replace('&taxontype=','&type=',$url);
        $url = str_replace('&usethes=','&thes=',$url);
        $url = str_replace(' ','%20',$url);
        return $url;
    }

    public function getChecklist($taxonAuthorityId){
        $returnVec = Array();
        $this->checklistTaxaCnt = 0;
        $sql = "";
        $sqlWhere = $this->getSqlWhere();
        if($taxonAuthorityId && is_numeric($taxonAuthorityId)){
            $sql = 'SELECT DISTINCT ts.family, t.sciname '.
                'FROM ((omoccurrences o INNER JOIN taxstatus ts1 ON o.TidInterpreted = ts1.Tid) '.
                'INNER JOIN taxa t ON ts1.TidAccepted = t.Tid) '.
                'INNER JOIN taxstatus ts ON t.tid = ts.tid ';
            $sql .= $this->setTableJoins($sqlWhere);
            $sql .= str_ireplace("o.sciname","t.sciname",str_ireplace("o.family","ts.family",$sqlWhere)).
                " AND ts1.taxauthid = ".$taxonAuthorityId." AND ts.taxauthid = ".$taxonAuthorityId." AND t.RankId > 140 ";
        }
        else{
            $sql = 'SELECT DISTINCT IFNULL(ts.family,o.family) AS family, o.sciname '.
                'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
                'LEFT JOIN taxstatus ts ON t.tid = ts.tid ';
            $sql .= $this->setTableJoins($sqlWhere);
            $sql .= $sqlWhere." AND (t.rankid > 140) AND (ts.taxauthid = 1) ";
        }
        //echo "<div>".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $family = strtoupper($row->family);
            if(!$family) $family = 'undefined';
            $sciName = $row->sciname;
            if($sciName && substr($sciName,-5)!='aceae'){
                $returnVec[$family][] = $sciName;
                $this->checklistTaxaCnt++;
            }
        }
        return $returnVec;
    }

    public function getChecklistTaxaCnt(){
        return $this->checklistTaxaCnt;
    }

    private function xmlentities($string){
        return str_replace(array ('&','"',"'",'<','>','?'),array ('&amp;','&quot;','&apos;','&lt;','&gt;','&apos;'),$string);
    }

    public function setSorting($sf1,$sf2,$so){
        $this->sortField1 = $sf1;
        $this->sortField2 = $sf2;
        $this->sortOrder = $so;
    }

    protected function cleanInputStr($str){
        $newStr = str_replace(array('"', "'"), array('', '%apos;'), $str);
        $newStr = strip_tags($newStr);
        return $newStr;
    }

    protected function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }

    protected function cleanOutStr($str){
        return htmlspecialchars($str);
    }
}
?>

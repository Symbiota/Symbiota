<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class EthnoProjectManager {

    private $conn;
    private $collid;
    private $cplid;
    private $perid;
    private $comid;

    public function __construct(){
        $this->conn = MySQLiConnectionFactory::getCon("write");
    }

    public function __destruct(){
        if(!($this->conn === null)) $this->conn->close();
    }

    public function setCollid($id){
        if(is_numeric($id)){
            $this->collid = $id;
            return true;
        }
        return false;
    }

    public function setCplid($id){
        if(is_numeric($id)){
            $this->cplid = $id;
            return true;
        }
        return false;
    }

    public function setPerid($id){
        if(is_numeric($id)){
            $this->perid = $id;
            return true;
        }
        return false;
    }

    public function setComid($id){
        if(is_numeric($id)){
            $this->comid = $id;
            return true;
        }
        return false;
    }

    public function getComid(){
        return $this->comid;
    }

    public function getPerid(){
        return $this->perid;
    }

    public function getCplid(){
        return $this->cplid;
    }

    public function getCollectionName(){
        $collName = '';
        $sql = 'SELECT collid, collectionname '.
            'FROM omcollections WHERE (collid = '.$this->collid.')';
        if($rs = $this->conn->query($sql)){
            if($row = $rs->fetch_object()){
                $collName = $row->collectionname;
            }
            $rs->close();
        }
        return $collName;
    }

    public function getPersonnelRoleArr(){
        $retArr = Array();
        $sql = 'SELECT roleID, roleName, sortSeq '.
            'FROM ethnopersonnelroles '.
            'ORDER BY sortSeq ';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->roleID] = $row->roleName;
        }
        $rs->free();
        return $retArr;
    }
	
	public function getPersonnelArr(){
		$retArr = Array();
		$sql = 'SELECT ecp.ethCollPerLinkID, ecp.projectCode, ecp.rolearr, ecp.rolecomments, ep.ethPerID, '.
			'CONCAT_WS(" ",ep.title,ep.firstname,ep.lastname) AS fullName, ec.communityname AS birthCommunity '.
            'FROM ethnocollperlink AS ecp LEFT JOIN ethnopersonnel AS ep ON ecp.perID = ep.ethPerID '.
            'LEFT JOIN ethnocommunity AS ec ON ecp.birthCommunity = ec.ethComID '.
            'WHERE ecp.collID = '.$this->collid.' '.
            'ORDER BY fullName ';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->ethCollPerLinkID]['pid'] = $row->ethPerID;
            $retArr[$row->ethCollPerLinkID]['code'] = $row->projectCode;
            $retArr[$row->ethCollPerLinkID]['birthCommunity'] = $row->birthCommunity;
            $retArr[$row->ethCollPerLinkID]['rolearr'] = json_decode($row->rolearr);
            $retArr[$row->ethCollPerLinkID]['rolecomments'] = $row->rolecomments;
            $retArr[$row->ethCollPerLinkID]['name'] = $row->fullName;
		}
		$rs->free();
		return $retArr;
	}

    public function getCommunityArr(){
        $retArr = Array();
        $sql = 'SELECT ecc.ethCollCommLinkID, ecc.commID, ec.communityname '.
            'FROM ethnocollcommlink AS ecc LEFT JOIN ethnocommunity AS ec on ecc.commID = ec.ethComID '.
            'WHERE ecc.collID = '.$this->collid.' '.
            'ORDER BY ec.communityname ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->ethCollCommLinkID]['comid'] = $row->commID;
            $retArr[$row->ethCollCommLinkID]['communityname'] = $row->communityname;
        }
        $rs->free();
        return $retArr;
    }

    public function getReferenceArr(){
        $retArr = Array();
        $sql = 'SELECT ecr.ethCollRefLinkID, ecr.refid, r.title '.
            'FROM ethnocollreflink AS ecr LEFT JOIN referenceobject AS r on ecr.refid = r.refid '.
            'WHERE ecr.collID = '.$this->collid.' '.
            'ORDER BY r.title ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->ethCollRefLinkID]['refid'] = $row->refid;
            $retArr[$row->ethCollRefLinkID]['title'] = $row->title;
        }
        $rs->free();
        return $retArr;
    }

    public function getLanguageArr(){
        $retArr = Array();
        $sql = 'SELECT ecl.ethCollLangLinkID, g.id, g.`name`, g.iso639P3code, ecl.fileurl, ecl.filetopic '.
            'FROM ethnocolllanglink AS ecl LEFT JOIN glottolog AS g ON ecl.langID = g.id '.
            'WHERE ecl.collID = '.$this->collid.' '.
            'ORDER BY g.`name` ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $retArr[$row->ethCollLangLinkID]['glottocode'] = $row->id;
            $retArr[$row->ethCollLangLinkID]['isocode'] = $row->iso639P3code;
            $retArr[$row->ethCollLangLinkID]['langname'] = $row->name;
            $retArr[$row->ethCollLangLinkID]['fileurl'] = $row->fileurl;
            $retArr[$row->ethCollLangLinkID]['filetopic'] = $row->filetopic;
        }
        $rs->free();
        return $retArr;
    }

    public function getPersonnelNameList($fn,$ln){
        $retArr = Array();
        $whereArr = array();
        if($fn) $whereArr[] = 'firstname LIKE "'.$this->cleanInStr($fn).'%"';
        if($ln) $whereArr[] = 'lastname LIKE "'.$this->cleanInStr($ln).'%"';
        $sql = 'SELECT DISTINCT ethPerID, CONCAT_WS(" ",title,firstname,lastname) AS fullName '.
            'FROM ethnopersonnel '.
            'WHERE '.implode(" AND ",$whereArr).' '.
            'ORDER BY fullName '.
            'LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->fullName]['id'] = $r->ethPerID;
            $retArr[$r->fullName]['value'] = $r->fullName;
        }
        return $retArr;
    }

    public function getProjLangList($collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT g.id, g.iso639P3code, g.`name` '.
            'FROM glottolog AS g LEFT JOIN ethnocolllanglink AS cl ON g.id = cl.langID '.
            'WHERE cl.collID = '.$collid.' '.
            'ORDER BY g.`name` ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $display = $r->id.' | '.($r->iso639P3code?$r->iso639P3code:'[No ISO code]').' | '.$r->name;
            $retArr[$r->name]['id'] = $r->id;
            $retArr[$r->name]['value'] = $display;
        }
        return $retArr;
    }

    public function getProjCommList($collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT c.ethComID, c.communityname '.
            'FROM ethnocommunity AS c LEFT JOIN ethnocollcommlink AS ec ON c.ethComID = ec.commID '.
            'WHERE cl.collID = '.$collid.' '.
            'ORDER BY c.communityname ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $display = $r->id.' | '.($r->iso639P3code?$r->iso639P3code:'[No ISO code]').' | '.$r->name;
            $retArr[$r->name]['id'] = $r->id;
            $retArr[$r->name]['value'] = $display;
        }
        return $retArr;
    }

    public function getLangNameList($ln,$collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT g.id, g.iso639P3code, g.`name` '.
            'FROM glottolog AS g ';
        if($collid) $sql .= 'LEFT JOIN ethnocolllanglink AS cl ON g.id = cl.langID ';
        $sql .= 'WHERE (g.`name` LIKE "%'.$this->cleanInStr($ln).'%" OR g.id LIKE "%'.$this->cleanInStr($ln).'%" OR g.iso639P3code LIKE "%'.$this->cleanInStr($ln).'%") ';
        if($collid) $sql .= 'AND cl.collID = '.$collid.' ';
        $sql .='ORDER BY g.`name` ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $display = $r->id.' | '.($r->iso639P3code?$r->iso639P3code:'[No ISO code]').' | '.$r->name;
            $retArr[$r->name]['id'] = $r->id;
            $retArr[$r->name]['value'] = $display;
        }
        return $retArr;
    }

    public function getLangNameDropDownList($collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT g.id, g.iso639P3code, g.`name` '.
            'FROM glottolog AS g LEFT JOIN ethnocolllanglink AS cl ON g.id = cl.langID '.
            'WHERE cl.collID = '.$collid.' '.
            'ORDER BY g.`name` ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $display = $r->id.' | '.($r->iso639P3code?$r->iso639P3code:'[No ISO code]').' | '.$r->name;
            $retArr[$r->name]['id'] = $r->id;
            $retArr[$r->name]['name'] = $display;
        }
        return $retArr;
    }

    public function getCommNameList($cn,$collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT c.ethComID, c.communityname '.
            'FROM ethnocommunity AS c ';
        if($collid) $sql .= 'LEFT JOIN ethnocollcommlink AS ec ON c.ethComID = ec.commID ';
        $sql .= 'WHERE c.communityname LIKE "'.$this->cleanInStr($cn).'%" ';
        if($collid) $sql .= 'AND ec.collID = '.$collid.' ';
        $sql .='ORDER BY c.communityname '.
            'LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->communityname]['id'] = $r->ethComID;
            $retArr[$r->communityname]['value'] = $r->communityname;
        }
        return $retArr;
    }

    public function getRefTitleList($rt){
        $retArr = Array();
        $sql = 'SELECT DISTINCT r.refid, r.title '.
            'FROM referenceobject AS r ';
        $sql .= 'WHERE r.title LIKE "%'.$this->cleanInStr($rt).'%" ';
        $sql .='ORDER BY r.title '.
            'LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->title]['id'] = $r->refid;
            $retArr[$r->title]['value'] = $r->title;
        }
        return $retArr;
    }

    public function getRefAuthorList($auth){
        $retArr = Array();
        $sql = 'SELECT DISTINCT r.refid, r.title '.
            'FROM referenceobject AS r ';
        $sql .= 'WHERE r.title LIKE "%'.$this->cleanInStr($auth).'%" ';
        $sql .='ORDER BY r.title '.
            'LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->title]['id'] = $r->refid;
            $retArr[$r->title]['value'] = $r->title;
        }
        return $retArr;
    }

    public function getCommNameDropDownList($collid){
        $retArr = Array();
        $sql = 'SELECT DISTINCT c.ethComID, c.communityname '.
            'FROM ethnocommunity AS c LEFT JOIN ethnocollcommlink AS ec ON c.ethComID = ec.commID '.
            'WHERE ec.collID = '.$collid.' '.
            'ORDER BY c.communityname ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->communityname]['id'] = $r->ethComID;
            $retArr[$r->communityname]['name'] = $r->communityname;
        }
        return $retArr;
    }

    public function getCountryNameList($cn){
        $retArr = Array();
        $sql = 'SELECT DISTINCT countryName '.
            'FROM lkupcountry '.
            'WHERE countryName LIKE "'.$this->cleanInStr($cn).'%" '.
            'ORDER BY countryName '.
            'LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->countryName]['value'] = $r->countryName;
        }
        return $retArr;
    }

    public function getPersonnelInfoArr(){
        $retArr = Array();
        $sql = 'SELECT p.title, p.firstname, p.lastname, p.birthyear, p.birthyearestimation, p.sex, '.
            'p.sexcomments, c.communityname, g.`name` '.
            'FROM ethnopersonnel AS p LEFT JOIN glottolog AS g ON p.primarylanguageID = g.id '.
            'LEFT JOIN ethnocommunity AS c ON p.birthcommunityID = c.ethComID '.
            'WHERE p.ethPerID = '.$this->perid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr['title'] = $r->title;
            $retArr['firstname'] = $r->firstname;
            $retArr['lastname'] = $r->lastname;
            $retArr['birthyear'] = $r->birthyear;
            $retArr['birthyearestimation'] = $r->birthyearestimation;
            $retArr['sex'] = $r->sex;
            $retArr['sexcomments'] = $r->sexcomments;
            $retArr['birthcommunityname'] = $r->communityname;
            $retArr['primarylanguage'] = $r->name;
        }
        return $retArr;
    }

    public function getCommunityInfoArr(){
        $retArr = Array();
        $sql = 'SELECT c.communityname, c.country, c.stateProvince, c.county, c.municipality, c.decimalLatitude, '.
            'c.decimalLongitude, c.elevationInMeters, c.languagecomments '.
            'FROM ethnocommunity AS c '.
            'WHERE c.ethComID = '.$this->comid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr['communityname'] = $r->communityname;
            $retArr['country'] = $r->country;
            $retArr['stateProvince'] = $r->stateProvince;
            $retArr['county'] = $r->county;
            $retArr['municipality'] = $r->municipality;
            $retArr['decimalLatitude'] = $r->decimalLatitude;
            $retArr['decimalLongitude'] = $r->decimalLongitude;
            $retArr['elevationInMeters'] = $r->elevationInMeters;
            $retArr['languagecomments'] = $r->languagecomments;
        }
        return $retArr;
    }

    public function getPersonnelProjInfoArr(){
        $retArr = Array();
        $sql = 'SELECT ethCollPerLinkID, rolearr, rolecomments, projectCode, defaultdisplay, residenceCommunity, '.
            'residenceStatus, birthCommunity, commcomments, targetLanguage, targetLangQual, secondLanguage, '.
            'secondLangQual, thirdLanguage, thirdLangQual '.
            'FROM ethnocollperlink '.
            'WHERE perID = '.$this->perid.' AND collID = '.$this->collid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr['linkId'] = $r->ethCollPerLinkID;
            $retArr['rolearr'] = json_decode($r->rolearr);
            $retArr['rolecomments'] = $r->rolecomments;
            $retArr['projectCode'] = $r->projectCode;
            $retArr['defaultdisplay'] = $r->defaultdisplay;
            $retArr['residenceCommunity'] = $r->residenceCommunity;
            $retArr['residenceStatus'] = $r->residenceStatus;
            $retArr['birthCommunity'] = $r->birthCommunity;
            $retArr['commcomments'] = $r->commcomments;
            $retArr['targetLanguage'] = $r->targetLanguage;
            $retArr['targetLangQual'] = $r->targetLangQual;
            $retArr['secondLanguage'] = $r->secondLanguage;
            $retArr['secondLangQual'] = $r->secondLangQual;
            $retArr['thirdLanguage'] = $r->thirdLanguage;
            $retArr['thirdLangQual'] = $r->thirdLangQual;
        }
        return $retArr;
    }

    public function getPersonnelCommArr(){
        $retArr = Array();
        $sql = 'SELECT ecpc.ethCollPerCommLinkID, ecpc.commID, ec.communityname, ecpc.resident, ecpc.commcomments '.
            'FROM ethnocollpercommlink AS ecpc LEFT JOIN ethnocommunity AS ec ON ecpc.commID = ec.ethComID '.
            'WHERE ecpc.perID = '.$this->perid.' AND ecpc.collID = '.$this->collid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->ethCollPerCommLinkID]['comid'] = $r->commID;
            $retArr[$r->ethCollPerCommLinkID]['communityname'] = $r->communityname;
            $retArr[$r->ethCollPerCommLinkID]['resident'] = $r->resident;
            $retArr[$r->ethCollPerCommLinkID]['commcomments'] = $r->commcomments;
        }
        return $retArr;
    }

    public function getPersonnelLangArr(){
        $retArr = Array();
        $sql = 'SELECT ecpl.ethCollPerLangLinkID, ecpl.langcomments, g.id, g.iso639P3code, g.`name` '.
            'FROM ethnocollperlanglink AS ecpl LEFT JOIN glottolog AS g ON ecpl.langID = g.id '.
            'WHERE ecpl.perID = '.$this->perid.' AND ecpl.collID = '.$this->collid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->ethCollPerLangLinkID]['glottocode'] = $r->id;
            $retArr[$r->ethCollPerLangLinkID]['isocode'] = $r->iso639P3code;
            $retArr[$r->ethCollPerLangLinkID]['langname'] = $r->name;
            $retArr[$r->ethCollPerLangLinkID]['langcomments'] = $r->langcomments;
        }
        return $retArr;
    }

    public function getCommunityLangArr(){
        $retArr = Array();
        $sql = 'SELECT ecll.ethCommLangLinkID, ecll.linktype, g.id, g.iso639P3code, g.`name` '.
            'FROM ethnocommlanglink AS ecll LEFT JOIN glottolog AS g ON ecll.langID = g.id '.
            'WHERE ecll.commID = '.$this->comid.' AND ecll.collID = '.$this->collid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $retArr[$r->ethCommLangLinkID]['glottocode'] = $r->id;
            $retArr[$r->ethCommLangLinkID]['isocode'] = $r->iso639P3code;
            $retArr[$r->ethCommLangLinkID]['langname'] = $r->name;
            $retArr[$r->ethCommLangLinkID]['linktype'] = $r->linktype;
        }
        return $retArr;
    }

    public function createCommunity($pArr){
        $valueStr = 'VALUES (';
        $valueStr .= ($this->cleanInStr($pArr['communityName'])?'"'.$this->cleanInStr($pArr['communityName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['country'])?'"'.$this->cleanInStr($pArr['country']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['stateProvince'])?'"'.$this->cleanInStr($pArr['stateProvince']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['county'])?'"'.$this->cleanInStr($pArr['county']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['municipality'])?'"'.$this->cleanInStr($pArr['municipality']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['decimalLatitude'])?'"'.$this->cleanInStr($pArr['decimalLatitude']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['decimalLongitude'])?'"'.$this->cleanInStr($pArr['decimalLongitude']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['elevationInMeters'])?'"'.$this->cleanInStr($pArr['elevationInMeters']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['languagecomments'])?'"'.$this->cleanInStr($pArr['languagecomments']).'") ':'null) ');
        $sql = 'INSERT INTO ethnocommunity(communityname,country,stateProvince,county,municipality,decimalLatitude,'.
            'decimalLongitude,elevationInMeters,languagecomments) '.$valueStr;
        if($this->conn->query($sql)){
            $this->comid = $this->conn->insert_id;
        }
    }

    public function saveCommunityChanges($pArr){
        $valueStr = 'SET ';
        $valueStr .= 'communityname='.($this->cleanInStr($pArr['communityName'])?'"'.$this->cleanInStr($pArr['communityName']).'"':'null').',';
        $valueStr .= 'country='.($this->cleanInStr($pArr['country'])?'"'.$this->cleanInStr($pArr['country']).'"':'null').',';
        $valueStr .= 'stateProvince='.($this->cleanInStr($pArr['stateProvince'])?'"'.$this->cleanInStr($pArr['stateProvince']).'"':'null').',';
        $valueStr .= 'county='.($this->cleanInStr($pArr['county'])?'"'.$this->cleanInStr($pArr['county']).'"':'null').',';
        $valueStr .= 'municipality='.($this->cleanInStr($pArr['municipality'])?'"'.$this->cleanInStr($pArr['municipality']).'"':'null').',';
        $valueStr .= 'decimalLatitude='.($this->cleanInStr($pArr['decimalLatitude'])?'"'.$this->cleanInStr($pArr['decimalLatitude']).'"':'null').',';
        $valueStr .= 'decimalLongitude='.($pArr['decimalLongitude']?$pArr['decimalLongitude']:'null').',';
        $valueStr .= 'elevationInMeters='.($this->cleanInStr($pArr['elevationInMeters'])?'"'.$this->cleanInStr($pArr['elevationInMeters']).'"':'null').',';
        $valueStr .= 'languagecomments='.($this->cleanInStr($pArr['languagecomments'])?'"'.$this->cleanInStr($pArr['languagecomments']).'"':'null');
        $sql = 'UPDATE ethnocommunity '.$valueStr.' '.
            'WHERE ethComID = '.$this->comid.' ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createCommLangLink($pArr){
        $valueStr = 'VALUES ('.$this->comid.','.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($pArr['addLanguageID']).'",';
        $valueStr .= '"'.$this->cleanInStr($pArr['addLangPrevalence']).'") ';
        $sql = 'INSERT INTO ethnocommlanglink(commID,collID,langID,linktype) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deleteCommLangLink($pArr){
        $sql = 'DELETE FROM ethnocommlanglink '.
            'WHERE ethCommLangLinkID IN('.implode(',',$pArr['llid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createPersonnel($pArr){
        $valueStr = 'VALUES (';
        $valueStr .= ($this->cleanInStr($pArr['personnelTitle'])?'"'.$this->cleanInStr($pArr['personnelTitle']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelFirstName'])?'"'.$this->cleanInStr($pArr['personnelFirstName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelLastName'])?'"'.$this->cleanInStr($pArr['personnelLastName']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelBirthYear'])?'"'.$this->cleanInStr($pArr['personnelBirthYear']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['birthYearEst'])?'"'.$this->cleanInStr($pArr['birthYearEst']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelSex'])?'"'.$this->cleanInStr($pArr['personnelSex']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelSexComments'])?'"'.$this->cleanInStr($pArr['personnelSexComments']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['birthCommID'])?'"'.$this->cleanInStr($pArr['birthCommID']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['priLanguageID'])?'"'.$this->cleanInStr($pArr['priLanguageID']).'") ':'null) ');
        $sql = 'INSERT INTO ethnopersonnel(title,firstname,lastname,birthyear,birthyearestimation,sex,'.
            'sexcomments,birthcommunityID,primarylanguageID) '.$valueStr;
        if($this->conn->query($sql)){
            $this->perid = $this->conn->insert_id;
        }
    }

    public function savePersonnelChanges($pArr){
        $valueStr = 'SET ';
        $valueStr .= 'title='.($this->cleanInStr($pArr['personnelTitle'])?'"'.$this->cleanInStr($pArr['personnelTitle']).'"':'null').',';
        $valueStr .= 'firstname='.($this->cleanInStr($pArr['personnelFirstName'])?'"'.$this->cleanInStr($pArr['personnelFirstName']).'"':'null').',';
        $valueStr .= 'lastname='.($this->cleanInStr($pArr['personnelLastName'])?'"'.$this->cleanInStr($pArr['personnelLastName']).'"':'null').',';
        $valueStr .= 'birthyear='.($this->cleanInStr($pArr['personnelBirthYear'])?'"'.$this->cleanInStr($pArr['personnelBirthYear']).'"':'null').',';
        $valueStr .= 'birthyearestimation='.($this->cleanInStr($pArr['birthYearEst'])?'"'.$this->cleanInStr($pArr['birthYearEst']).'"':'null').',';
        $valueStr .= 'sex='.($pArr['personnelSex']?'"'.$pArr['personnelSex'].'"':'null').',';
        $valueStr .= 'sexcomments='.($this->cleanInStr($pArr['personnelSexComments'])?'"'.$this->cleanInStr($pArr['personnelSexComments']).'"':'null').',';
        $valueStr .= 'birthcommunityID='.($this->cleanInStr($pArr['birthCommID'])?'"'.$this->cleanInStr($pArr['birthCommID']).'"':'null').',';
        $valueStr .= 'primarylanguageID='.($this->cleanInStr($pArr['priLanguageID'])?'"'.$this->cleanInStr($pArr['priLanguageID']).'"':'null');
        $sql = 'UPDATE ethnopersonnel '.$valueStr.' '.
            'WHERE ethPerID = '.$pArr['perid'].' ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createPersonnelLink($pArr){
        $roleStr = '';
        if($pArr['roleid']) $roleStr = json_encode($pArr['roleid']);
        $valueStr = 'VALUES ('.$this->perid.','.$this->collid.',';
        $valueStr .= ($roleStr?"'".$roleStr."',":'null,');
        $valueStr .= ($this->cleanInStr($pArr['personnelRoleComments'])?'"'.$this->cleanInStr($pArr['personnelRoleComments']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['projectCode'])?'"'.$this->cleanInStr($pArr['projectCode']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['defaultDisplay'])?'"'.$this->cleanInStr($pArr['defaultDisplay']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['residenceCommunity'])?'"'.$this->cleanInStr($pArr['residenceCommunity']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['residenceStatus'])?'"'.$this->cleanInStr($pArr['residenceStatus']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['birthCommunity'])?'"'.$this->cleanInStr($pArr['birthCommunity']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['commcomments'])?'"'.$this->cleanInStr($pArr['commcomments']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['targetLanguage'])?'"'.$this->cleanInStr($pArr['targetLanguage']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['targetLangQual'])?'"'.$this->cleanInStr($pArr['targetLangQual']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['secondLanguage'])?'"'.$this->cleanInStr($pArr['secondLanguage']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['secondLangQual'])?'"'.$this->cleanInStr($pArr['secondLangQual']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['thirdLanguage'])?'"'.$this->cleanInStr($pArr['thirdLanguage']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['thirdLangQual'])?'"'.$this->cleanInStr($pArr['thirdLangQual']).'") ':'null) ');
        $sql = 'INSERT INTO ethnocollperlink(perID,collID,rolearr,rolecomments,projectCode,defaultdisplay,residenceCommunity,'.
            'residenceStatus,birthCommunity,commcomments,targetLanguage,targetLangQual,secondLanguage,secondLangQual,thirdLanguage,'.
            'thirdLangQual) '.$valueStr;
        if($this->conn->query($sql)){
            $this->cplid = $this->conn->insert_id;
        }
    }

    public function savePersonnelLinkChanges($pArr){
        $roleStr = '';
        if($pArr['roleid']) $roleStr = json_encode($pArr['roleid']);
        $valueStr = 'SET ';
        $valueStr .= "rolearr=".($roleStr?"'".$roleStr."'":'null').',';
        $valueStr .= 'rolecomments='.($this->cleanInStr($pArr['personnelRoleComments'])?'"'.$this->cleanInStr($pArr['personnelRoleComments']).'"':'null').',';
        $valueStr .= 'projectCode='.($this->cleanInStr($pArr['projectCode'])?'"'.$this->cleanInStr($pArr['projectCode']).'"':'null').',';
        $valueStr .= 'defaultdisplay='.($this->cleanInStr($pArr['defaultDisplay'])?'"'.$this->cleanInStr($pArr['defaultDisplay']).'"':'null').',';
        $valueStr .= 'residenceCommunity='.($this->cleanInStr($pArr['residenceCommunity'])?'"'.$this->cleanInStr($pArr['residenceCommunity']).'"':'null').',';
        $valueStr .= 'residenceStatus='.($this->cleanInStr($pArr['residenceStatus'])?'"'.$this->cleanInStr($pArr['residenceStatus']).'"':'null').',';
        $valueStr .= 'birthCommunity='.($this->cleanInStr($pArr['birthCommunity'])?'"'.$this->cleanInStr($pArr['birthCommunity']).'"':'null').',';
        $valueStr .= 'commcomments='.($this->cleanInStr($pArr['commcomments'])?'"'.$this->cleanInStr($pArr['commcomments']).'"':'null').',';
        $valueStr .= 'targetLanguage='.($this->cleanInStr($pArr['targetLanguage'])?'"'.$this->cleanInStr($pArr['targetLanguage']).'"':'null').',';
        $valueStr .= 'targetLangQual='.($this->cleanInStr($pArr['targetLangQual'])?'"'.$this->cleanInStr($pArr['targetLangQual']).'"':'null').',';
        $valueStr .= 'secondLanguage='.($this->cleanInStr($pArr['secondLanguage'])?'"'.$this->cleanInStr($pArr['secondLanguage']).'"':'null').',';
        $valueStr .= 'secondLangQual='.($this->cleanInStr($pArr['secondLangQual'])?'"'.$this->cleanInStr($pArr['secondLangQual']).'"':'null').',';
        $valueStr .= 'thirdLanguage='.($this->cleanInStr($pArr['thirdLanguage'])?'"'.$this->cleanInStr($pArr['thirdLanguage']).'"':'null').',';
        $valueStr .= 'thirdLangQual='.($this->cleanInStr($pArr['thirdLangQual'])?'"'.$this->cleanInStr($pArr['thirdLangQual']).'"':'null');
        $sql = 'UPDATE ethnocollperlink '.$valueStr.' '.
            'WHERE ethCollPerLinkID = '.$pArr['cplid'].' ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createPerCommLink($pArr){
        $valueStr = 'VALUES ('.$this->perid.','.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($pArr['addCommID']).'",';
        $valueStr .= '"'.$this->cleanInStr($pArr['addCommunityRes']).'",';
        $valueStr .= ($this->cleanInStr($pArr['addCommunityComm'])?'"'.$this->cleanInStr($pArr['addCommunityComm']).'") ':'null) ');
        $sql = 'INSERT INTO ethnocollpercommlink(perID,collID,commID,resident,commcomments) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deletePerCommLink($pArr){
        $sql = 'DELETE FROM ethnocollpercommlink '.
            'WHERE ethCollPerCommLinkID IN('.implode(',',$pArr['clid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createPerLangLink($pArr){
        $valueStr = 'VALUES ('.$this->perid.','.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($pArr['addLanguageID']).'",';
        $valueStr .= ($this->cleanInStr($pArr['addLanguageComm'])?'"'.$this->cleanInStr($pArr['addLanguageComm']).'") ':'null) ');
        $sql = 'INSERT INTO ethnocollperlanglink(perID,collID,langID,langcomments) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deletePerLangLink($pArr){
        $sql = 'DELETE FROM ethnocollperlanglink '.
            'WHERE ethCollPerLangLinkID IN('.implode(',',$pArr['llid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deleteProjPerLink($pArr){
        $sql = 'DELETE ecpc.* FROM ethnocollpercommlink AS ecpc LEFT JOIN ethnocollperlink AS l ON ecpc.perID = l.perID AND ecpc.collID = l.collID '.
            'WHERE l.ethCollPerLinkID IN('.implode(',',$pArr['cplid']).') ';
        if($this->conn->query($sql)){
            $sql = 'DELETE ecpl.* FROM ethnocollperlanglink AS ecpl LEFT JOIN ethnocollperlink AS l ON ecpl.perID = l.perID AND ecpl.collID = l.collID '.
                'WHERE l.ethCollPerLinkID IN('.implode(',',$pArr['cplid']).') ';
            if($this->conn->query($sql)){
                $sql = 'DELETE FROM ethnocollperlink '.
                    'WHERE ethCollPerLinkID IN('.implode(',',$pArr['cplid']).') ';
                if($this->conn->query($sql)){
                    return true;
                }
                else return false;
            }
            else return false;
        }
        else return false;
    }

    public function createProjCommLink($pArr){
        $valueStr = 'VALUES ('.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($pArr['addCommID']).'") ';
        $sql = 'INSERT INTO ethnocollcommlink(collID,commID) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createProjRefLink($refid){
        $valueStr = 'VALUES ('.$this->collid.',';
        $valueStr .= '"'.$refid.'") ';
        $sql = 'INSERT INTO ethnocollreflink(collID,refid) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deleteProjCommLink($pArr){
        $sql = 'DELETE FROM ethnocollcommlink '.
            'WHERE ethCollCommLinkID IN('.implode(',',$pArr['cplid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deleteProjRefLink($pArr){
        $sql = 'DELETE FROM ethnocollreflink '.
            'WHERE ethCollRefLinkID IN('.implode(',',$pArr['crlid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function createProjLangLink($pArr){
        $valueStr = 'VALUES ('.$this->collid.',';
        $valueStr .= '"'.$this->cleanInStr($pArr['addLanguageID']).'",';
        $valueStr .= ($this->cleanInStr($pArr['addLanguageFileUrl'])?'"'.$this->cleanInStr($pArr['addLanguageFileUrl']).'",':'null,');
        $valueStr .= ($this->cleanInStr($pArr['addLanguageFileTopic'])?'"'.$this->cleanInStr($pArr['addLanguageFileTopic']).'") ':'null) ');
        $sql = 'INSERT INTO ethnocolllanglink(collID,langID,fileurl,filetopic) '.$valueStr;
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function deleteProjLangLink($pArr){
        $sql = 'DELETE FROM ethnocolllanglink '.
            'WHERE ethCollLangLinkID IN('.implode(',',$pArr['cplid']).') ';
        if($this->conn->query($sql)) return true;
        else return false;
    }

    public function checkCommName($name,$id){
        $result = '';
        $sql = 'SELECT ethComID FROM ethnocommunity WHERE communityname = "'.$name.'" ';
        if($id) $sql .= 'AND ethComID <> '.$id.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $result = $r->ethComID;
        }
        return $result;
    }

    public function checkPerName($pertitle,$perfn,$perln,$perid){
        $result = '';
        $sql = 'SELECT ethPerID FROM ethnopersonnel WHERE firstname = "'.$perfn.'" ';
        if($pertitle) $sql .= 'AND title = "'.$pertitle.'" ';
        if($perln) $sql .= 'AND lastname = "'.$perln.'" ';
        if($perid) $sql .= 'AND ethPerID <> '.$perid.' ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $result = $r->ethPerID;
        }
        return $result;
    }

    protected function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }
}
?>

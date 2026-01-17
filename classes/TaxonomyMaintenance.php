<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/utilities/TaxonomyUtil.php');

class TaxonomyMaintenance extends Manager{

	private $taxAuthID = 1;
	private $nodeTid = '';
	private $nodeName = '';
	private $rankArr = array();

	function __construct() {
		parent::__construct(null,'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getTaxonomyReport(){
		$retArr = array();
		$retArr['orphanedTaxa'] = $this->getOrphanedTaxaCount();
		$retArr['mismatchedFamilies'] = $this->getMismatchedFamilyCount();
		$retArr['illegalParentRankid'] = $this->getIllegalParentRankidCount();
		$retArr['acceptedNonAcceptedParent'] = $this->getAcceptedNonAcceptedParentCount();
		$retArr['nonAcceptedLinkedToNonAccepted'] = $this->getNonAcceptedLinkedToNonAcceptedCount();
		$retArr['infraspIssues'] = $this->getMislinkedInfraspecificCount();
		$retArr['speciesIssues'] = $this->getMislinkedSpeciesCount();
		$retArr['generaIssues'] = $this->getMislinkedGeneraCount();
		return $retArr;
	}

	//Taxa entered into taxa table, but without hierarchy or acceptance defined within taxstatus table
	public function getOrphanedTaxaCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(*) as cnt ' . $this->getOrphanedSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->taxAuthID);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getOrphanedTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT tid, sciname, author, rankid ' . $this->getOrphanedSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->taxAuthID);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$retArr[$r->tid]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getOrphanedSqlFrag(){
		$sql = 'FROM taxa WHERE tid NOT IN(SELECT tid FROM taxstatus WHERE taxauthid = ?)';
		return $sql;
	}

	//Taxa with mismatched families (family quick lookup field mismatched with family defined within hierarchy)
	public function getMismatchedFamilyCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) as cnt ' . $this->getMismatchedFamilySqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getMismatchedFamilyTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, p.family, ts.family as familyIncorrect ' . $this->getMismatchedFamilySqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$retArr[$r->tid]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid]['rankid'] = $r->rankid;
					$retArr[$r->tid]['family'] = $this->cleanInStr($r->family);
					$retArr[$r->tid]['familyIncorrect'] = $this->cleanInStr($r->familyIncorrect);
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getMismatchedFamilySqlFrag(){
		$sql = 'FROM taxstatus ts INNER JOIN taxaenumtree e ON ts.tid = e.tid
			INNER JOIN taxa t ON ts.tid = t.tid
			INNER JOIN taxa p ON e.parenttid = p.tid
			WHERE e.taxauthid = ? AND ts.taxauthid = ? AND p.rankid = 140 AND ts.family != p.sciname AND e.parentTid = ?';
		return $sql;
	}

	//Taxa whose direct parent is of an equal or higher rankid
	public function getIllegalParentRankidCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getIllegalParentRankidSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getIllegalParentRankidTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, p.tid AS parentTid, p.sciname as parent, p.rankID AS parentRankID ' . $this->getIllegalParentRankidSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$retArr[$r->tid]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid]['parentTid'] = $r->parentTid;
					$retArr[$r->tid]['parent'] = $r->parent;
					$retArr[$r->tid]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getIllegalParentRankidSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
		INNER JOIN taxa p ON ts.parenttid = p.tid
		INNER JOIN taxaenumtree e ON t.tid = e.tid
		WHERE ts.taxauthid = ? AND t.rankid < p.rankid AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Accepted taxa with a non-accepted parent
	public function getAcceptedNonAcceptedParentCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getAcceptedNonAcceptedParentSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getAcceptedNonAcceptedParentTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, p.tid AS parentTid, p.sciname AS parent, p.rankid AS parentRankID ' . $this->getAcceptedNonAcceptedParentSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$retArr[$r->tid]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid]['parentTid'] = $r->parentTid;
					$retArr[$r->tid]['parent'] = $this->cleanInStr($r->parent);
					$retArr[$r->tid]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getAcceptedNonAcceptedParentSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			INNER JOIN taxstatus pts ON ts.parentTid = pts.tid
			INNER JOIN taxa p ON pts.tid = p.tid
			INNER JOIN taxaenumtree e ON t.tid = e.tid
			WHERE ts.taxauthid = ? AND pts.taxauthid = ? AND ts.tid = ts.tidAccepted AND pts.tid != pts.tidAccepted AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Non-accepted taxa linked to another non-accepted taxon as the accepted taxon
	public function getNonAcceptedLinkedToNonAcceptedCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getNonAcceptedLinkedToNonAcceptedSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getNonAcceptedLinkedToNonAcceptedTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, p.tid AS parentTid, p.sciname AS parent, p.rankid AS parentRankID ' . $this->getNonAcceptedLinkedToNonAcceptedSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$retArr[$r->tid]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid]['parentTid'] = $r->parentTid;
					$retArr[$r->tid]['parent'] = $r->parent;
					$retArr[$r->tid]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getNonAcceptedLinkedToNonAcceptedSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			INNER JOIN taxstatus a ON ts.tidAccepted = a.tid
			INNER JOIN taxaenumtree e ON t.tid = e.tid
			WHERE ts.taxauthid = ? AND a.taxauthid = ? AND ts.tid != ts.tidAccepted AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Infraspecific taxa linked to a parent of a higher rank of species (rankid < 220)
	public function getMislinkedInfraspecificCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getMislinkedInfraspecificSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getMislinkedInfraspecificTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, ts.tidaccepted, p.tid AS parentTid, p.sciname AS parent, p.rankid AS parentRankid ' . $this->getMislinkedInfraspecificSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$acceptance = 1;
					if($r->tid != $r->tidaccepted) $acceptance = 0;
					$retArr[$r->tid][$acceptance]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid][$acceptance]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid][$acceptance]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid][$acceptance]['parentTid'] = $r->parentTid;
					$retArr[$r->tid][$acceptance]['parent'] = $r->parent;
					$retArr[$r->tid][$acceptance]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getMislinkedInfraspecificSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			INNER JOIN taxstatus pts ON ts.parentTid = pts.tid
			INNER JOIN taxa p ON pts.tid = p.tid
			INNER JOIN taxaenumtree e ON t.tid = e.tid
			WHERE ts.taxauthid = ? AND pts.taxauthid = ? AND t.rankid > 220 AND p.rankid < 220 AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Species ranked taxa (rankid = 220) that are linked to a parent of a rank < genus rank
	public function getMislinkedSpeciesCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getMislinkedSpeciesSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getMislinkedSpeciesTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, ts.tidaccepted, p.tid AS parentTid, p.sciname AS parent, p.rankid AS parentRankid ' . $this->getMislinkedSpeciesSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$acceptance = 1;
					if($r->tid != $r->tidaccepted) $acceptance = 0;
					$retArr[$r->tid][$acceptance]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid][$acceptance]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid][$acceptance]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid][$acceptance]['parentTid'] = $r->parentTid;
					$retArr[$r->tid][$acceptance]['parent'] = $r->parent;
					$retArr[$r->tid][$acceptance]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getMislinkedSpeciesSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			INNER JOIN taxstatus pts ON ts.parentTid = pts.tid
			INNER JOIN taxa p ON pts.tid = p.tid
			INNER JOIN taxaenumtree e ON t.tid = e.tid
			WHERE ts.taxauthid = ? AND pts.taxauthid = ? AND t.rankid = 220 AND p.rankid < 180 AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Genera that are linked to a parent of a rank < family rank (this might not be a problem, but should be checked)
	public function getMislinkedGeneraCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(t.tid) AS cnt ' . $this->getMislinkedGeneraSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			$stmt->bind_result($retCnt);
			$stmt->fetch();
			$stmt->close();
		}
		return $retCnt;
	}

	public function getMislinkedGeneraTaxa(){
		$retArr = array();
		$this->setRankArr();
		$sql = 'SELECT t.tid, t.sciname, t.author, t.rankid, ts.tidaccepted, p.tid AS parentTid, p.sciname AS parent, p.rankid AS parentRankid ' . $this->getMislinkedGeneraSqlFrag();
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iiii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID, $this->nodeTid);
			$stmt->execute();
			if($rs = $stmt->get_result()){
				while($r = $rs->fetch_object()){
					$acceptance = 1;
					if($r->tid != $r->tidaccepted) $acceptance = 0;
					$retArr[$r->tid][$acceptance]['sciname'] = $this->cleanInStr($r->sciname);
					$retArr[$r->tid][$acceptance]['author'] = $this->cleanInStr($r->author);
					$retArr[$r->tid][$acceptance]['rankid'] = $r->rankid;
					if($this->rankArr[$r->rankid]) $retArr[$r->tid]['rankName'] = $this->rankArr[$r->rankid];
					$retArr[$r->tid][$acceptance]['parentTid'] = $r->parentTid;
					$retArr[$r->tid][$acceptance]['parent'] = $r->parent;
					$retArr[$r->tid][$acceptance]['parentRankID'] = $r->parentRankID;
				}
				$rs->free();
			}
			$stmt->close();
		}
		return $retArr;
	}

	private function getMislinkedGeneraSqlFrag(){
		$sql = 'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
			INNER JOIN taxstatus pts ON ts.parentTid = pts.tid
			INNER JOIN taxa p ON pts.tid = p.tid
			INNER JOIN taxaenumtree e ON t.tid = e.tid
			WHERE ts.taxauthid = ? AND pts.taxauthid = ? AND t.rankid = 180 AND p.rankid < 140 AND e.taxAuthID = ? AND e.parentTid = ?';
		return $sql;
	}

	//Data repair functions
	public function synchronizeFamilyQuickLookup(){
		$status = false;
		//Delete enumeration index for mismatched taxa
		$sql = 'DELETE e.*
			FROM taxstatus ts INNER JOIN taxaenumtree e on ts.tid = e.tid
			INNER JOIN taxa p on e.parenttid = p.tid
			WHERE e.taxauthid = ? and p.rankid = 140 and ts.taxauthid = ? AND ts.family != p.sciname';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $this->taxAuthID, $this->taxAuthID);
			$stmt->execute();
			$stmt->close();
		}

		//Reset enumeration index for all taxa
		TaxonomyUtil::buildHierarchyEnumTree($this->conn, $this->taxAuthID);

		//Reset family quick lookup field based on hierarchy
		$sql = 'UPDATE taxstatus ts INNER JOIN taxaenumtree e ON ts.tid = e.tid
			INNER JOIN taxa p ON e.parenttid = p.tid
			SET ts.family = p.sciname
			WHERE ts.taxauthid = ? AND e.taxauthid = ? AND p.rankid = 140 AND ts.taxauthid = ? AND ts.family != p.sciname';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $this->taxAuthID, $this->taxAuthID, $this->taxAuthID);
			$stmt->execute();
			$status = $stmt->affected_rows;
			$stmt->close();
		}
		return $status;
	}

	public function pruneBadParentNodes(){
		$status = false;
		$taxaArr = $this->getIllegalParentRankidTaxa();
		foreach($taxaArr as $tid => $taxaArr){
			$this->pruneTaxonNodes($tid, $taxaArr['rankid']);
		}
		return $status;
	}

	private function pruneTaxonNodes($childTid, $childRankID){
		$remapTid = 0;
		$parentTid = 0;
		$parentRankID = 0;
		$cnt = 0;
		do{
			if(!$childRankID) return false;
			$sql = 'SELECT p.tid, p.rankid FROM taxstatus ts INNER JOIN taxa p ON ts.parentTid = p.tid WHERE ts.taxauthid = ? AND ts.tid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('ii', $this->taxAuthID, $childTid);
				$stmt->execute();
				$stmt->bind_result($parentTid, $parentRankID);
				$stmt->fetch();
				$stmt->close();
				if($remapTid){
					$this->remapNode($parentTid, $childTid);
					$remapTid = 0;
				}
				if($childTid == $parentTid) break;
				if($parentRankID >= $childRankID){
					$remapTid = $childTid;
				}
				else $childRankID = $parentRankID;
				$childTid = $parentTid;
			}
			$cnt++;
		}
		while($parentRankID < 11 || $cnt > 20);
	}

	private function remapNode($parentTid, $childTid){
		$sql = 'UPDATE taxstatus SET parentTid = ? WHERE taxAuthID = ? AND tid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('iii', $parentTid, $this->taxAuthID, $childTid);
			$stmt->execute();
			$stmt->close();
		}
	}

	public function rebuildHierarchyEnumTree(){
		TaxonomyUtil::rebuildHierarchyEnumTree($this->conn);
	}

	//Data set functions
	private function setRankArr(){
		if(!$this->rankArr && $this->nodeTid){
			$sql = 'SELECT u.rankID, u.rankName
				FROM taxonunits u INNER JOIN taxa t ON u.kingdomName = t.sciname
				INNER JOIN taxaenumtree e ON t.tid = e.parentTid
				WHERE e.tid = ? AND e.taxAuthId = ? AND t.rankid = 10';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('ii', $this->nodeTid, $this->taxAuthID);
				$stmt->execute();
				$rankID = 0;
				$rankName = '';
				$stmt->bind_result($rankID, $rankName);
				while($stmt->fetch()){
					$this->rankArr[$rankID] = $rankName;
				}
				$stmt->close();
			}
		}
	}

	public function getNodeArr(){
		$retArr = array();
		$sql = 'SELECT tid, sciname, rankid FROM taxa WHERE rankid <= 140 ORDER BY rankid, sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->sciname . ' (rankid: ' . $r->rankid . ')';
		}
		return $retArr;
	}

	//Setters and getters
	public function setTaxAuthID($authID){
		$this->taxAuthID = filter_var($authID, FILTER_SANITIZE_NUMBER_INT);
	}

	public function setNode($node){
		if(strpos($node, '-')){
			$nodeArr = explode('-', $node);
			$this->nodeTid = filter_var($nodeArr[0], FILTER_SANITIZE_NUMBER_INT);
			$this->nodeName = $nodeArr[1];
		}
	}
}
?>
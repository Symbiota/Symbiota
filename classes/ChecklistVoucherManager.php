<?php
include_once($SERVER_ROOT.'/classes/ChecklistVoucherAdmin.php');

class ChecklistVoucherManager extends ChecklistVoucherAdmin{

	private $tid;
	private $taxonName;

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getChecklistData(){
		$checklistData = Array();
		if($this->tid && $this->clid){
			$sql = 'SELECT t.SciName, cllink.Habitat, cllink.Abundance, cllink.Notes, cllink.internalnotes, cllink.source, cllink.familyoverride, cl.Name, cl.type, cl.locality '.
				'FROM fmchecklists cl INNER JOIN fmchklsttaxalink cllink ON cl.CLID = cllink.CLID '.
				'INNER JOIN taxa t ON cllink.TID = t.TID '.
				'WHERE (cllink.TID = '.$this->tid.') AND (cllink.CLID = '.$this->clid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$checklistData['habitat'] = $this->cleanOutStr($row->Habitat);
				$checklistData['abundance'] = $this->cleanOutStr($row->Abundance);
				$checklistData['notes'] = $this->cleanOutStr($row->Notes);
				$checklistData['internalnotes'] = $this->cleanOutStr($row->internalnotes);
				$checklistData['source'] = $this->cleanOutStr($row->source);
				$checklistData['familyoverride'] = $this->cleanOutStr($row->familyoverride);
				$checklistData['cltype'] = $row->type;
				$checklistData['locality'] = $row->locality;
				if(!$this->clName) $this->clName = $this->cleanOutStr($row->Name);
				if(!$this->taxonName) $this->taxonName = $this->cleanOutStr($row->SciName);
			}
			$result->free();
		}
		return $checklistData;
	}

	//Editing functions
	public function editClData($postArr){
		$status = false;
		$inventoryManager = new ImInventories();
		$clTaxaID = $this->getClTaxaID($this->tid);
		$inventoryManager->setClTaxaID($clTaxaID);
		$status = $inventoryManager->updateChecklistTaxaLink($postArr);
		if(!$status) $this->errorMessage = $inventoryManager->getErrorMessage();
		return $status;
	}

	public function remapTaxon($targetTid, $rareLocality = ''){
		$statusStr = false;
		if(is_numeric($targetTid)){
			$inventoryManager = new ImInventories();
			$clTaxaID = $this->getClTaxaID($this->tid);
			$inventoryManager->setClTaxaID($clTaxaID);
			//First transfer taxa that
			$inputArr = array('tid' => $targetTid);
			if($inventoryManager->updateChecklistTaxaLink($inputArr)){
				$this->tid = $targetTid;
				$this->taxonName = '';
				$statusStr = true;
			}
			else{
				if(!$inventoryManager->getErrorMessage()){
					//Transferred failed due to target name already exiting within checklist
					$targetArr = array();
					$sqlTarget = 'SELECT clTaxaID, habitat, abundance, notes, internalNotes, source, nativity FROM fmchklsttaxalink WHERE (tid = '.$targetTid.') AND (clid = '.$this->clid.')';
					$rsTarget = $this->conn->query($sqlTarget);
					if($row = $rsTarget->fetch_assoc()){
						$targetArr = $row;
						$clTaxaIDTarget = $row['clTaxaID'];
						unset($row['clTaxaID']);
						if(!$inventoryManager->updateChecklistVouchersByClTaxaID(array('clTaxaID' => $clTaxaIDTarget))){
							$this->errorMessage = 'ERROR transferring vouchers during taxon transfer: ' . $this->conn->error;
						}
						//Delete all Vouchers that didn't transfer because they were already linked to target name
						if(!$inventoryManager->deleteChecklistVouchersByClTaxaID()){
							$this->errorMessage = 'ERROR removing vouchers during taxon transfer: ' . $this->conn->error;
						}
					}
					$rsTarget->free();

					//Merge chklsttaxalink data
					//Harvest source (unwanted) chklsttaxalink data
					$sourceArr = array();
					$sqlSourceCl = 'SELECT habitat, abundance, notes, internalNotes, source, nativity FROM fmchklsttaxalink WHERE (clTaxaID = '.$clTaxaID.')';
					$rsSourceCl =  $this->conn->query($sqlSourceCl);
					if($row = $rsSourceCl->fetch_object()){
						$sourceArr = $row;
					}
					$rsSourceCl->free();
					//Transfer source chklsttaxalink data to target record

					foreach($sourceArr as $sourceField => $sourceValue){
						$newValue = $targetArr[$sourceField];
						if($newValue && $sourceValue) $newValue .= '; ';
						$newValue .= $sourceValue;
						$targetArr[$sourceField] = trim($newValue , '; ');
					}
					if($inventoryManager->deleteChecklistTaxaLink()){
						$inventoryManager->setClTaxaID($clTaxaIDTarget);
						$inventoryManager->updateChecklistTaxaLink($targetArr);
						$this->tid = $targetTid;
						$this->taxonName = '';
						$statusStr = true;
					}
					else $this->errorMessage = 'ERROR removing taxon during taxon transfer: '.$this->conn->error;
				}
			}
			if($rareLocality){
				$inventoryManager->removeStateLocalitySecurityByTid($rareLocality, $this->tid);
			}
		}
		return $statusStr;
	}

	public function deleteTaxon($rareLocality = ''){
		$status = false;
		$inventoryManager = new ImInventories();
		$clTaxaID = $this->getClTaxaID($this->tid);
		$inventoryManager->setClTaxaID($clTaxaID);
		//First delete all linked voucehrs
		$status = $inventoryManager->deleteChecklistVouchersByClTaxaID();
		if(!$status) $this->errorMessage = $inventoryManager->getErrorMessage();
		//Then delete checklist taxa linkage
		if($status){
			$status = $inventoryManager->deleteChecklistTaxaLink();
			if(!$status) $this->errorMessage = $inventoryManager->getErrorMessage();
		}
		if($rareLocality){
			$inventoryManager->removeStateLocalitySecurityByTid($rareLocality, $this->tid);
		}
		return $status;
	}

	//Voucher functions
	public function getVoucherData(){
		$voucherData = Array();
		if($this->tid && $this->clid){
			if($clTaxaID = $this->getClTaxaID($this->tid)){
				$sql = 'SELECT v.voucherID, o.occid, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, o.catalognumber, o.sciname, o.eventdate, v.notes, v.editornotes
					FROM fmvouchers v INNER JOIN omoccurrences o ON v.occid = o.occid
					WHERE (v.clTaxaID = '.$clTaxaID.') ';
				$sql .= OccurrenceUtil::appendFullProtectionSQL();
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$voucherData[$r->voucherID]['occid'] = $r->occid;
					$voucherData[$r->voucherID]['collector'] = $r->collector;
					$voucherData[$r->voucherID]['catalognumber'] = $r->catalognumber;
					$voucherData[$r->voucherID]['sciname'] = $r->sciname;
					$voucherData[$r->voucherID]['eventdate'] = $r->eventdate;
					$voucherData[$r->voucherID]['notes'] = $r->notes;
					$voucherData[$r->voucherID]['editornotes'] = $r->editornotes;
				}
				$rs->free();
				$voucherData = $this->cleanOutArray($voucherData);
			}
		}
		return $voucherData;
	}

	public function editVoucher($voucherID, $notes, $editorNotes){
		$status = false;
		if(is_numeric($voucherID)){
			$inventoryManager = new ImInventories();
			$inventoryManager->setVoucherID($voucherID);
			$inputArr = array('notes' => $notes, 'editorNotes' => $editorNotes);
			$status = $inventoryManager->updateChecklistVoucher($inputArr);
			if(!$status) $this->errorMessage = $inventoryManager->getErrorMessage();
		}
		return $status;
	}

	private function addVoucherRecord($vOccId, $vNotes, $vEditNotes){
		//Checklist-taxon combination already exists
		$sql = 'SELECT DISTINCT o.occid, ctl.tid '.
			'FROM omoccurrences o INNER JOIN taxstatus ts1 ON o.TidInterpreted = ts1.tid '.
			'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
			'INNER JOIN fmchklsttaxalink ctl ON ts2.tid = ctl.tid '.
			'WHERE (ctl.clid = '.$this->clid.') AND (o.occid = '.$vOccId.') AND ts1.taxauthid = 1 AND ts2.taxauthid = 1 '.
			'LIMIT 1';
		$rs = $this->conn->query($sql);
		if($row = $rs->fetch_object()){
			if($clTaxaID = $this->getClTaxaID($row->tid)){
				if($this->insertVoucher($clTaxaID, $row->occid, $vNotes, $vEditNotes)){
					$this->tid = $row->tid;
				}
				else{
					$this->errorMessage = 'ERROR - Voucher insert failed: ' . $this->conn->error;
					return false;
				}
			}
			$rs-free();
			return true;
		}
		$this->errorMessage =  'ERROR: Neither the target taxon nor a sysnonym is present in this checklists. Taxon needs to be added.';
		return false;
	}

	//Setters and getters
	public function setTid($t){
		if(is_numeric($t)){
			$this->tid = $t;
		}
	}

	public function getTid(){
		return $this->tid;
	}

	public function getTaxonName(){
		return $this->taxonName;
	}
}
?>
<?php
include_once('Manager.php');
include_once('Person.php');
include_once('Encryption.php');
@include_once 'Mail.php';

class ProfileManager extends Manager{

	private $rememberMe = false;
	private $uid;
	private $userName;
	private $displayName;
	private $token;
	private $authSql;
	private $errorStr;

	public function __construct($connType = 'readonly'){
		parent::__construct(null, $connType);
	}

 	public function __destruct(){
 		parent::__destruct();
	}

	private function resetConnection(){
		$this->conn = MySQLiConnectionFactory::getCon('write');
	}

	public function reset(){
		$domainName = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_URL);
		if($domainName == 'localhost') $domainName = false;
		setcookie('SymbiotaCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?$GLOBALS['CLIENT_ROOT']:'/'), $domainName, false, true);
		setcookie('SymbiotaCrumb', '', time() - 3600, ($GLOBALS['CLIENT_ROOT']?$GLOBALS['CLIENT_ROOT']:'/'));
		unset($_SESSION['userrights']);
		unset($_SESSION['userparams']);
	}

	public function authenticate($pwdStr = ''){
		$authStatus = false;
		unset($_SESSION['userrights']);
		unset($_SESSION['userparams']);
		if($this->userName){
			if(!$this->authSql){
				$this->authSql = 'SELECT u.uid, u.firstname, u.lastname FROM users u INNER JOIN userlogin l ON u.uid = l.uid '.
					'WHERE ((l.username = "'.$this->userName.'") OR (u.email = "'.$this->userName.'")) ';
				if($pwdStr) $this->authSql .= 'AND (l.password = PASSWORD("'.$this->cleanInStr($pwdStr).'")) ';
			}
			$result = $this->conn->query($this->authSql);
			if($row = $result->fetch_object()){
				$this->uid = $row->uid;
				$this->displayName = $row->firstname;
				if(strlen($this->displayName) > 15) $this->displayName = $this->userName;
				if(strlen($this->displayName) > 15) $this->displayName = substr($this->displayName,0,10).'...';

				$authStatus = true;
				$this->reset();
				$this->setUserRights();
				$this->setUserParams();
				if($this->rememberMe) $this->setTokenCookie();
				if(!isset($GLOBALS['SYMB_UID']) || !$GLOBALS['SYMB_UID']){
					$this->resetConnection();
					$sql = 'UPDATE userlogin SET lastlogindate = NOW() WHERE (uid = '.$this->uid.')';
					$this->conn->query($sql);
				}
			}
		}
		return $authStatus;
	}

	private function setTokenCookie(){
		$tokenArr = Array();
		if(!$this->token){
			$this->createToken();
		}
		if($this->token){
			$tokenArr[] = $this->userName;
			$tokenArr[] = $this->token;
			$cookieExpire = time() + 60 * 60 * 24 * 30;
			$domainName = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_URL);
			if ($domainName == 'localhost') $domainName = false;
			setcookie('SymbiotaCrumb', Encryption::encrypt(json_encode($tokenArr)), $cookieExpire, ($GLOBALS['CLIENT_ROOT'] ? $GLOBALS['CLIENT_ROOT'] : '/'), $domainName, false, true);
		}
	}

	public function getPerson(){
		$sqlStr = 'SELECT u.uid, u.firstname, u.lastname, u.title, u.institution, u.department, u.address, u.city, u.state, u.zip, u.country, u.phone, u.email, '.
			'u.url, u.guid, u.biography, u.ispublic, u.notes, ul.username, ul.lastlogindate '.
			'FROM users u LEFT JOIN userlogin ul ON u.uid = ul.uid '.
			'WHERE (u.uid = '.$this->uid.')';
		$person = new Person();
		//echo $sqlStr;
		$badUserNameArr = array();
		$result = $this->conn->query($sqlStr);
		if($row = $result->fetch_object()){
			$person->setUid($row->uid);
			$person->setUserName($row->username);
			$person->setLastLoginDate($row->lastlogindate);
			$person->setFirstName($row->firstname);
			$person->setLastName($row->lastname);
			$person->setTitle($row->title);
			$person->setInstitution($row->institution);
			$person->setDepartment($row->department);
			$person->setCity($row->city);
			$person->setState($row->state);
			$person->setZip($row->zip);
			$person->setCountry($row->country);
			$person->setPhone($row->phone);
			$person->setEmail($row->email);
			$person->setGUID($row->guid);
			$this->setUserTaxonomy($person);
			while($row = $result->fetch_object()){
				//Old code allowed folks to maintain more than one login names. This code will make sure the most recently active one is used
				if($row->lastlogindate && (!$person->getLastLoginDate() || $row->lastlogindate > $person->getLastLoginDate())){
					$badUserNameArr[] = $person->getUserName();
					$person->setUserName($row->username);
					$person->setLastLoginDate($row->lastlogindate);
				}
				else{
					$badUserNameArr[] = $row->userName;
				}
			}
		}
		if($badUserNameArr){
			//Delete the none active logins
			$sql = 'DELETE FROM userlogin WHERE username IN("'.implode('","',$badUserNameArr).'")';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR removing extra logins: '.$this->conn->error;
			}
		}
		$result->free();
		return $person;
	}

	public function updateProfile($person){
		$success = false;
		if($person){
			$this->resetConnection();
			$sql = 'UPDATE users SET '.
				'firstname = '.($person->getFirstName()?'"'.$this->cleanInStr($person->getFirstName()).'"':'NULL').','.
				'lastname = '.($person->getLastName()?'"'.$this->cleanInStr($person->getLastName()).'"':'NULLL').','.
				'title = '.($person->getTitle()?'"'.$this->cleanInStr($person->getTitle()).'"':'NULL').','.
				'institution = '.($person->getInstitution()?'"'.$this->cleanInStr($person->getInstitution()).'"':'NULL').','.
				'department = '.($person->getDepartment()?'"'.$this->cleanInStr($person->getDepartment()).'"':'NULL').','.
				'city = '.($person->getCity()?'"'.$this->cleanInStr($person->getCity()).'"':'NULL').','.
				'state = '.($person->getState()?'"'.$this->cleanInStr($person->getState()).'"':'NULL').','.
				'zip = '.($person->getZip()?'"'.$this->cleanInStr($person->getZip()).'"':'NULL').','.
				'country = '.($person->getCountry()?'"'.$this->cleanInStr($person->getCountry()).'"':'NULL').','.
				'phone = '.($person->getPhone()?'"'.$this->cleanInStr($person->getPhone()).'"':'NULL').','.
				'email = '.($person->getEmail()?'"'.$this->cleanInStr($person->getEmail()).'"':'NULL').','.
				'guid = '.($person->getGUID()?'"'.$this->cleanInStr($person->getGUID()).'"':'NULL').' '.
				'WHERE (uid = '.$person->getUid().')';
			$success = $this->conn->query($sql);
		}
		return $success;
	}

	public function deleteProfile($reset = 0){
		$success = false;
		if($this->uid){
			$this->resetConnection();
			$sql = 'DELETE FROM users WHERE (uid = '.$this->uid.')';
			$success = $this->conn->query($sql);
		}
		if($reset) $this->reset();
		return $success;
	}

	public function changePassword ($newPwd, $oldPwd = "", $isSelf = 0) {
		$success = false;
		if($newPwd){
			$this->resetConnection();
			if($isSelf){
				$sqlTest = 'SELECT ul.uid FROM userlogin ul WHERE (ul.uid = '.$this->uid.') AND (ul.password = PASSWORD("'.$this->cleanInStr($oldPwd).'"))';
				$rsTest = $this->conn->query($sqlTest);
				if(!$rsTest->num_rows) return false;
			}
			$sql = 'UPDATE userlogin ul SET ul.password = PASSWORD("'.$this->cleanInStr($newPwd).'") WHERE (uid = '.$this->uid.')';
			$successCnt = $this->conn->query($sql);
			if($successCnt > 0) $success = true;
		}
		return $success;
	}

	public function resetPassword($un){
		$newPassword = $this->generateNewPassword();
		$status = false;
		if($un && $newPassword){
			$uid = 0;
			$email = '';
			$un = $this->cleanInStr($un);
			$sql = 'SELECT u.uid, u.email FROM users u INNER JOIN userlogin l ON u.uid = l.uid WHERE (l.username = "'.$un.'") OR (u.email = "'.$un.'")';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$uid = $row->uid;
				$email = $row->email;
			}
			$rs->free();

			if($uid){
				$subject = 'RE: Password reset';
				$serverPath = $this->getDomain().$GLOBALS['CLIENT_ROOT'];
				$from = '';
				if (array_key_exists("SYSTEM_EMAIL", $GLOBALS) && !empty($GLOBALS["SYSTEM_EMAIL"])){
					$from = 'Reset Request <'.$GLOBALS["SYSTEM_EMAIL"].'>';
				}
				$body = 'Your '.$GLOBALS['DEFAULT_TITLE'].' password has been reset to: '.$newPassword.'<br/><br/> '.
					'After logging in, you can change your password by clicking on the My Profile link within the site menu and then selecting the Edit Profile tab. '.
					'If you have problems, contact the System Administrator: '.$GLOBALS['ADMIN_EMAIL'].'<br/><br/>'.
					'Data portal: <a href="'.$serverPath.'">'.$serverPath.'</a><br/>'.
					'Direct link to your user profile: <a href="'.$serverPath.'/profile/viewprofile.php?tabindex=2">'.$serverPath.'/profile/viewprofile.php</a>';

				$status = $this->sendEmail($email, $subject, $body, $from);
				if($status){
					$this->resetConnection();
					$sql = 'UPDATE userlogin SET password = PASSWORD("'.$this->cleanInStr($newPassword).'") WHERE (uid = '.$uid.')';
					if($this->conn->query($sql)) $status = $email;
					else{
						$status = false;
						$this->errorStr = $this->conn->error;
					}
				}
			}
		}
		else{
			return false;
		}
		return $status;
	}

	private function generateNewPassword(){
		// generate new random password
		$newPassword = "";
		$alphabet = str_split("0123456789abcdefghijklmnopqrstuvwxyz");
		for($i = 0; $i<8; $i++) {
			$newPassword .= $alphabet[rand(0,count($alphabet)-1)];
		}
		return $newPassword;
	}

	public function register($postArr){
		$status = false;

		$firstName = $postArr['firstname'];
		$lastName = $postArr['lastname'];
		if($postArr['institution'] && !trim(strpos($postArr['institution'],' ')) && preg_match('/[a-z]+[A-Z]+[a-z]+[A-Z]+/',$postArr['institution'])){
			if($postArr['title'] && !trim(strpos($postArr['title'],' ')) && preg_match('/[a-z]+[A-Z]+[a-z]+[A-Z]+/',$postArr['title'])){
				return false;
			}
		}

		$person = new Person();
		$person->setPassword($postArr['pwd']);
		$person->setUserName($this->userName);
		$person->setFirstName($firstName);
		$person->setLastName($lastName);
		$person->setTitle($postArr['title']);
		$person->setInstitution($postArr['institution']);
		$person->setCity($postArr['city']);
		$person->setState($postArr['state']);
		$person->setZip($postArr['zip']);
		$person->setCountry($postArr['country']);
		$person->setEmail($postArr['emailaddr']);
		$person->setGUID($postArr['guid']);

		//Add to users table
		$fields = 'INSERT INTO users (';
		$values = 'VALUES (';
		$fields .= 'firstname ';
		$values .= '"'.$this->cleanInStr($person->getFirstName()).'"';
		$fields .= ', lastname';
		$values .= ', "'.$this->cleanInStr($person->getLastName()).'"';
		if($person->getTitle()){
			$fields .= ', title';
			$values .= ', "'.$this->cleanInStr($person->getTitle()).'"';
		}
		if($person->getInstitution()){
			$fields .= ', institution';
			$values .= ', "'.$this->cleanInStr($person->getInstitution()).'"';
		}
		if($person->getDepartment()){
			$fields .= ', department';
			$values .= ', "'.$this->cleanInStr($person->getDepartment()).'"';
		}
		if($person->getCity()){
			$fields .= ', city';
			$values .= ', "'.$this->cleanInStr($person->getCity()).'"';
		}
		$fields .= ', state';
		$values .= ', "'.$this->cleanInStr($person->getState()).'"';
		$fields .= ', country';
		$values .= ', "'.$this->cleanInStr($person->getCountry()).'"';
		if($person->getZip()){
			$fields .= ', zip';
			$values .= ', "'.$this->cleanInStr($person->getZip()).'"';
		}
		if($person->getPhone()){
			$fields .= ', phone';
			$values .= ', "'.$this->cleanInStr($person->getPhone()).'"';
		}
		if($person->getEmail()){
			$fields .= ', email';
			$values .= ', "'.$this->cleanInStr($person->getEmail()).'"';
		}
		if($person->getGUID()){
			$fields .= ', guid';
			$values .= ', "'.$person->getGUID().'"';
		}

		$sql = $fields.') '.$values.')';
		$this->resetConnection();
		if($this->conn->query($sql)){
			$person->setUid($this->conn->insert_id);
			$this->uid = $person->getUid();
			//Add userlogin
			$sql = 'INSERT INTO userlogin (uid, username, password) '.
				'VALUES ('.$person->getUid().', "'.$this->cleanInStr($person->getUserName()).'", PASSWORD("'.$this->cleanInStr($person->getPassword()).'"))';
			if($this->conn->query($sql)){
				$status = true;
				//authenicate
				$this->userName = $person->getUserName();
				$this->displayName = $person->getFirstName();
				$this->reset();
				$this->authenticate();
			}
			else{
				$this->errorStr = 'FAILED: Unable to create user.<div style="margin-left:55px;">Please contact system administrator for assistance.</div>';
			}
		}
		return $status;
	}

	public function lookupUserName($emailAddr){
		$status = false;
		$from = '';
		if (array_key_exists("SYSTEM_EMAIL", $GLOBALS) && !empty($GLOBALS["SYSTEM_EMAIL"])){
			$from = 'Reset Request <'.$GLOBALS["SYSTEM_EMAIL"].'>';
		}
		if(!$this->validateEmailAddress($emailAddr)) return false;
		$loginStr = '';
		$sql = 'SELECT u.uid, ul.username, concat_ws("; ",u.lastname,u.firstname) '.
			'FROM users u INNER JOIN userlogin ul ON u.uid = ul.uid '.
			'WHERE (u.email = "'.$emailAddr.'")';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			if($loginStr) $loginStr .= '; ';
			$loginStr .= $row->username;
		}
		$rs->free();
		if($loginStr){
			$subject = $GLOBALS['DEFAULT_TITLE'].' Login Name';
			$serverPath = $this->getDomain().$GLOBALS['CLIENT_ROOT'];
			$bodyStr = 'Your '.$GLOBALS['DEFAULT_TITLE'].' (<a href="'.$serverPath.'">'.$serverPath.'</a>) login name is: '.
				$loginStr.'<br/><br/>If you continue to have login issues, contact the System Administrator: '.$GLOBALS['ADMIN_EMAIL'];
			$status = $this->sendEmail($emailAddr, $subject, $bodyStr, $from);
		}
		else{
			$this->errorStr = 'There are no users registered to email address: '.$emailAddr;
		}
		return $status;
	}

	private function sendEmail($to, $subject, $body, $from = ''){
		$status = true;
		if (empty($from)){
			$from = 'portal admin <'.$GLOBALS["ADMIN_EMAIL"].'>';
		}
		$smtpArr = null;
		if(isset($GLOBALS['SMTP_ARR']) && $GLOBALS['SMTP_ARR']) $smtpArr = $GLOBALS['SMTP_ARR'];
		if(class_exists('Mail') && $smtpArr){
			$smtp = Mail::factory('smtp', $smtpArr);
			$headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);
			$mail = $smtp->send($to, $headers, $body);
			if(PEAR::isError($mail)){
				$status = false;
				$this->errorStr = $mail->getMessage();
			}
		}
		else{
			$header = "Organization: ".$GLOBALS["DEFAULT_TITLE"]." \r\n".
				"MIME-Version: 1.0 \r\n".
				"Content-type: text/html; charset=iso-8859-1 \r\n";
			if(array_key_exists("ADMIN_EMAIL",$GLOBALS) && $GLOBALS["ADMIN_EMAIL"]){
				$header .= "From: ".$from." \r\n".
					"Reply-To: ".$GLOBALS["ADMIN_EMAIL"]." \r\n".
					"Return-Path: ".$GLOBALS["ADMIN_EMAIL"]." \r\n";
			}
			
			if(!mail($to,$subject,$body,$header)){
				$status = false;
				$this->errorStr = 'mailserver might not be properly setup';
			}
		}
		return $status;
	}

	public function changeLogin($newLogin, $pwd = ''){
		$status = true;
		if($this->uid){
			$isSelf = true;
			if($this->uid != $GLOBALS['SYMB_UID']) $isSelf = false;
			$newLogin = trim($newLogin);
			if(!$this->validateUserName($newLogin)) return false;

			//Test if login exists
			$sqlTestLogin = 'SELECT uid FROM userlogin WHERE (username = "'.$newLogin.'") ';
			$rs = $this->conn->query($sqlTestLogin);
			if($rs->num_rows){
				$this->errorStr = 'Login '.$newLogin.' is already being used by another user. Please try a new login.';
				$status = false;
			}
			$rs->free();

			if($status){
				$this->setUserName();
				if($isSelf){
					if(!$this->authenticate($pwd)){
						$this->errorStr = 'ERROR saving new login: incorrect password';
						$status = false;
					}
				}
				if($status){
					//Change login
					$sql = 'UPDATE userlogin SET username = "'.$newLogin.'" WHERE (uid = '.$this->uid.') AND (username = "'.$this->userName.'")';
					//echo $sql;
					$this->resetConnection();
					if($this->conn->query($sql)){
						if($isSelf){
							$this->userName = $newLogin;
							$this->authenticate();
						}
					}
					else{
						$this->errorStr = 'ERROR saving new login: '.$this->conn->error;
						$status = false;
					}
				}
			}
		}
		return $status;
	}

	public function checkLogin($email){
		if(!$this->validateEmailAddress($email)) return false;
		//Check to see if userlogin already exists
		$status = true;
	   	$sql = 'SELECT u.email, ul.username '.
			'FROM users u INNER JOIN userlogin ul ON u.uid = ul.uid '.
			'WHERE (ul.username = "'.$this->userName.'" OR u.email = "'.$email.'" )';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$status = false;
			if($row->username == $this->userName){
				$this->errorStr = 'login_exists';
				break;
			}
			else{
				$this->errorStr = 'email_registered';
			}
		}
		$result->free();
		return $status;
	}

	//Personal and general specimen management
	public function getPersonalOccurrenceCount($collid){
		$retCnt = 0;
		if($this->uid){
			$sql = 'SELECT count(*) AS reccnt FROM omoccurrences WHERE observeruid = '.$this->uid.' AND collid = '.$collid;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retCnt = $r->reccnt;
				}
				$rs->free();
			}
		}
		return $retCnt;
	}

	public function unreviewedCommentsExist($collid){
		$retCnt = 0;
		$sql = 'SELECT count(c.comid) AS reccnt '.
			'FROM omoccurrences o INNER JOIN omoccurcomments c ON o.occid = c.occid '.
			'WHERE (o.observeruid = '.$GLOBALS['SYMB_UID'].') AND (o.collid = '.$collid.') AND (c.reviewstatus < 3)';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retCnt = $r->reccnt;
			}
			$rs->free();
		}
		return $retCnt;
	}

	//User Taxonomy functions
	private function setUserTaxonomy(&$person){
		$sql = 'SELECT ut.idusertaxonomy, t.tid, t.sciname, '.
			'ut.editorstatus, ut.geographicscope, ut.notes, ut.modifieduid, ut.modifiedtimestamp '.
			'FROM usertaxonomy ut INNER JOIN taxa t ON ut.tid = t.tid '.
			'WHERE ut.uid = ?';
		$statement = $this->conn->prepare($sql);
		$uid = $person->getUid();
		$statement->bind_param('i', $uid);
		$statement->execute();
		$statement->bind_result($id, $tid, $sciname, $editorStatus, $geographicScope, $notes, $modifiedUid, $modifiedtimestamp);
		while($statement->fetch()){
			$person->addUserTaxonomy($editorStatus, $id,'sciname',$sciname);
			$person->addUserTaxonomy($editorStatus, $id,'tid',$tid);
			$person->addUserTaxonomy($editorStatus, $id,'geographicScope',$geographicScope);
			$person->addUserTaxonomy($editorStatus, $id,'notes',$notes);
		}
		$statement->close();
	}

	public function deleteUserTaxonomy($utid,$editorStatus = ''){
		$statusStr = 'SUCCESS: Taxonomic relationship deleted';
		if(is_numeric($utid) || $utid == 'all'){
			$sql = 'DELETE FROM usertaxonomy ';
			if($utid == 'all'){
				$sql .= 'WHERE uid = '.$this->uid;
			}
			else{
				$sql .= 'WHERE idusertaxonomy = '.$utid;
			}
			if($editorStatus){
				$sql .= ' AND editorstatus = "'.$editorStatus.'" ';
			}
			$this->resetConnection();
			if($this->conn->query($sql)){
				if($this->uid == $GLOBALS['SYMB_UID']){
					$this->userName = $GLOBALS['USERNAME'];
					$this->authenticate();
				}
			}
			else{
				$statusStr = 'ERROR deleting taxonomic relationship: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function addUserTaxonomy($taxon,$editorStatus,$geographicScope,$notes){
		$statusStr = 'SUCCESS adding taxonomic relationship';

		$tid = 0;
		$taxon = $this->cleanInStr($taxon);
		$editorStatus = $this->cleanInStr($editorStatus);
		$geographicScope = $this->cleanInStr($geographicScope);
		$notes = $this->cleanInStr($notes);
		$modDate = date('Y-m-d H:i:s');
		//Get tid for taxon
		$sql1 = 'SELECT tid FROM taxa WHERE sciname = "'.$taxon.'"';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$tid = $r1->tid;
		}
		$rs1->close();
		if($tid){
			$sql2 = 'INSERT INTO usertaxonomy(uid, tid, taxauthid, editorstatus, geographicScope, notes, modifiedUid, modifiedtimestamp) '.
				'VALUES('.$this->uid.','.$tid.',1,"'.$editorStatus.'","'.$geographicScope.'","'.$notes.'",'.$GLOBALS['SYMB_UID'].',"'.$modDate.'") ';
			//echo $sql;
			$this->resetConnection();
			if($this->conn->query($sql2)){
				if($this->uid == $GLOBALS['SYMB_UID']){
					$this->userName = $GLOBALS['USERNAME'];
					$this->authenticate();
				}
			}
			else{
				$statusStr = 'ERROR adding taxonomic relationship: '.$this->conn->error;
			}
		}
		else{
			$statusStr = 'ERROR adding taxonomic relationship: unable to obtain tid for '.$taxon;
		}
		return $statusStr;
	}

	/**
	 *
	 * Obtain the list of specimens that have an identification verification status rank less than 6
	 * within the list of taxa for which this user is listed as a specialist.
	 *
	 */
	public function echoSpecimensPendingIdent($withImgOnly = 1){
		if($this->uid){
			$tidArr = array();
			$sqlt = 'SELECT t.tid, t.sciname '.
				'FROM usertaxonomy u INNER JOIN taxa t ON u.tid = t.tid '.
				'WHERE u.uid = '.$this->uid.' AND u.editorstatus = "OccurrenceEditor" '.
				'ORDER BY t.sciname ';
			$rst = $this->conn->query($sqlt);
			while($rt = $rst->fetch_object()){
				$tidArr[$rt->tid] = $rt->sciname;
			}
			$rst->free();
			if($tidArr){
				foreach($tidArr as $tid => $taxonName){
					echo '<div style="margin:10px;">';
					echo '<div><b><u>'.$taxonName.'</u></b></div>';
					echo '<ul style="margin:10px;">';
					$sql = 'SELECT DISTINCT o.occid, o.catalognumber, IFNULL(o.sciname,t.sciname) as sciname, o.stateprovince, '.
						'CONCAT_WS("-",IFNULL(o.institutioncode,c.institutioncode),IFNULL(o.collectioncode,c.collectioncode)) AS collcode '.
						'FROM omoccurrences o INNER JOIN omoccurverification v ON o.occid = v.occid '.
						'INNER JOIN omcollections c ON o.collid = c.collid '.
						'INNER JOIN taxa t ON o.tidinterpreted = t.tid '.
						'INNER JOIN taxaenumtree e ON t.tid = e.tid ';
					if($withImgOnly) $sql .= 'INNER JOIN images i ON o.occid = i.occid ';
					$sql .= 'WHERE v.category = "identification" AND v.ranking < 6 AND e.taxauthid = 1 '.
						'AND (e.parenttid = '.$tid.' OR t.tid = '.$tid.') '.
						'ORDER BY o.sciname,t.sciname,o.catalognumber ';
					//echo '<div>'.$sql.'</div>';
					$rs = $this->conn->query($sql);
					if($rs->num_rows){
						while($r = $rs->fetch_object()){
							echo '<li><i>'.$r->sciname.'</i>, ';
							echo '<a href="../collections/editor/occurrenceeditor.php?occid='.$r->occid.'" target="_blank">';
							echo $r->catalognumber.'</a> ['.$r->collcode.']'.($r->stateprovince?', '.$r->stateprovince:'');
							echo '</li>'."\n";
						}
					}
					else{
						echo '<li>No deficiently identified specimens were found within this taxon</li>';
					}
					echo '</ul>';
					echo '</div>';
					$rs->free();
					ob_flush();
					flush();
				}
			}
		}
	}

	public function echoSpecimensLackingIdent($withImgOnly = 1){
		if($this->uid){
			echo '<div style="margin:10px;">';
			echo '<div><b><u>Lacking Identifications</u></b></div>';
			echo '<ul style="margin:10px;">';
			$sql = 'SELECT DISTINCT o.occid, o.catalognumber, o.stateprovince, '.
				'CONCAT_WS("-",IFNULL(o.institutioncode,c.institutioncode),IFNULL(o.collectioncode,c.collectioncode)) AS collcode '.
				'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid ';
			if($withImgOnly) $sql .= 'INNER JOIN images i ON o.occid = i.occid ';
			$sql .= 'WHERE (o.sciname IS NULL) '.
				'ORDER BY c.institutioncode, o.catalognumber LIMIT 2000';
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			if($rs->num_rows){
				while($r = $rs->fetch_object()){
					echo '<li>';
					echo '<a href="../collections/editor/occurrenceeditor.php?occid='.$r->occid.'" target="_blank">';
					echo $r->catalognumber.'</a> ['.$r->collcode.']'.($r->stateprovince?', '.$r->stateprovince:'');
					echo '</li>'."\n";
				}
			}
			else{
				echo '<li>No un-identified specimens were found</li>';
			}
			echo '</ul>';
			echo '</div>';
			$rs->free();
			ob_flush();
			flush();
		}
	}

	//Functions to be replaced
	public function dlSpecBackup($collId, $characterSet, $zipFile = 1){
		global $PARAMS_ARR;

		$tempPath = $this->getTempPath();
		$buFileName = $PARAMS_ARR['un'].'_'.time();

 		$cSet = str_replace('-','',strtolower($GLOBALS['CHARSET']));
		$fileUrl = '';
		//If zip archive can be created, the occurrences, determinations, and image records will be added to single archive file
		//If not, then a CSV file containing just occurrence records will be returned
		echo '<li style="font-weight:bold;">Zip Archive created</li>';
		echo '<li style="font-weight:bold;">Adding occurrence records to archive...';
		ob_flush();
		flush();
		//Adding occurrence records
		$fileName = $tempPath.$buFileName;
		$specFH = fopen($fileName.'_spec.csv', "w");
		//Output header
		$headerStr = 'occid,dbpk,basisOfRecord,otherCatalogNumbers,ownerInstitutionCode, '.
			'family,scientificName,sciname,tidinterpreted,genus,specificEpithet,taxonRank,infraspecificEpithet,scientificNameAuthorship, '.
			'taxonRemarks,identifiedBy,dateIdentified,identificationReferences,identificationRemarks,identificationQualifier, '.
			'typeStatus,recordedBy,recordNumber,associatedCollectors,eventDate,year,month,day,startDayOfYear,endDayOfYear, '.
			'verbatimEventDate,habitat,substrate,occurrenceRemarks,informationWithheld,associatedOccurrences, '.
			'dataGeneralizations,associatedTaxa,dynamicProperties,verbatimAttributes,reproductiveCondition, '.
			'cultivationStatus,establishmentMeans,lifeStage,sex,individualCount,country,stateProvince,county,municipality, '.
			'locality,localitySecurity,localitySecurityReason,decimalLatitude,decimalLongitude,geodeticDatum, '.
			'coordinateUncertaintyInMeters,verbatimCoordinates,georeferencedBy,georeferenceProtocol,georeferenceSources, '.
			'georeferenceVerificationStatus,georeferenceRemarks,minimumElevationInMeters,maximumElevationInMeters,verbatimElevation, '.
			'previousIdentifications,disposition,modified,language,processingstatus,recordEnteredBy,duplicateQuantity,dateLastModified ';
		fputcsv($specFH, explode(',',$headerStr));
		//Query and output values
		$sql = 'SELECT '.$headerStr.' FROM omoccurrences WHERE collid = '.$collId.' AND observeruid = '.$this->uid;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_row()){
				if($characterSet && $characterSet != $cSet){
					$this->encodeArr($r,$characterSet);
				}
				fputcsv($specFH, $r);
			}
			$rs->free();
		}
		fclose($specFH);

		if($zipFile && class_exists('ZipArchive')){
			$zipArchive = new ZipArchive;
			$zipArchive->open($tempPath.$buFileName.'.zip', ZipArchive::CREATE);
			//Add occurrence file and then rename to
			$zipArchive->addFile($fileName.'_spec.csv');
			$zipArchive->renameName($fileName.'_spec.csv','occurrences.csv');

			//Add determinations
			/*
			echo 'Done!</li> ';
			echo '<li style="font-weight:bold;">Adding determinations records to archive...';
			ob_flush();
			flush();
			$detFH = fopen($fileName.'_det.csv', "w");
			fputcsv($detFH, Array('detid','occid','sciname','scientificNameAuthorship','identifiedBy','d.dateIdentified','identificationQualifier','identificationReferences','identificationRemarks','sortsequence'));
			//Add determination values
			$sql = 'SELECT d.detid,d.occid,d.sciname,d.scientificNameAuthorship,d.identifiedBy,d.dateIdentified, '.
				'd.identificationQualifier,d.identificationReferences,d.identificationRemarks,d.sortsequence '.
				'FROM omdeterminations d INNER JOIN omoccurrences o ON d.occid = o.occid '.
				'WHERE o.collid = '.$this->collId.' AND o.observeruid = '.$this->uid;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_row()){
					fputcsv($detFH, $r);
				}
				$rs->close();
			}
			fclose($detFH);
			$zipArchive->addFile($fileName.'_det.csv');
			$zipArchive->renameName($fileName.'_det.csv','determinations.csv');
			*/

			echo 'Done!</li> ';
			ob_flush();
			flush();
			$fileUrl = str_replace($GLOBALS['SERVER_ROOT'],$GLOBALS['CLIENT_ROOT'],$tempPath.$buFileName.'.zip');
			$zipArchive->close();
			unlink($fileName.'_spec.csv');
			//unlink($fileName.'_det.csv');
		}
		else{
			$fileUrl = str_replace($GLOBALS['SERVER_ROOT'],$GLOBALS['CLIENT_ROOT'],$tempPath.$buFileName.'_spec.csv');
		}
		return $fileUrl;
	}

	//Setters and getters
	public function setUid($uid){
		if(is_numeric($uid)){
			$this->uid = $uid;
		}
	}

	private function setUserRights(){
		global $USER_RIGHTS;
		//Get Admin Rights
		if($this->uid){
			$userRights = array();
			$sql = 'SELECT role, tablepk FROM userroles WHERE (uid = '.$this->uid.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$userRights[$r->role][] = $r->tablepk;
			}
			$rs->free();
			$_SESSION['userrights'] = $userRights;
			$USER_RIGHTS = $userRights;
		}
	}

	private function setUserParams(){
		global $PARAMS_ARR;
		$_SESSION['userparams']['un'] = $this->userName;
		$_SESSION['userparams']['dn'] = $this->displayName;
		$_SESSION['userparams']['uid'] = $this->uid;
		$PARAMS_ARR = $_SESSION['userparams'];
		$GLOBALS['USERNAME'] = $this->userName;
	}

	public function setToken($token){
		$this->token = $token;
		$this->authSql = 'SELECT u.uid, u.firstname, u.lastname '.
			'FROM users u INNER JOIN userlogin l ON u.uid = l.uid INNER JOIN useraccesstokens t ON u.uid = t.uid '.
			'WHERE (l.username = "'.$this->userName.'" OR u.email = "'.$this->userName.'") AND (t.token = "'.$this->token.'") ';
	}

	public function setRememberMe($test){
		$this->rememberMe = $test;
	}

	public function getRememberMe(){
		return $this->rememberMe;
	}

	public function setUserName($un = ''){
		if($un){
			if(!$this->validateUserName($un)) return false;
			$this->userName = $un;
		}
		else{
			if($this->uid == $GLOBALS['SYMB_UID']){
				$this->userName = $GLOBALS['USERNAME'];
			}
			elseif($this->uid){
				$sql = 'SELECT username FROM userlogin WHERE (uid = '.$this->uid.') ';
				//echo $sql;
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()){
					$this->userName = $r->username;
				}
				$rs->free();
			}
		}
		return true;
	}

	public function getUserName($uid){
		$un = '';
		$sql = 'SELECT username FROM userlogin WHERE uid = '.$uid.' ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$un = $r->username;
		}
		$rs->free();
		return $un;
	}

	private function getTempPath(){
		$tPath = $GLOBALS["SERVER_ROOT"];
		if(substr($tPath,-1) != '/' && substr($tPath,-1) != '\\') $tPath .= '/';
		$tPath .= "temp/";
		if(file_exists($tPath."downloads/")){
			$tPath .= "downloads/";
		}
		return $tPath;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Other misc functions
	public function validateEmailAddress($emailAddress){
		if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
			$this->errorStr = 'email_invalid';
			return false;
		}
		return true;
	}

	private function validateUserName($un){
		$status = true;
		if (preg_match('/^[0-9A-Za-z_!@#$\s\.+\-]+$/', $un) == 0) $status = false;
		if (substr($un,0,1) == ' ') $status = false;
		if (substr($un,-1) == ' ') $status = false;
		if(!$status) $this->errorStr = 'username not valid';
		return $status;
	}

	private function encodeArr(&$inArr,$cSet){
		foreach($inArr as $k => $v){
			$inArr[$k] = $this->encodeString($v,$cSet);
		}
	}

	//OAuth2 functions
	public function generateTokenPacket(){
		$pkArr = Array();
		$this->createToken();
		$person = $this->getPerson();
		if($this->token){
			$pkArr['uid'] = $this->uid;
			$pkArr['firstname'] = $person->getFirstName();;
			$pkArr['lastname'] = $person->getLastName();
			$pkArr['email'] = $person->getEmail();
			$pkArr['token'] = $this->token;
		}
		return $pkArr;
	}

	public function generateAccessPacket(){
		$pkArr = Array();
		$sql = 'SELECT ul.role, ul.tablename, ul.tablepk, c.CollectionName, c.CollectionCode, c.InstitutionCode, fc.`Name`, fp.projname '.
			'FROM userroles AS ul LEFT JOIN omcollections AS c ON ul.tablepk = c.CollID '.
			'LEFT JOIN fmchecklists AS fc ON ul.tablepk = fc.CLID '.
			'LEFT JOIN fmprojects AS fp ON ul.tablepk = fp.pid '.
			'WHERE ul.uid = '.$this->uid.' ';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				if($r->role == 'CollAdmin' || $r->role == 'CollEditor' || $r->role == 'CollTaxon'){
					$pkArr['collections'][$r->role][$r->tablepk]['CollectionName'] = $r->CollectionName;
					$pkArr['collections'][$r->role][$r->tablepk]['CollectionCode'] = $r->CollectionCode;
					$pkArr['collections'][$r->role][$r->tablepk]['InstitutionCode'] = $r->InstitutionCode;
				}
				elseif($r->role == 'ClAdmin'){
					$pkArr['checklists'][$r->role][$r->tablepk]['ChecklistName'] = $r->Name;
				}
				elseif($r->role == 'ProjAdmin'){
					$pkArr['projects'][$r->role][$r->tablepk]['ProjectName'] = $r->projname;
				}
				else{
					$pkArr['portal'][] = $r->role;
				}
			}
			$rs->free();
		}
		if(in_array('SuperAdmin',$pkArr['portal'])){
			$pkArr['collections']['CollAdmin'] = $this->getCollectionArr();
			$pkArr['checklists']['ClAdmin'] = $this->getChecklistArr();
			$pkArr['projects']['ProjAdmin'] = $this->getProjectArr();
		}
		return $pkArr;
	}

	public function createToken(){
		$token = '';
		$token = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
		if($token){
			$this->resetConnection();
			$sql = 'INSERT INTO useraccesstokens (uid,token) VALUES ('.$this->uid.',"'.$token.'") ';
			if($this->conn->query($sql)){
				$this->token = $token;
			}
		}
	}

	public function getCollectionArr(){
		global $USER_RIGHTS;
		$retArr = Array();

		$cArr = array();
		if(array_key_exists('CollAdmin',$USER_RIGHTS)) $cArr = $USER_RIGHTS['CollAdmin'];
		if(array_key_exists('CollEditor',$USER_RIGHTS)) $cArr = array_merge($cArr,$USER_RIGHTS['CollEditor']);
		if(!$cArr) return $retArr;

		$sql = 'SELECT collid, institutioncode, collectioncode, collectionname, colltype FROM omcollections WHERE collid IN('.implode(',',$cArr).') ORDER BY collectionname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->collid]['collectionname'] = $r->collectionname;
				$retArr[$r->collid]['collectioncode'] = $r->collectioncode;
				$retArr[$r->collid]['institutioncode'] = $r->institutioncode;
				$retArr[$r->collid]['colltype'] = $r->colltype;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getChecklistArr(){
		$retArr = Array();
		$sql = 'SELECT CLID, `Name` FROM fmchecklists';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->CLID]['ChecklistName'] = $r->Name;
			}
			$rs->free();
		}

		return $retArr;
	}

	public function getProjectArr(){
		$retArr = Array();
		$sql = 'SELECT pid, projname FROM fmprojects';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->pid]['ProjectName'] = $r->projname;
			}
			$rs->free();
		}

		return $retArr;
	}

	public function getTokenCnt(){
		$cnt = 0;
		$sql = 'SELECT COUNT(token) AS cnt FROM useraccesstokens WHERE uid = '.$this->uid;
		//echo $sql;
		$result = $this->conn->query($sql);
		if($row = $result->fetch_object()){
			$cnt = $row->cnt;
			$result->free();
		}
		return $cnt;
	}

	public function getUid($un){
		$uid = '';
		$un = $this->cleanInStr($un);
		$sql = 'SELECT u.uid FROM users u INNER JOIN userlogin l ON u.uid = l.uid WHERE l.username = "'.$un.'" OR u.email = "'.$un.'" ';
		//echo $sql;
		$result = $this->conn->query($sql);
		if($row = $result->fetch_object()){
			$uid = $row->uid;
			$result->free();
		}
		return $uid;
	}

	public function deleteToken($uid,$token){
		$statusStr = '';
		$sql = 'DELETE FROM useraccesstokens WHERE uid = '.$uid.' AND token = "'.$token.'" ';
		$this->resetConnection();
		if($this->conn->query($sql)) $statusStr = 'Access token cleared!';
		else $statusStr = 'ERROR clearing access token: '.$this->conn->error;
		return $statusStr;
	}

	public function clearAccessTokens(){
		$statusStr = '';
		$sql = 'DELETE FROM useraccesstokens WHERE uid = '.$this->uid;
		//echo $sql;
		$this->resetConnection();
		if($this->conn->query($sql)) $statusStr = 'Access tokens cleared!';
		else $statusStr = 'ERROR clearing access tokens: '.$this->conn->error;
		return $statusStr;
	}
}
?>
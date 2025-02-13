<?php

use function PHPUnit\Framework\returnValue;

include_once('ProfileManager.php');

class OpenIdProfileManager extends ProfileManager
{

	public function authenticate($sub = '', $provider = '')
	{
		$status = false;
		unset($_SESSION['userrights']);
		unset($_SESSION['userparams']);
		$status = $this->authenticateUsingOidSub($sub, $provider);
		if ($status) {
			if (strlen($this->displayName) > 15) $this->displayName = $this->userName;
			if (strlen($this->displayName) > 15) $this->displayName = substr($this->displayName, 0, 10) . '...';
			$this->reset();
			$this->setUserRights();
			$this->setUserParams();
			// if($this->rememberMe) $this->setTokenCookie();
			if (!isset($GLOBALS['SYMB_UID']) || !$GLOBALS['SYMB_UID']) {
				$this->resetConnection();
				$sql = 'UPDATE users SET lastLoginDate = NOW() WHERE (uid = ?)';
				if ($stmt = $this->conn->prepare($sql)) {
					$stmt->bind_param('i', $this->uid);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		return $status;
	}

	private function authenticateUsingOidSub($sub, $provider)
	{
		$status = false;
		if ($sub && $provider) {
			$sql = 'SELECT uid from usersthirdpartyauth WHERE subUuid = ? AND provider = ?';
			if ($stmt = $this->conn->prepare($sql)) {
				if ($stmt->bind_param('ss', $sub, $provider)) {
					$stmt->execute();
					$stmt->bind_result($this->uid);
					$stmt->fetch();
					$stmt->close();
				} else echo 'error binding parameters: ' . $stmt->error;
			}
			if ($this->uid) {
				$sql = 'SELECT uid, firstname, username FROM users WHERE (uid = ?)';
				if ($stmt = $this->conn->prepare($sql)) {
					if ($stmt->bind_param('i', $this->uid)) {
						$stmt->execute();
						$stmt->bind_result($this->uid, $this->displayName, $this->userName);
						if ($stmt->fetch()) $status = true;
						$stmt->close();
					} else echo 'error binding parameters: ' . $stmt->error;
				} else echo 'error preparing statement: ' . $this->conn->error;
			}
		}
		return $status;
	}

	public function linkThirdPartySid($thirdparty_sid, $local_sid, $ip)
	{
		$sql = 'INSERT INTO usersthirdpartysessions(thirdparty_id, localsession_id, ipaddr) VALUES (?, ?, ?)';
		if ($stmt = $this->conn->prepare($sql)) {
			if ($stmt->bind_param('sss', $thirdparty_sid, $local_sid, $ip)) {
				$stmt->execute();
				//if($stmt->error){
				//}
				$stmt->close();
			}
		}
	}

	public function linkLocalUserOidSub($email, $sub, $provider)
	{
		if ($email && $sub && $provider) {
			$sql = 'SELECT u.uid, oid.subUuid, oid.provider from users u LEFT join usersthirdpartyauth oid ON u.uid = oid.uid 
			WHERE u.email = ?';
			if ($stmt = $this->conn->prepare($sql)) {
				if ($stmt->bind_param('s', $email)) {
					$stmt->execute();
					$results = mysqli_stmt_get_result($stmt);
					$stmt->close();
				}
				if ($results->num_rows < 1) {
					//Local user does not exist
					throw new Exception("User does not exist in symbiota database <ERR/>");
				} else {
					if ($results->num_rows == 1) {
						$row = $results->fetch_array(MYSQLI_ASSOC);
						if (($row['provider'] == '' && $row['subUuid'] == '') || ($row['provider'] && $row['provider'] !== $provider)) {
							//found existing user. add 3rdparty auth info
							$sql = 'INSERT INTO usersthirdpartyauth (uid, subUuid, provider) VALUES(?,?,?)';
							$this->resetConnection();
							if ($stmt = $this->conn->prepare($sql)) {
								$stmt->bind_param('iss', $row['uid'], $sub, $provider);
								$stmt->execute();
							}
							$this->uid = $row['uid'];
							return true;
						}
					} else if ($results->num_rows > 1) {
						$uidPlaceholder = '';
						while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
							$uidPlaceholder = $row['uid']; // assumes one-to-one relationship between user and email address
							if ($row['provider'] == $provider && $row['subUuid'] !== $sub) {
								return false; // current assumption is that if this happens, the subUuid is not kosher. 
								// If this assumption is ever violated, one solution would be to purge relevant rows from usersthirdpartyauth
							} else continue;
						}
						// Provider not found - handle adding new entry to usersthirdpartyauth table
						$sql = 'INSERT INTO usersthirdpartyauth (uid, subUuid, provider) VALUES(?,?,?)';
						$this->resetConnection();
						if ($stmt = $this->conn->prepare($sql)) {
							$stmt->bind_param('iss', $uidPlaceholder, $sub, $provider);
							$stmt->execute();
						}
						$this->uid = $row['uid'];
						return true;
					}
				}
			}
		}
	}

	public function lookupLocalSessionIDWithThirdPartySid($thirdparty_sid)
	{
		$sql = 'SELECT localsession_id FROM usersthirdpartysessions WHERE thirdparty_id = ?';
		$localSessionID = '';
		if ($stmt = $this->conn->prepare($sql)) {
			if ($stmt->bind_param('s', $thirdparty_sid)) {
				$stmt->execute();
				$stmt->bind_result($localSessionID);
				$stmt->fetch();
				$stmt->close();
			}
		}
		return $localSessionID;
	}

	public function forceLogout($targetSessionId)
	{
		$originalSessionId = session_id();
		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((1)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);

		if ($currentSessionStatus === PHP_SESSION_ACTIVE) {
			error_log("(forceLogout) ((entered if block))");
			session_write_close();
		}

		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((2)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);

		session_id($targetSessionId);
		session_start();
		$_SESSION['force_logout'] = true;
		
		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((3)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);

		
		/**
		$_SESSION = [];
		session_unset();

		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((4)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);

		if (ini_get("session.use_cookies")) {
			error_log("deleteMe got here session cookies");
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]
			);
		}

		session_destroy();
		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((5)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);

		$sessionFile = session_save_path() . '/sess_' . $targetSessionId;
		if (file_exists($sessionFile)) {
			error_log("got here in the file exists");
			unlink($sessionFile);
		}

		if ($originalSessionId !== $targetSessionId) {
			error_log("deleteMe got here in the re-activation of the session");
			session_id($originalSessionId);
			if ($originalSessionId === PHP_SESSION_ACTIVE) {
				error_log("deleteMe got here $originalSessionId === PHP_SESSION_ACTIVE");
				session_start();
			}
		}
		$currentSessionId = session_id();
		$currentSessionStatus = session_status();
		error_log("(forceLogout) ((6)) CurrentSession: " . $currentSessionId . "CurrentSessionStatus: " . $currentSessionStatus .  "targetSession: " . $targetSessionId);
		*/
	}
}

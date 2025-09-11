<?php
include_once(__DIR__ . '/config/symbini.php');

$AUTHORIZE_STAGE = 'authorize';
$TOKEN_STAGE = 'token';

function oauth2_authorize($query_params) {
	global $TOKEN_STAGE;

	$api_url = 'https://auth.globus.org/v2/oauth2/authorize';
	$ch = curl_init();

	curl_setopt_array($ch, [
		CURLOPT_URL => $api_url . '?' . http_build_query($query_params),
		CURLOPT_HEADER => TRUE,
		CURLOPT_RETURNTRANSFER => true,
	]);

	// grab URL and pass it to the browser
	$response = curl_exec($ch);

	$curl_info = curl_getinfo($ch);

    curl_close($ch);

	$_SESSION['globus_oauth_stage'] = $TOKEN_STAGE;

	if($curl_info['redirect_url']) {
		header('location: ' . $curl_info['redirect_url']);
	}
}

function get_access_token($body) {
	global $GLOBUS_CLIENT_ID, $GLOBUS_CLIENT_SECRET;
	$ch = curl_init();

	curl_setopt_array($ch, [
		CURLOPT_URL => 'https://auth.globus.org/v2/oauth2/token',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => http_build_query($body),
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_USERPWD => $GLOBUS_CLIENT_ID . ':' . $GLOBUS_CLIENT_SECRET
	]);  

	// grab URL and pass it to the browser
	$response = curl_exec($ch);

    curl_close($ch);

	return json_decode($response);
}

function generatePKCECodeVerifier() {
	$length = 43; //= random_int(43, 128);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function base64url_encode($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function getAccessToken() {
	global $GLOBUS_CLIENT_ID, $GLOBUS_CLIENT_SECRET, $TOKEN_STAGE, $AUTHORIZE_STAGE;

	if(isset($_SESSION['GLOBUS_ACCESS_TOKEN'])) {
		return $_SESSION['GLOBUS_ACCESS_TOKEN'];
	}

	$stage = $_SESSION['globus_oauth_stage'] ?? $AUTHORIZE_STAGE;
	$globus_auth = $_SESSION['GLOBUS_CLIENT_AUTH'] ?? $AUTHORIZE_STAGE;

	if($stage == $AUTHORIZE_STAGE) {
		$_SESSION['oauth2_state'] = uniqid();
		$_SESSION['code_verifier'] = generatePKCECodeVerifier();

		$config = [
			'client_id' => $GLOBUS_CLIENT_ID,
			'scope' => 'urn:globus:auth:scope:transfer.api.globus.org:all urn:globus:auth:scope:auth.globus.org:view_identities offline_access',
			'state' => $_SESSION['oauth2_state'],
			'redirect_uri' => 'http://localhost:8000/callback.php',
			'response_type' => 'code',
			'code_challenge_method' => 'S256',
			'code_challenge' => base64url_encode(hash('sha256', $_SESSION['code_verifier'], true))
		];

		oauth2_authorize($config);
	} else if($stage == $TOKEN_STAGE) {
		try {
			if(($_SESSION['oauth2_state'] ?? null) === ($_REQUEST['state'] ?? false)) {
				$body = [
					'grant_type' => 'authorization_code',
					'code' => $_REQUEST['code'],
					'redirect_uri' => 'http://localhost:8000/callback.php',
					'code_verifier' => $_SESSION['code_verifier'],
				];

				$_SESSION['GLOBUS_ACCESS_TOKEN'] = get_access_token($body);

				return $_SESSION['GLOBUS_ACCESS_TOKEN'];
			} else {
				return [
					'error' => 'Request state does not match server state',
					'expected' => $_SESSION['oauth2_state'],
					'received' => $_REQUEST['state'] ?? null,
				];
			}
		} finally {
			$_SESSION['globus_oauth_stage'] = null;
		}
	} else {
		$_SESSION['globus_oauth_stage'] = null;
		return [
			'error' => $stage . ' is not a valid stage'
		];
	}
}

// header('Content-Type: application/json');
var_dump(getAccessToken());

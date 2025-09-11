<?php
include_once(__DIR__ . '/config/symbini.php');

if(!isset($_SESSION['GLOBUS_ACCESS_TOKEN'])) {
	echo '<div>No Access Token</div>';
	return;
}

$AUTH_HEADER = 'Bearer ' .$_SESSION['GLOBUS_ACCESS_TOKEN']->other_tokens[0]->access_token;


$BASE_URL = 'https://transfer.api.globusonline.org/v0.10';

$KU = '67d70bc9-c47a-4583-8a56-5edc02d263ec';
$BIOKIC = '41e1992f-2c60-40cd-ae74-918c51f3222d';

function transfer_files(string $source, string $destination, $submission_id, $files) {
	$transfer_body = [
		'DATA_TYPE' => 'transfer',
		'source_endpoint' => $source,
		'destination_endpoint' => $destination,
		'submission_id' => $submission_id,
		'DATA' => $files,
		'verify_checksum' => true,
		'preserve_timestamp' => true,
	];

	return transfer_api('/transfer', [
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => json_encode($transfer_body),
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json' 
		]
	]);
}

function get_submission_id() {
	try {
		$response = json_decode(transfer_api('/submission_id'));
		return $response->value;
	} catch(Throwable $th) {
		var_dump($th->getMessage());
		return false;
	}
}

function list_files($collection_id, $path = null) {
	$url = '/operation/endpoint/' . $collection_id . '/ls';

	if($path) {
		$url .= '?path=' . $path;
	}

	return transfer_api($url, [
		CURLOPT_CUSTOMREQUEST => 'GET',
	]);
}

function transfer_api($url, $options= []) {
	global $BASE_URL, $AUTH_HEADER;

	$ch = curl_init();

	if(isset($options[CURLOPT_HTTPHEADER]) ) {
		array_push($options[CURLOPT_HTTPHEADER], 'Authorization: ' . $AUTH_HEADER);
	} else {
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $AUTH_HEADER]);
	}

	curl_setopt_array($ch, [
		CURLOPT_URL => $BASE_URL . $url,
		CURLOPT_RETURNTRANSFER => true,
	]);

	if(count($options) > 0) {
		curl_setopt_array($ch, $options);
	}

	$response = curl_exec($ch);
	return $response;
}

// TODO (Logan) hook this up to unique media url database
$files = [];
if(count($files) > 0) {
	$submission_id = get_submission_id();
	$response = transfer_files($BIOKIC, $KU, $submission_id, $files);

	header('Content-Type: application/json');
	echo $response;
} else {
	echo json_encode(['error' => 'no files provided']);
}

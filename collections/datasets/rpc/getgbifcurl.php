<?php
include_once('../../../config/symbini.php');

$type = array_key_exists('type',$_REQUEST)?$_REQUEST['type']:'';
$data = array_key_exists('data',$_REQUEST)?$_REQUEST['data']:'';

$endpoint = '';
if(array_key_exists('endpoint',$data)){
    $endpoint = $data['endpoint'];
    unset($data['endpoint']);
}

$GBIFdatasetKey = '';
if(array_key_exists('datasetkey',$data)){
    $GBIFdatasetKey = $data['datasetkey'];
    unset($data['datasetkey']);
}

$GBIF_url = 'https://api.gbif.org/v1/' . $endpoint;
if(!empty($GBIFdatasetKey)){
    $GBIF_url .= '/'.$GBIFdatasetKey . +'/endpoint';
}

$result = '';
$loginStr = $GBIF_USERNAME.':'.$GBIF_PASSWORD;

if($type && filter_var($GBIF_url, FILTER_VALIDATE_URL)) {
    $ch = curl_init($GBIF_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    if($data){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json')
    );

    $result = curl_exec($ch);
}

echo str_replace('"','',$result);
?>
<?php
$PROVIDER_URLS = array('oid' => 'https://login.microsoftonline.com/AddMore/v2.0', 'google' => 'foo');
$CLIENT_IDS = array('oid' => 'someGuid', 'google' => 'foo');
$CLIENT_SECRETS = array('oid' => 'someGuid', 'google' => 'foo');
$CALLBACK_REDIRECT = '/profile/authCallback.php';
$LOGOUT_REDIRECT = '/profile/logout.php'; //@TODO add as auth provider's logout callback URL
// Needed for local Dev Env Only
// $SHOULD_UPGRADE_INSECURE_REQUESTS = false; // this needs to be commented in if you're developing locally without ssl enabled
// $SHOULD_VERIFY_PEERS = false;

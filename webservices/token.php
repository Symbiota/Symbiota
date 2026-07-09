<?php
/*
 * * ****  Accepts  ********************************************
 *
 * POST requests
 *
 * ****  Input Variables  ********************************************
 *
 * un: Username for user.
 * pw: Password for user.
 *
 * * ****  Output  ********************************************
 *
 * JSON array of user variables.
 *
 * uid: User ID for user.
 * firstname: First name of user.
 * lastname: Last name of user.
 * email: Email address of user.
 * token: Token for user to use in future API access to portal.
 *
 */

include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');

$un = array_key_exists('un',$_POST)?$_POST['un']:'';
$pw = array_key_exists('pw',$_POST)?$_POST['pw']:'';

$pHandler = new ProfileManager();

$tokenPacket = Array();

if($un && $pw){
    $pHandler->setUserName($un);
    if($pHandler->authenticate($pw)){
        $tokenPacket = $pHandler->generateTokenPacket();
        echo json_encode($tokenPacket);
    }
    else{
        echo 'Incorrect username/password.';
    }
}
?>
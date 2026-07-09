<?php
/*
 * * ****  Accepts  ********************************************
 *
 * POST requests
 *
 * ****  Input Variables  ********************************************
 *
 * un (optional): Username for user – if blank, must have uid.
 * uid (optional): User ID for user – if blank, must have un.
 * token: Access token for user.
 *
 * * ****  Output  ********************************************
 *
 * JSON array of user permission variables.
 *
 * collections: Subarray of collections user has permissions for (only included if permissions exist).
 * checklists: Subarray of checklists user has permissions for (only included if permissions exist).
 * projects: Subarray of projects user has permissions for (only included if permissions exist).
 * portal: Subarray of portal-wide permissions user has (only included if permissions exist).
 *
 * Each collections subarray contains with collection ID as index:
 *  CollectionName: Name of collection.
 *  CollectionCode: Collection code for collection.
 *  InstitutionCode: Institution code for collection.
 * Each checklists subarray contains with checklist ID as index:
 *  ChecklistName: Name of checklist.
 * Each projects subarray contains with project ID as index:
 *  ProjectName: Name of project.
 * Each portal subarray contains permissions.
 *
 */

include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');

$un = array_key_exists('un',$_POST)?$_POST['un']:'';
$uId = array_key_exists('uid',$_POST)?$_POST['uid']:'';
$token = array_key_exists('token',$_POST)?$_POST['token']:'';

$pHandler = new ProfileManager();

$accessPacket = Array();

if(!$un && $uId){
    $un = $pHandler->getUserName($uId);
}

if($un && $token){
    if($pHandler->setUserName($un)){
        $pHandler->setToken($token);
        if($pHandler->authenticate()){
            $accessPacket = $pHandler->generateAccessPacket();
            echo json_encode($accessPacket);
        }
    }
}
?>
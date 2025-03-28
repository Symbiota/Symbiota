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
 * collid: Collection ID for occurrence record.
 * occid (optional): occid of occurrence record – if blank, must have catnum.
 * catnum (optional): Catalog number or other catalog number of occurrence record – if blank, must have occid.
 * sciname (optional): Scientific name of specimen. NOTE BOTH sciname AND determiner REQUIRED TO APPLY NEW DETERMINATION.
 * determiner (optional): Name of person or automated process which identified the specimen/image. NOTE BOTH sciname AND determiner REQUIRED TO APPLY NEW DETERMINATION.
 * detacc (optional): Percent accuracy of identification for automated identifications.
 * caption (optional): Caption for the image.
 * notes (optional): Notes for the image.
 * imgfile: The image file.
 *
 * * ****  Output  ********************************************
 *
 * ERROR or SUCCESS messages.
 *
 */

include_once($SERVER_ROOT . '/config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAPIManager.php');

$un = array_key_exists('un',$_POST)?$_POST['un']:'';
$uId = array_key_exists('uid',$_POST)?$_POST['uid']:'';
$token = array_key_exists('token',$_POST)?$_POST['token']:'';
$collid = array_key_exists('collid',$_POST)?$_POST['collid']:0;
$occid = array_key_exists('occid',$_POST)?$_POST['occid']:0;
$catnum = array_key_exists('catnum',$_POST)?$_POST['catnum']:'';

$pHandler = new ProfileManager();
$qHandler = new OccurrenceAPIManager();
$occManager = new OccurrenceEditorImages();
$authenticated = false;
$isEditor = false;
$size = Array();

if($_FILES){
	@$size = getimagesize(str_replace(' ', '%20', $_FILES['imgfile']['tmp_name']));
}

if($size){
    if(!$un && $uId){
        $un = $pHandler->getUserName($uId);
    }

    if($un && $token){
        if($pHandler->setUserName($un)){
            $pHandler->setToken($token);
            $authenticated = $pHandler->authenticate();
        }
    }

    if($authenticated){
        if($collid){
            if($occid || $catnum){
                $isEditor = $qHandler->validateEditor($collid);
                if($isEditor){
                    $qHandler->processImageUpload($_POST);
                }
                else{
                    echo 'ERROR: User does not have permission to upload images for this collection';
                }
            }
            else{
                echo 'ERROR: Missing occid or catnum';
            }
        }
        else{
            echo 'ERROR: Missing collid';
        }
    }
    else{
        echo 'ERROR: Could not authenticate user';
    }
}
else{
    echo 'ERROR: Missing image file';
}
?>
<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OpenIdProfileManager.php');
include_once($SERVER_ROOT . '/config/auth_config.php');
require_once($SERVER_ROOT . '/vendor/autoload.php');
use Jumbojett\OpenIDConnectClient;

$profManager = new OpenIdProfileManager();

$oidc = new OpenIDConnectClient($providerUrls['oid'], $clientIds['oid'], $clientSecrets['oid'], $providerUrls['oid']); // assumes that the issuer is identical to the providerUrl, as seems to be the case for microsoft

if(isset($shouldUpgradeInsecureRequests)){
  $oidc->setHttpUpgradeInsecureRequests($shouldUpgradeInsecureRequests);
}
if(isset($shouldVerifyPeers)){
  $oidc->setVerifyPeer($shouldVerifyPeers);
}


if (array_key_exists('code', $_REQUEST) && $_REQUEST['code']) {
  
  try{
    $status = $oidc->authenticate();
  }
  catch (Exception $ex){
    $_SESSION['last_message'] = 'Caught exception: ' . $ex->getMessage() . ' <ERR/>';
    header('Location:' . $CLIENT_ROOT . '/profile/index.php');
    exit();
  }  
  if($status){
    $sub = $oidc->requestUserInfo('sub');
    if($profManager->authenticate($sub, $providerUrls['oid'])){
      if($_SESSION['refurl']){
        header("Location:" . $_SESSION['refurl']);
        unset($_SESSION['refurl']);
      }
    }
    else {
      if ($email = $oidc->requestUserInfo('email')){
        // Authprovider returned a subscriber; however, user was not authenticated to local user account
        try{
          $status = $profManager->linkLocalUserOidSub($email, $sub, $oidc->getProviderURL());
        }catch (Exception $ex){
          $_SESSION['last_message'] = 'Caught exception: ' . $ex->getMessage();
          header('Location:' . $CLIENT_ROOT . '/profile/index.php');
          exit();
        }
        if($status){
          if($profManager->authenticate($sub, $providerUrls['oid'])){
            if($_SESSION['refurl']){
              header("Location:" . $_SESSION['refurl']);
              unset($_SESSION['refurl']);
            }
          }
          else{
            $_SESSION['last_message'] = "Unkown Error - Could not authenticate - try again later or alert a system admin <ERR/>";
            header('Location:' . $CLIENT_ROOT . '/profile/index.php');
            //@TODO Consider logging this error to PHP logfiles
          }
        }else{
          $_SESSION['last_message'] = "Error - Could not authenticate with Authentication provider <ERR/>";
          header('Location:' . $CLIENT_ROOT . '/profile/index.php');
        }
        
      }
      else{
        $_SESSION['last_message'] = "Unable to retrieve email address from authentication provider. <ERR/>";
        header('Location:' . $CLIENT_ROOT . '/profile/index.php');
      }
    }
  }
  $_SESSION['last_message'] = "Authentication failed. <ERR/>";
  header('Location:' . $CLIENT_ROOT . '/profile/index.php');
}
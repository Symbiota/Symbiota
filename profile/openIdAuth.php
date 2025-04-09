<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/vendor/autoload.php');
include_once($SERVER_ROOT . '/config/auth_config.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
if ($LANG_TAG != 'en' && file_exists($SERVER_ROOT . '/content/lang/profile/openIdAuth.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT . '/content/lang/profile/openIdAuth.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/profile/openIdAuth.en.php');

use Jumbojett\OpenIDConnectClient;

$AUTH_PROVIDER = $AUTH_PROVIDER ?? 'oid';
$oidc = new OpenIDConnectClient(
  $PROVIDER_URLS[$AUTH_PROVIDER],
  $CLIENT_IDS[$AUTH_PROVIDER],
  $CLIENT_SECRETS[$AUTH_PROVIDER]
);

$oidc->addScope(array('openid'));
$oidc->addScope(array('email'));
$oidc->setResponseTypes(array('code'));
//$oidc->setResponseTypes(array('id_token'));
$oidc->setRedirectUrl(GeneralUtil::getDomain() . $CLIENT_ROOT . $CALLBACK_REDIRECT);

if (isset($SHOULD_UPGRADE_INSECURE_REQUESTS)) {
  $oidc->setHttpUpgradeInsecureRequests($SHOULD_UPGRADE_INSECURE_REQUESTS);
}
if (isset($SHOULD_VERIFY_PEERS)) {
  $oidc->setVerifyPeer($SHOULD_VERIFY_PEERS);
}

// $_SESSION['oidIssuer'] = $oidc->getIssuer(); // moot for microsoft where it's the same as the providerUrl, but potentially useful for other auth providers?
$oidc->authenticate();

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">

<head>
  <title><?php echo $LANG['OPEN_ID_CONNECT_CLIENT']; ?></title>
</head>

<body>
  <h1 class="page-heading"><?php echo $LANG['OPEN_ID_CONNECT_CLIENT']; ?></h1>
  <div>
    Hello <?php echo $name; ?>
  </div>

</body>

</html>
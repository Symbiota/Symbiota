<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/vendor/capito/src/Cap.php');
include_once($SERVER_ROOT . '/vendor/capito/src/Interfaces/StorageInterface.php');
include_once($SERVER_ROOT . '/vendor/capito/src/Storage/FileStorage.php');
include_once($SERVER_ROOT . '/vendor/capito/src/RateLimiter.php');
include_once($SERVER_ROOT . '/vendor/capito/src/Exceptions/CapException.php');
use Capito\CapPhpServer\Cap;
use Capito\CapPhpServer\Storage\FileStorage;
//use Capito\CapPhpServer\Exceptions\CapException;

Language::load('security/human');

unset($_SESSION['captchaverified']);

if(!empty($_POST['cap-token'])){

    $capServer = new Cap(['storage' => new FileStorage(['path' => $TEMP_DIR_ROOT . '/cap_storage.json']) ]);
    $response = $capServer->validateToken($_POST['cap-token']);
    //TODO: Implement time horizon on validated session
    if($response['success'] == false){
        echo '<h2>'.(isset($LANG['CAPTCHA_FAILED'])?$LANG['CAPTCHA_FAILED']:'Captcha verification failed').'</h2>';
    }
    else{
        $_SESSION['captchaverified'] = $_POST['cap-token'];
        if (!empty($_SESSION['captcha_return_url'])){
            $ref_url = $_SESSION['captcha_return_url'];
            unset($_SESSION['captcha_return_url']);
            header('Location: ' . $ref_url);
            exit;
        }

        header('Location: ' . $CLIENT_ROOT);
        exit;
    }
}

else{
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' - CAPTCHA'; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
    include_once($SERVER_ROOT.'/includes/globalcaptchahead.php');
	?>
</head>
<body>
    <?php include_once($SERVER_ROOT.'/includes/globalcaptchabody.php');?>
</body>
</html>

<?php
};
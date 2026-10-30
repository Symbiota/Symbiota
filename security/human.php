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

    $capServer = new Cap([
        //Todo - consider moving this into a config file
        'challengeCount' => 3,          // 3 challenges (1–3 seconds to solve)   [== 5 higher sec]
        'challengeSize' => 16,          // 16-byte salt    
        'bruteForceLimit' => 3,         // 3 requests max per window              [==5 default limit]
        'bruteForceWindow' => 60,       // 60 second time window                  [==30 shorter window]
        'bruteForcePenalty' => 60,      // 60 second penalty when blocked         [==120 longer penalty]
        'challengeDifficulty' => 2,     // Difficulty 2 (balanced optimization)  [==3 hard]                     
        'difficultyModerate'=>3,      	// Difficulty level when moderate rate limiting pressure detected
        'difficultyAggressive'=>5,      // Difficulty level when high limiting pressure detected
        'tokenVerifyOnce' => true,      // One-time validation
        'challengeExpires' => 300,      // Expires in 5 minutes
        'tokenExpires' => 600,          // Token expires in 10 minutes  
        'storage' => new FileStorage(['path' => $TEMP_DIR_ROOT . '/cap_storage.json']) 
    ]);    
    
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
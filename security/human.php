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

if(!empty($_POST['cap-token'])){

    $capServer = new Cap(['storage' => new FileStorage(['path' => $TEMP_DIR_ROOT . '/cap_storage.json']) ]);
    $response = $capServer->validateToken($_POST['cap-token']);
    if($response['success'] == false){
        echo '<h2>'.(isset($LANG['CAPTCHA_FAILED'])?$LANG['CAPTCHA_FAILED']:'Captcha verification failed').'</h2>';
        $okToCreateLogin = false;
    }
    else{
        $_SESSION['captchaverified'] = $_POST['cap-token'];
    }
}

else{
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' - ' . $LANG['NEW_USER']; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
    <script src="<?=$CLIENT_ROOT?>/js/cap.js/widget/cap.min.js"></script>
	<script type="text/javascript">
		function validateform(f){
			
            let capToken = document.querySelector('input[name="cap-token"]');
            if (!(capToken && capToken.value !== '')){
                alert("<?php echo (isset($LANG['CHECK_CAPTCHA'])?$LANG['CHECK_CAPTCHA']:"You must first check the CAPTCHA checkbox (to prove you are a human)"); ?>");
                return false;
            }
			
            return true;
		}
	</script>
</head>
<body>
    <form action="human.php" method="post" onsubmit="return validateform(this);">
        <cap-widget data-cap-api-endpoint='<?=$CAPTCHA_ENDPOINT?>'></cap-widget>
		<button id="submit" name="submit" type="submit" value="Validate Human"><?php echo (isset($LANG['IM_HUMAN']) ? $LANG['IM_HUMAN'] : "I'm HUman"); ?></button>					
	</form>

    <script>
			const widget = document.querySelector("cap-widget");
			widget.addEventListener("solve", function (e) {
				const verificationToken = e.detail.token;
			});
			
			widget.addEventListener("error", function (e) {
				console.error('❌ Cap validation failed:', e.detail);
			});
		</script>
		<?php
	?>
</body>
</html>

<?php
};
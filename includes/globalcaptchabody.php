<form action="<?=$CLIENT_ROOT?>/security/human.php" method="post" onsubmit="return validateform(this);">
        <cap-widget data-cap-api-endpoint='<?=$CAPTCHA_ENDPOINT?>'></cap-widget>
		<button id="submit" name="submit" type="submit" value="Validate Human"><?php echo (isset($LANG['IM_HUMAN']) ? $LANG['IM_HUMAN'] : "I'm Human"); ?></button>					
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
</form>
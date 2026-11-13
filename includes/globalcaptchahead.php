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
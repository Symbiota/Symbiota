<?php global $SERVER_ROOT, $LANG_TAG, $CLIENT_ROOT;
$JS_LANG_TAG_SCRIPT_PATH = '/js/symb/' . $LANG_TAG . '.js';
$JS_LANG_TAG_SCRIPT = file_exists($SERVER_ROOT . $JS_LANG_TAG_SCRIPT_PATH) ? $JS_LANG_TAG_SCRIPT_PATH : '/js/symb/en.js';
?>
<script src="<?= $CLIENT_ROOT . $JS_LANG_TAG_SCRIPT ?>" type="text/javascript"></script>

<?php global $LANG, $LANG_TAG, $SERVER_ROOT;

include_once($SERVER_ROOT . '/content/lang/collections/editor/includes/requiredFieldInstruction.' . ($LANG_TAG ?? 'en') . '.php');
?>

<span style="color:red;">*</span> <?= $LANG['REQUIRED_FIELD'] ?>

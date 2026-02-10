<?php global $LANG, $LANG_TAG, $SERVER_ROOT;

include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/editor/includes/requiredFieldInstruction');

?>

<span style="color:red;">*</span> <?= $LANG['REQUIRED_FIELD'] ?>

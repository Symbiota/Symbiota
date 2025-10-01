<?php
global $LANG, $LANG_TAG, $SERVER_ROOT;

if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.en.php');

// Pass in through function scope;
$MAX_CUSTOM_INPUTS = $MAX_CUSTOM_INPUTS ?? 8;
$CUSTOM_TERMS = $CUSTOM_TERMS ?? [];
$CUSTOM_VALUES = $CUSTOM_VALUES ?? [];
$CUSTOM_FIELDS = $CUSTOM_FIELDS ?? [];

function selected(bool $v) {
	return $v ? 'SELECTED': '';
}

function onChange($index) {
	return 'customSelectChanged(' .  $index .')';
}
?> 

<div style="display:flex; flex-direction: column; gap:1rem;">
	<?php for($index = 1; $index <= $MAX_CUSTOM_INPUTS; $index++): ?>

	<?php
	$cAndOr = $CUSTOM_VALUES[$index]['andor'] ?? 'AND';
	$cOpenParen = $CUSTOM_VALUES[$index]['openparen'] ?? null;
	$cField = $CUSTOM_VALUES[$index]['field'] ?? null;
	$cTerm = $CUSTOM_VALUES[$index]['term'] ?? null;
	$cValue = $CUSTOM_VALUES[$index]['value'] ?? null;
	$cCloseParen = $CUSTOM_VALUES[$index]['closeparen'] ?? null;
	$divDisplay = 'none';

	if($index == 1 || $cValue != '' || $cTerm == 'IS_NULL' || $cTerm == 'NOT_NULL') {
		$divDisplay = 'flex';
	}
	?>

	<div id="customdiv<?= $index ?>" style="align-items:center; gap:0.5rem; display: <?= $divDisplay ?>" >
		<?= $LANG['CUSTOM_FIELD'] . ' ' . $index; ?>:
		<?php if($index > 1): ?> 
		<select 
			name="q_customandor<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
		>
			<option value="AND">
				<?= $LANG['AND'] ?>
			</option>
			<option <?= selected($cAndOr == 'OR') ?> value="OR">
				<?php echo $LANG['OR']; ?>
			</option>
		</select>
		<?php endif ?> 

		<select name="q_customopenparen<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['OPEN_PAREN_FIELD']; ?>">
			<option value="">---</option>
			<option value="(" <?= selected($cOpenParen == '(') ?>>(</option>
			
			<?php if($index < ($MAX_CUSTOM_INPUTS - 1)): ?>
			<option value="((" <?= selected($cOpenParen == '((') ?>>((</option>
			<?php endif ?>

			<?php if($index < $MAX_CUSTOM_INPUTS): ?>
			<option value="(((" <?= selected($cOpenParen == '(((') ?>>(((</option>
			<?php endif ?>
		</select>

		<select name="q_customfield<?= $index; ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['CRITERIA']; ?>">
			<option value=""><?= $LANG['SELECT_FIELD_NAME']; ?></option>
			<option value="">---------------------------------</option>
			<?php foreach($CUSTOM_FIELDS as $k => $v): ?>
			<option value="<?= $k ?>" <?= selected($k == $cField) ?>>
				<?= $v ?>
			</option>
			<?php endforeach?>
		</select>

		<select name="q_customtype<?= $index; ?>" aria-label="<?= $LANG['CONDITION']; ?>">
			<?php foreach($CUSTOM_TERMS as $term): ?>
			<option <?= selected($cTerm == $term)?> value="<?= $term ?>">
				<?= $LANG[$term] ?>
			</option>
			<?php endforeach ?>
		</select>

		<input name="q_customvalue<?= $index; ?>" type="text" value="<?= $cValue; ?>" style="width:200px; margin:0; padding-top: 0; padding-bottom: 0;" aria-label="<?= $LANG['CRITERIA']; ?>"/>

		<select name="q_customcloseparen<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['CLOSE_PAREN_FIELD']; ?>">
			<option value="">---</option>
			<option value=")" <?= selected($cCloseParen == ')') ?>>)</option>
			
			<?php if($index < ($MAX_CUSTOM_INPUTS - 1)): ?>
			<option value="))" <?= selected($cCloseParen == '))') ?>>))</option>
			<?php endif ?>

			<?php if($index < $MAX_CUSTOM_INPUTS): ?>
			<option value=")))" <?= selected($cCloseParen == ')))') ?>>)))</option>
			<?php endif ?>
		</select>

		<?php if($index < $MAX_CUSTOM_INPUTS): ?>
		<a href="#" style="height:1.2em;" onclick="if(document.getElementById('customdiv<?= $index +1 ?>')) {document.getElementById('customdiv<?= $index +1 ?>').style.display='flex'};return false;"><img class="editimg" src="../../images/plus.png" style="display:inline-block;width:1.2em;height:1.2em;" alt="<?php echo htmlspecialchars($LANG['ADD_CUSTOM_FIELD'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" /></a>

		<?php endif ?>

		<?php if($index > 1): ?>
		<a href="#" style="height:1.2em;" onclick="if(document.getElementById('customdiv<?= $index ?>')) {document.getElementById('customdiv<?= $index ?>').style.display='none'};return false;">
		<img class="editimg" src="../../images/minus.png" style="display:inline-block;width:1.2em;" alt="<?php echo htmlspecialchars($LANG['ADD_CUSTOM_FIELD'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" />
		</a>
		<?php endif ?>
	</div>
	<?php endfor ?>
</div>

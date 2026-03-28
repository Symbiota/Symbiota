<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ProfileManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('profile/userprofile');

header('Content-Type: text/html; charset='.$CHARSET);

$userId = Sanitize::int($_REQUEST['userid']);

$pHandler = new ProfileManager();
$pHandler->setUid($userId);
$person = $pHandler->getPerson();
$isAccessiblePreferred = $pHandler->getAccessibilityPreference($SYMB_UID);

$isSelf = true;
if($userId != $SYMB_UID) $isSelf = false;

$isEditor = false;
if(isset($SYMB_UID) && $SYMB_UID){
	if($isSelf || $IS_ADMIN) $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $LANG['DETAILS']; ?></title>
	</head>
	<body>
		<?php
		if($isEditor){
			?>
			<div style="padding:15px;">
				<div>
					<h1 class="page-heading screen-reader-only"><?= $LANG['DETAILS']; ?></h1>
				</div>
				<section class="fieldset-like">
					<h2><span><?= $LANG['DETAILS']; ?></span></h2>
					<div>
						<div style="margin:20px;">
							<?php
							echo '<div>'.$person->getFirstName().' '.$person->getLastName().'</div>';
							if($person->getEmail()) echo '<div>'.$person->getEmail().'</div>';
							if($person->getGUID()){
								$guid = $person->getGUID();
								if(preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/',$guid)) $guid = 'https://orcid.org/'.$guid;
								echo '<div>';
								if(substr($guid,0,4) == 'http') echo '<a href="'.$guid.'" target="_blank">';
								echo $guid;
								if(substr($guid,0,4) == 'http') echo '</a>';
								echo '</div>';
							}
							if($person->getTitle()) echo '<div>'.$person->getTitle().'</div>';
							if($person->getInstitution()) echo '<div>'.$person->getInstitution().'</div>';
							$cityStateStr = trim($person->getCity().', '.$person->getState().' '.$person->getZip(),' ,');
							if($cityStateStr) echo '<div>'.$cityStateStr.'</div>';
							if($person->getCountry()) echo '<div>'.$person->getCountry().'</div>';
							echo '<div>Login name: '.($person->getUserName()?$person->getUserName():'not registered').'</div>';
							?>
							<div style="font-weight:bold;margin-top:10px;">
								<div><a href="#" onclick="toggleEditingTools('profileeditdiv');return false;"><?= $LANG['EDIT_PROFILE'] ?></a></div>
								<div><a href="#" onclick="toggleEditingTools('pwdeditdiv');return false;"><?= $LANG['CHANGE_PASSWORD'] ?></a></div>
								<div><a href="#" onclick="toggleEditingTools('logineditdiv');return false;"><?= $LANG['CHANGE_LOGIN'] ?></a></div>
								<div><a href="#" onclick="toggleEditingTools('managetokensdiv');return false;"><?= $LANG['MANAGE_ACCESS'] ?></a></div>
							</div>
						</div>
					</div>
					<div id="profileeditdiv" style="display:none;margin:15px;">
						<form name="editprofileform" action="viewprofile.php" method="post">
							<fieldset>
								<legend><b><?= $LANG['EDIT_U_PROFILE'] ?></b></legend>
								<table style="width:100%; border-spacing: 1px;">
									<tr>
										<td><b><?= $LANG['FIRST_NAME'] ?>:</b></td>
										<td>
											<div>
												<input id="firstname" name="firstname" size="40" value="<?= $person->getFirstName();?>" required />
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['LAST_NAME'] ?>:</b></td>
										<td>
											<div>
												<input id="lastname" name="lastname" size="40" value="<?= $person->getLastName();?>" required />
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['EMAIL'] ?>:</b></td>
										<td>
											<div>
												<input id="email" name="email" type="email" size="40" value="<?= $person->getEmail();?>" required />
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['ACCESSIBILITY_PREF'] ?>:</b></td>
										<td>
											<div>
												<input type="checkbox" name="accessibility-pref" id="accessibility-pref" value="1" <?= $isAccessiblePreferred ? 'checked' : ''; ?> />
												<label for="accessibility-pref"><?= $LANG['ACCESSIBILITY_PREF_DESC'] ?></label>
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['ORCID'] ?>:</b></td>
										<td>
											<div>
												<input name="guid" type="text" size="40" value="<?= $person->getGUID();?>" />
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['TITLE'] ?>:</b></td>
										<td>
											<div>
												<input name="title"  size="40" value="<?= $person->getTitle();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['INSTITUTION'] ?>:</b></td>
										<td>
											<div>
												<input name="institution"  size="40" value="<?= $person->getInstitution();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['CITY'] ?>:</b></td>
										<td>
											<div>
												<input id="city" name="city" size="40" value="<?= $person->getCity();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['STATE'] ?>:</b></td>
										<td>
											<div>
												<input id="state" name="state" size="40" value="<?= $person->getState();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['ZIP_CODE'] ?>:</b></td>
										<td>
											<div>
												<input name="zip" size="40" value="<?= $person->getZip();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td><b><?= $LANG['COUNTRY'] ?>:</b></td>
										<td>
											<div>
												<input id="country" name="country" size="40" value="<?= $person->getCountry();?>">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div style="margin:10px;">
												<input type="hidden" name="userid" value="<?= $userId;?>" />
												<button type="submit" name="action" value="Submit Edits"><?= $LANG['SUBMIT_EDITS'] ?></button>
											</div>
										</td>
									</tr>
								</table>
							</fieldset>
						</form>
						<form name="delprofileform" action="viewprofile.php" method="post" onsubmit="return window.confirm('<?= $LANG['SURE_DELETE'] ?>');">
							<fieldset style="padding:15px;width:200px;">
								<legend><b><?= $LANG['DELETE_PROF'] ?></b></legend>
								<input type="hidden" name="userid" value="<?= $userId;?>" />
								<button type="submit" name="action" class="button-danger" value="deleteProfile"><?= $LANG['DELETE_PROF'] ?></button>
							</fieldset>
						</form>
					</div>
					<div id="pwdeditdiv" style="display:none;margin:15px;">
						<form name="changepwdform" action="viewprofile.php" method="post" onsubmit="return verifyPwdForm(this);">
							<fieldset style='padding:15px;width:550px;'>
								<legend><b><?= $LANG['CHANGE_PASSWORD'] ?></b></legend>
								<table>
									<?php
									if($isSelf){
										?>
										<tr>
											<td>
												<b><?= $LANG['CURRENT_PWORD'] ?>:</b>
											</td>
											<td>
												<input id="oldpwd" name="oldpwd" type="password"/>
											</td>
										</tr>
										<?php
									}
									?>
									<tr>
										<td>
											<b><?= $LANG['NEW_PWORD'] ?>:</b>
										</td>
										<td>
											<input id="newpwd" name="newpwd" type="password" minlength="10">
										</td>
									</tr>
									<tr>
										<td>
											<b><?= $LANG['PWORD_AGAIN'] ?>:</b>
										</td>
										<td>
											<input id="newpwd2" name="newpwd2" type="password" minlength="10">
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="hidden" name="userid" value="<?= $userId;?>" />
											<button type="submit" name="action" value="Change Password"><?= $LANG['CHANGE_PASSWORD'] ?></button>
										</td>
									</tr>
								</table>
							</fieldset>
						</form>
					</div>
					<div id="logineditdiv" style="display:none;margin:15px;">
						<fieldset style='padding:15px;width:550px;'>
							<legend><b><?= $LANG['CHANGE_USERNAME'] ?></b></legend>
							<form name="modifyloginform" action="viewprofile.php" method="post" onsubmit="return verifyModifyLoginForm(this);">
								<div><b><?= $LANG['NEW_USERNAME'] ?>:</b> <input name="newlogin" type="text" /></div>
								<?php
								if($isSelf){
									?>
									<div><b><?= $LANG['CURRENT_PWORD'] ?>:</b> <input name="newloginpwd" id="newloginpwd" type="password" /></div>
									<?php
								}
								?>
								<div style="margin:10px">
									<input type="hidden" name="userid" value="<?= $userId;?>" />
									<button type="submit" name="action" value="changeLogin"><?= $LANG['CHANGE_USERNAME'] ?></button>
								</div>
							</form>
						</fieldset>
					</div>
					<div id="managetokensdiv" style="display:none;margin:15px;">
						<fieldset style='padding:15px;width:550px;'>
							<legend><b><?= (isset($LANG['MANAGE_TOKENS'])?$LANG['MANAGE_TOKENS']:'Manage Access Tokens'); ?></b></legend>
							<form name="cleartokenform" action="viewprofile.php" method="post" onsubmit="">
								<div>
								<?php
								$tokenCount = $pHandler->getTokenCnt();
								echo (isset($LANG['YOU_HAVE'])?$LANG['YOU_HAVE']:'You currently have').' <b>'.($tokenCount?$tokenCount:0).' </b>'.
								(isset($LANG['EXPLAIN_TOKENS'])?$LANG['EXPLAIN_TOKENS']:''); ?>
								</div>
								<div style="margin:10px">
									<input type="hidden" name="userid" value="<?= $userId;?>" />
									<button type="submit" name="action" value="Clear Tokens"><?= $LANG['CLEAR_TOKENS'] ?></button>
								</div>
							</form>
						</fieldset>
					</div>
					<div>
						<div>
							<b><span style="text-decoration: underline;"><?= $LANG['TAXON_RELS'] ?></span></b>
							<a href="#" onclick="toggle('addtaxonrelationdiv')" title="<?= $LANG['ADD_TAXON_REL'] ?>" aria-label="<?= $LANG['CREATE_TAXON_REL'] ?>" >
								<img style='border:0px;width:1.3em;' src='../images/add.png' alt='<?= $LANG['ADD_ICON'] ?>'/>
							</a>
						</div>
						<div id="addtaxonrelationdiv" style="display:none;">
							<fieldset style="padding:20px;margin:15px;">
								<legend><b><?= $LANG['NEW_TAX_REGION'] ?></b></legend>
								<div style="margin-bottom:10px;">
									<?= $LANG['TAX_FORM'] ?>
								</div>
								<form name="addtaxonomyform" action="viewprofile.php" method="post" onsubmit="return verifyAddTaxonomyForm(this)">
									<div style="margin:3px;">
										<b><?= $LANG['TAXON'] ?></b><br/>
										<input id="taxoninput" name="taxon" type="text" value="" style="width:90%;" onfocus="initTaxonAutoComplete()" />
									</div>
									<div style="margin:3px;">
										<b><?= $LANG['SCOPE_OF_REL'] ?></b><br/>
										<select name="editorstatus">
											<option value="RegionOfInterest"><?= $LANG['REGION'] ?></option>
											<!-- <option value="OccurrenceEditor">Occurrence Editor</option> -->
										</select>
									</div>
									<div style="margin:3px;">
										<b><?= $LANG['SCOPE_LIMITS'] ?></b><br/>
										<input name="geographicscope" type="text" value="" style="width:90%;"/>
									</div>
									<div style="margin:3px;">
										<b><?= $LANG['NOTES'] ?></b><br/>
										<input name="notes" type="text" value="" style="width:90%;" />
									</div>
									<div style="margin:20px 10px;">
										<button name="action" type="submit" value="Add Taxonomic Relationship"><?= $LANG['ADD_TAX'] ?></button>
									</div>
								</form>
							</fieldset>
						</div>
						<?php
						$userTaxonomy = $person->getUserTaxonomy();
						if($userTaxonomy){
							ksort($userTaxonomy);
							foreach($userTaxonomy as $cat => $userTaxArr){
								if($cat == 'RegionOfInterest') $cat = $LANG['REGION'];
								elseif($cat == 'OccurrenceEditor') $cat = $LANG['OCC_EDIT'];
								elseif($cat == 'TaxonomicThesaurusEditor') $cat = $LANG['TAX_THES'];
								echo '<div style="margin:10px;">';
								echo '<div><b>'.$cat.'</b></div>';
								echo '<ul style="margin:10px;">';
								foreach($userTaxArr as $utid => $utArr){
									echo '<li>';
									echo $utArr['sciname'];
									if($utArr['geographicScope']) echo ' - '.$utArr['geographicScope'].' ';
									if($utArr['notes']) echo ', '.$utArr['notes'];
									echo ' <a href="viewprofile.php?action=delusertaxonomy&utid=' . $utid . '&userid=' . $userId . '"><img src="../images/drop.png" style="width:1.2em;" /></a>';
									echo '</li>';
								}
								echo '</ul>';
								echo '</div>';
							}
						}
						else{
							echo '<div style="margin:20px;">' . $LANG['NO_RELS'] . '</div>';
						}
						?>
					</div>
				</section>
			</div>
			<?php
		}
		?>
	</body>
</html>

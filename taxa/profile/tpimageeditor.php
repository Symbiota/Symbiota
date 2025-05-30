<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TPImageEditorManager.php');
include_once($SERVER_ROOT . '/classes/Media.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/taxa/profile/tpimageeditor.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT.'/content/lang/taxa/profile/tpimageeditor.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/taxa/profile/tpimageeditor.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$tid = filter_var($_REQUEST['tid'], FILTER_SANITIZE_NUMBER_INT);
$category = array_key_exists('cat', $_REQUEST) ? $_REQUEST['cat'] : '';

$imageEditor = new TPImageEditorManager();
$isEditor = false;

if($tid){
	$imageEditor->setTid($tid);
	if($IS_ADMIN || array_key_exists('TaxonProfile', $USER_RIGHTS)) $isEditor = true;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?><?= $LANG['TP_IMAGE_EDITOR']; ?></title>
	<style>
		.div--overflow-wrap-anywhere {
			overflow-wrap: anywhere;
			width: 100%;
		}
		.screen-reader-only {
			position: absolute;
			left: -10000px;
		}
	</style>
</head>
<body>
	<div role="main" id="innertext" style="background-color:white;">
		<h1 class="page-heading screen-reader-only"><?= $LANG['TP_IMAGE_EDITOR'] ?></h1>
		<?php
		if($isEditor && $tid){
			if($category == "imagequicksort"){
				if($images = $imageEditor->getImages()){
					?>
					<div style='clear:both;'>
						<form action='tpeditor.php' method='post' target='_self'>
							<table border='0' cellspacing='0'>
								<tr>
									<?php
									$imgCnt = 0;
									foreach($images as $mediaID => $imgArr){
										$displayUrl = !empty($imgArr["thumbnailUrl"]) ? $imgArr["thumbnailUrl"] : $imgArr["url"];
										if($imgArr['mediaType'] === 'audio') {
											$displayUrl = $CLIENT_ROOT . '/images/speaker_thumbnail.png';
										}
										if($displayUrl && substr($displayUrl,0,10) != 'processing'){
											$webUrl = $imgArr['url'] ?? '';
											if($GLOBALS['MEDIA_DOMAIN']){
												if(substr($webUrl, 0, 1) == '/') $webUrl = $GLOBALS['MEDIA_DOMAIN'] . $imgArr['url'];
												if(substr($imgArr['thumbnailUrl'], 0, 1) == '/') $displayUrl = $GLOBALS['MEDIA_DOMAIN'] . $imgArr['thumbnailUrl'];
											}
											?>
											<td align='center' valign='bottom'>
												<div style='margin:20px 0px 0px 0px;'>
													<a href="<?php echo htmlspecialchars($webUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" target="_blank" tabindex="-1">
														<img width="150" src="<?php echo $displayUrl;?>" />
													</a>

												</div>
												<?php
												if($imgArr["creatorDisplay"]){
													?>
													<div>
														<?php echo $imgArr["creatorDisplay"];?>
													</div>
													<?php
												}
												if($imgArr["tid"] != $tid){
													?>
													<div>
														<a href="tpeditor.php?tid=<?php echo htmlspecialchars($imgArr["tid"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);?>" target="" title="<?= $LANG['IMAGE_LINKED_TO'] ?>" tabindex="-1"><?php echo htmlspecialchars($imgArr["sciname"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);?></a>
													</div>
													<?php
												}
												?>
												<div style='margin-top:2px;'>
												<?php
													$sortSequence = !empty($imgArr["sortSequence"]) ? $imgArr["sortSequence"] : $LANG['NOT_SET'];
													echo $LANG['SORT_SEQUENCE'] . ': <b>' . $sortSequence . '</b>';
												?>
												</div>
												<div>
													<?php echo $LANG['NEW_VALUE']; ?>:
													<input name="imgid-<?= $mediaID ?>" type="text" size="5" maxlength="5" />
												</div>
											</td>
											<?php
											$imgCnt++;
											if($imgCnt%5 == 0){
												?>
												</tr>
												<tr>
													<td colspan='5'>
														<hr>
														<div style='margin-top:2px;'>
															<button type='submit' name='action' id='submit' value='Submit Image Sort Edits'><?php echo $LANG['SUBMIT_SORT_EDITS']; ?></button>
														</div>
													</td>
												</tr>
												<tr>
												<?php
											}
										}
									}
									for($i = (5 - $imgCnt%5);$i > 0; $i--){
										echo "<td>&nbsp;</td>";
									}
									?>
								</tr>
							</table>
							<input name='tid' type='hidden' value='<?php echo $imageEditor->getTid(); ?>'>
							<input type="hidden" name="tabindex" value="2" />
							<?php
							if($imgCnt%5 != 0) echo "<div style='margin-top:2px;'><button type='submit' name='action' id='imgsortsubmit' value='Submit Image Sort Edits'>" . $LANG['SUBMIT_SORT_EDITS'] . "</button></div>\n";
							?>
						</form>
					</div>
					<?php
				}
				else{
					echo '<h2>'.$LANG['NO_IMAGES'].'.</h2>';
				}
			}
			elseif($category == "imageadd"){
				?>
				<div style='clear:both;'>
					<form enctype='multipart/form-data' action='tpeditor.php' id='imageaddform' method='post' target='_self' onsubmit='return submitAddImageForm(this);'>
						<fieldset style='margin:15px;padding:15px;width:90%;'>
							<legend><b><?php echo $LANG['ADD_NEW_IMAGE']; ?></b></legend>
							<div style='padding:10px;border:1px solid #c2c2c2;background-color:#f7f7f7;'>
								<div class="targetdiv" style="display:block;">
									<div style="font-weight:bold;margin-bottom:5px;">
										<?php echo $LANG['SELECT_IMAGE_TO_UPLOAD']; ?>:
									</div>
									<!-- following line sets MAX_FILE_SIZE (must precede the file input field)  -->
									<input type='hidden' name='MAX_FILE_SIZE' value='4000000' />
									<div>
										<input name='imgfile' id='imgfile' type='file' size='70'/>
									</div>
									<div style="margin-left:10px;">
										<input type="checkbox" name="createlargeimg" value="1" /> <?php echo $LANG['KEEP_LARGE_IMG']; ?>
									</div>
									<div style="margin-left:10px;"><?php echo $LANG['IMG_SIZE_NO_GREATER']; ?></div>
									<div style="margin:10px 0px 0px 350px;cursor:pointer;text-decoration:underline;font-weight:bold;" onclick="toggle('targetdiv')">
										<?php echo $LANG['LINK_TO_EXTERNAL']; ?>
									</div>
								</div>
								<div class="targetdiv" style="display:none;">
									<div style="font-weight:bold;margin-bottom:5px;">
										<?php echo $LANG['ENTER_URL_IMG']; ?>:
									</div>
									<div>
										URL:
										<input type='text' name='originalUrl' size='70'/>
									</div>
									<div style="margin-left:10px;">
										<input type="checkbox" name="importurl" value="1" /> <?php echo $LANG['IMPORT_IMG_LOCAL']; ?>
									</div>
									<div style="margin:10px 0px 0px 350px;cursor:pointer;text-decoration:underline;font-weight:bold;" onclick="toggle('targetdiv')">
										<?php echo $LANG['UPLOAD_LOCAL']; ?>
									</div>
								</div>
							</div>

							<!-- Image metadata -->
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['CAPTION']; ?>:</b>
								<input name='caption' type='text' value='' size='25' maxlength='100'>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['CREATOR']; ?>:</b>
								<select name='creatorUid' name='creatorUid'>
									<option value=""><?php echo $LANG['SEL_CREATOR']; ?></option>
									<option value="">---------------------------------------</option>
									<?php $imageEditor->echoCreatorSelect($PARAMS_ARR["uid"]); ?>
								</select>
								<a href="#" onclick="toggle('photooveridediv');return false;" title="<?php echo $LANG['DISP_CREATOR_OVERRIDE']; ?>">
									<img src="../../images/editplus.png" style="border:0px;width:1.5em;" />
								</a>
							</div>
							<div id="photooveridediv" style='margin:2px 0px 5px 10px;display:none;'>
								<b><?php echo $LANG['CREATOR_OVERRIDE']; ?>:</b>
								<input name='creator' type='text' value='' size='37' maxlength='100'><br/>
								* <?php echo $LANG['CREATOR_OVERRIDE_EXPLAIN']; ?>
							</div>
							<div style="margin-top:2px;" title="Use if manager is different than creator">
								<b><?php echo $LANG['MANAGER']; ?>:</b>
								<input name='owner' type='text' value='' size='35' maxlength='100'>
							</div>
							<div style='margin-top:2px;' title="<?php echo $LANG['URL_TO_SOURCE']; ?>">
								<b><?php echo $LANG['SOURCE_URL']; ?>:</b>
								<input name='sourceUrl' type='text' value='' size='70' maxlength='250'>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['COPYRIGHT']; ?>:</b>
								<input name='copyright' type='text' value='' size='70' maxlength='250'>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['OCC_REC_NUM']; ?>:</b>
								<input id="imgoccid-0" name="occid" type="text" value=""/>
								<a href="#" onclick="openOccurrenceSearch('0')"><?php echo $LANG['LINK_TO_OCC']; ?></a>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['LOCALITY']; ?>:</b>
								<input name='locality' type='text' value='' size='70' maxlength='250'>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['NOTES']; ?>:</b>
								<input name='notes' type='text' value='' size='70' maxlength='250'>
							</div>
							<div style='margin-top:2px;'>
								<b><?php echo $LANG['SORT_SEQUENCE']; ?>:</b>
								<input name='sortSequence' type='text' value='' size='5' maxlength='5'>
							</div>
							<input name="tid" type="hidden" value="<?php echo $imageEditor->getTid();?>">
							<input type="hidden" name="tabindex" value="1" />
							<div style='margin-top:2px;'>
								<button type='submit' name='action' id='imgaddsubmit' value='Upload Image' onclick="return submitAddForm(this.form);"><?php echo $LANG['UPLOAD_IMAGE']; ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			else{
				if($images = $imageEditor->getImages()){
					?>
					<div style='clear:both;'>
						<section class="gridlike-form">
							<?php
							foreach($images as $mediaID => $imgArr){
								?>
								<section class="gridlike-form-row bottom-breathing-room-rel">
									<div>
										<div style="margin:20px;float:left;text-align:center;">
											<?php
											try {
												echo '<a>';
												echo Media::render_media_item($imgArr);
												echo '</a>';
											} catch(Exception $e) {
												error_log($e->getMessage());
											}

											if($imgArr['originalUrl']) {
												echo'<br>';
												echo Media::render_media_link(
														$imgArr['originalUrl'],
														$LANG['OPEN_LARGE_IMAGE']
													);
											}
											?>
										</div>
									</div>
									<div class="div--overflow-wrap-anywhere">
										<?php
										if($imgArr['occid']){
											?>
											<div style="float:right;margin-right:10px;" title="<?php echo $LANG['MUST_HAVE_EDIT_PERM']; ?>">
												<a href="../../collections/editor/occurrenceeditor.php?occid=<?php echo htmlspecialchars($imgArr['occid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>&tabtarget=2" target="_blank">
													<img src="../../images/edit.png" style="width:1.3em;border:0px;"/>
												</a>
											</div>
											<?php
										}
										else{
											?>
											<div style='float:right;margin-right:10px;'>
												<a href="../../imagelib/imgdetails.php?mediaid=<?= $mediaID ?>&emode=1">
													<img src="../../images/edit.png" style="width:1.3em;border:0px;" />
												</a>
											</div>
											<?php
										}
										?>
										<div style='margin:60px 0px 10px 10px;clear:both;'>
											<?php
											if($imgArr["tid"] != $tid){
												?>
												<div>
													<b><?php echo $LANG['IMAGE_LINKED_TO']; ?>:</b>
													<a href="tpeditor.php?tid=<?php echo htmlspecialchars($imgArr["tid"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);?>" target=""><?php echo htmlspecialchars($imgArr["sciname"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);?></a>
												</div>
												<?php
											}
											if(!empty($imgArr['caption'])){
												?>
												<div>
													<b><?php echo $LANG['CAPTION']; ?>:</b>
													<?php echo $imgArr['caption'];?>
												</div>
												<?php
											}
											?>
											<div>
												<b><?php echo $LANG['CREATOR']; ?>:</b>
												<?php
													if (!empty($imgArr['creatorDisplay']))
														echo $imgArr['creatorDisplay'];
													else
														echo $LANG['NOT_SPECIFIED']
												?>
											</div>
											<?php
											if(!empty($imgArr['owner'])){
												?>
												<div>
													<b><?php echo $LANG['MANAGER']; ?>:</b>
													<?php echo $imgArr['owner'];?>
												</div>
												<?php
											}
											if(!empty($imgArr['sourceUrl'])){
												$sourceUrl = htmlspecialchars($imgArr['sourceUrl'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
												?>
												<div>
													<b><?php echo $LANG['SOURCE_URL']; ?>:</b>
													<a href="<?= $sourceUrl ?>" target="_blank"><?= $sourceUrl ?></a>
												</div>
												<?php
											}
											if(!empty($imgArr['copyright'])){
												?>
												<div>
													<b><?php echo $LANG['COPYRIGHT']; ?>:</b>
													<?php echo $imgArr['copyright'];?>
												</div>
												<?php
											}
											if(!empty($imgArr['locality'])){
												?>
												<div>
													<b><?php echo $LANG['LOCALITY']; ?>:</b>
													<?php echo $imgArr['locality'];?>
												</div>
												<?php
											}
											if(!empty($imgArr['occid'])){
												?>
												<div>
													<b><?php echo $LANG['OCC_REC_NUM']; ?>:</b>
													<a href="<?= $CLIENT_ROOT ?>/collections/individual/index.php?occid=<?php echo htmlspecialchars($imgArr["occid"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
														<?php echo $imgArr["occid"];?>
													</a>
												</div>
												<?php
											}
											if(!empty($imgArr['notes'])){
												?>
												<div>
													<b><?php echo $LANG['NOTES']; ?>:</b>
													<?php echo $imgArr['notes'];?>
												</div>
												<?php
											}
											if(!empty($imgArr['sortSequence'])){
												?>
												<div>
													<b><?php echo $LANG['SORT_SEQUENCE']; ?>:</b>
													<?php echo $imgArr['sortSequence'];?>
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</section>
								<div>
									<hr/>
								</div>
								<?php
							}
							?>
						</section>
					</div>
					<?php
				}
				else{
					echo '<h2>' . $LANG['NO_IMAGES'] . '</h2>';
				}
			}
		}
		?>
	</div>
</body>

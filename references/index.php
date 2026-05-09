<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../references/index.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

Language::load([
	'references/index'
]);

$refId = array_key_exists('refid', $_REQUEST) ? Sanitize::int($_REQUEST['refid']) : 0;
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$refManager = new ReferenceManager();

$refArr = [];
$refExist = false;

$isEditor = false;
if($IS_ADMIN) $isEditor = true;

$statusStr = '';

if(!$formSubmit || $formSubmit != 'Search References'){
	$refArr = $refManager->getRefList('');
	foreach($refArr as $valueArr){
		if(!empty($valueArr["bibliographicCitation"])){
			$refExist = true;
		}
	}
}

if($formSubmit == 'Search References'){
	$refArr = $refManager->getRefList($_POST['searchcitation'] ?? '');
	foreach($refArr as $valueArr){
		if(!empty($valueArr["bibliographicCitation"])){
			$refExist = true;
		}
	}
}

?>
<!DOCTYPE HTML>
<html lang="<?php echo $LANG_TAG ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?> Reference List</title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?= $CSS_BASE_PATH ?>/searchStyles.css" rel="stylesheet" type="text/css">
	<link href="<?= $CSS_BASE_PATH ?>/searchStylesInner.css" rel="stylesheet" type="text/css">
	<link href="<?= $CSS_BASE_PATH ?>/tables.css" rel="stylesheet" type="text/css">
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/symb/references.index.js"></script>

	<style>
		.reference-layout{
			display:grid;
			grid-template-columns: 1fr 320px;
			gap:1.5rem;
			align-items:start;
		}

		.reference-sidebar{
			position:sticky;
			top:1rem;
		}

		.reference-panel{
			background:#fff;
			border:1px solid #d2d2d2;
			border-radius:10px;
			padding:1rem;
			box-shadow:0 1px 4px rgba(0,0,0,0.05);
		}

		.reference-list{
			list-style:none;
			padding:0;
			margin:0;
		}

		.reference-item{
			padding:0.9rem 1rem;
			border-bottom:1px solid #d2d2d2;
			transition:background-color 0.15s ease;
		}

		.reference-item:last-child{
			border-bottom:none;
		}

		.reference-item:hover{
			background:#f8fafc;
		}

		.reference-item a{
			text-decoration:none;
			font-size:1rem;
			line-height:1.5;
			display:block;
		}

		.reference-item a:hover{
			text-decoration:underline;
		}

		.status-msg{
			padding:1rem;
			margin-bottom:1rem;
			border-radius:8px;
			background:#fff3f3;
			color:#9f3a38;
		}

		.empty-state{
			padding:2rem;
			text-align:center;
			font-size:1.1rem;
			color:#666;
		}

		.sidebar-button-link{
			display:block;
			width:100%;
			text-decoration:none;
		}

		.sidebar-button-link button{
			width:100%;
		}

		@media(max-width:900px){
			.reference-layout{
				grid-template-columns:1fr;
			}

			.reference-sidebar{
				position:static;
			}
		}
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($reference_indexMenu)?$reference_indexMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?= htmlspecialchars($LANG['HOME'] ?? 'Home'); ?></a> &gt;&gt;
		<a href="index.php"><b><?= htmlspecialchars($LANG['REFERENCES'] ?? 'References'); ?></b></a>
	</div>
	<div role="main" id="innertext" class="inner-search" style="max-width:1400px;">
		<h1 class="page-heading"><?= htmlspecialchars($LANG['REFERENCES'] ?? 'References'); ?></h1>

		<?php if($statusStr){ ?>
			<div class="status-msg">
				<?= htmlspecialchars($statusStr); ?>
			</div>
		<?php } ?>

		<div class="reference-layout">
			<section>
				<div class="reference-panel">
					<?php if($refExist) { ?>
						<ul class="reference-list">
							<?php foreach($refArr as $refId => $recArr) { ?>
								<li class="reference-item">
									<a href="publicref.php?refid=<?= (int)$refId; ?>">
										<?= htmlspecialchars(
											$recArr["bibliographicCitation"],
											ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE
										); ?>
									</a>
								</li>
							<?php }; ?>
						</ul>
					<?php } elseif(($formSubmit == 'Search References') && !$refExist) { ?>
						<div class="empty-state">
							<?= htmlspecialchars($LANG['NO_REF_MATCH'] ?? 'There were no references matching your criteria.'); ?>
						</div>
					<?php } else { ?>
						<div class="empty-state">
							<?= htmlspecialchars($LANG['NO_REF_EXIST'] ?? 'There are currently no references in the database.'); ?>
						</div>
					<?php }; ?>
				</div>
			</section>

			<!-- Sidebar -->
			<aside class="reference-sidebar">
				<div class="reference-panel">
					<form name="filterrefform" action="index.php" method="post">
						<h2 style="margin-top:0;">
							<?= htmlspecialchars($LANG['FILTER_LIST'] ?? 'Filter List'); ?>
						</h2>
						<div class="input-text-container">
							<label for="searchcitation" class="input-text--outlined">
								<span class="screen-reader-only">
									<?= htmlspecialchars($LANG['CITATION'] ?? 'Citation'); ?>
								</span>
								<input
									type="text"
									name="searchcitation"
									id="searchcitation"
									value="<?=($formSubmit == 'Search References') ? htmlspecialchars($_POST['searchcitation'] ?? '') : ''?>"
								/>
								<span class="inset-input-label">
									<?= htmlspecialchars($LANG['CITATION'] ?? 'Citation'); ?>
								</span>
							</label>
						</div>
						<button
							class="inner-search button"
							name="formsubmit"
							type="submit"
							value="Search References"
							style="width:100%;margin-top:1rem;"
						>
							<?= htmlspecialchars($LANG['FILTER_LIST'] ?? 'Filter List'); ?>
						</button>
					</form>
				</div>
				<?php if($isEditor) { ?>
					<div class="reference-panel" style="margin-top:1rem;">
						<a href="refdetails.php" class="sidebar-button-link">
							<button type="button" class="inner-search button">
								<?= htmlspecialchars($LANG['CREATE_NEW_REF'] ?? 'Create New Reference'); ?>
							</button>
						</a>
					</div>
				<?php }; ?>
			</aside>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
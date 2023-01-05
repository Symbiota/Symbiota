<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SchemaManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$schemaManager = new SchemaManager();
$verHistory = $schemaManager->getVersionHistory();
$currentVers = $schemaManager->getCurrentVersion();
?>
<html>
	<head>
		<title>Database Schema Manager</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<style type="text/css">
			label{ font-weight:bold }
		</style>
	</head>
	<body>
		<?php
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div id="innertext">
			<h1>Database Schema Manager</h1>
			<div style="margin:15px;">
				<label>Current version: </label>
				<?php echo $currentVers; ?>
			</div>
			<div style="margin:15px">
				<table class="styledtable" style="width:300px;">
					<tr><th>Version</th><th>Date Applied</th></tr>
					<?php
					foreach($verHistory as $ver => $date){
						echo '<tr><td>'.$ver.'</td><td>'.$date.'</td></tr>';
					}
					?>
				</table>
			</div>
			<form name="databaseMaintenanceForm" action="schemamanager.php" method="post">
				<fieldset>
					<legend>Database Schema Assistant</legend>
					<div class="form-section">
						<label>Database name:</label>
						<input name="database" type="text" value="<?php echo $database; ?>" required>
					</div>
					<div class="form-section">
						<label>Username:</label>
						<input name="database" type="text" value="<?php echo $database; ?>" required>
						<div>*Must have all DDL pivileges</div>
					</div>
					<div class="form-section">
						<label>Database name: </label>
						<input name="database" type="text" value="<?php echo $database; ?>" required>
					</div>
					<div class="form-section">
						<label>Schema: </label>
						<select name="schemaPatch">
							<option value="1.0">Base Schema 1.0</option>
							<option value="1.1">Schema Patch 1.1</option>
							<option value="1.2">Schema Patch 1.2</option>
							<option value="1.3">Schema Patch 1.3</option>
						</select>
					</div>
					<div class="form-section">
						<button>Install Patch</button>
					</div>
				</fieldset>
			</form>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>

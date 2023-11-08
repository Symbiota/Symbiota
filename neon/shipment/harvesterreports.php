<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/OccHarvesterReports.php');
header("Content-Type: text/html; charset=".$CHARSET);

$reports = new OccHarvesterReports();
$reportsArr = $reports->getHarvestReport();
$headerArr = ['sampleClass', 'errorMessage', 'count', 'shipment(s)'];
$total = $reports->getTotalSamples();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Occurrence Harvester Reports</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <style>
      .helper {
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.57;
        display: flex;
        margin: 4px 0px 24px 0px;
        border-color: #b39076;
        background-color: #f8f2ec;
        border: 1px solid #d7d9d9;
        overflow: hidden;
        border-radius: 4px;
        padding: 24px;
      }

      .jss173 {
        color: #b39076;
        margin-right: 16px;
      }

      .MuiSvgIcon-fontSizeLarge {
        font-size: 2.1875rem;
      }

      .MuiSvgIcon-root {
        fill: currentColor;
        width: 1em;
        height: 1em;
        display: inline-block;
        font-size: 1.5rem;
        transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        flex-shrink: 0;
        user-select: none;
      }

      table {
        width: 100%;
      }
      table, ul {
        font-size: small;
        text-align: left
        }

      .table-sortable {
        margin: 20px 0;
      }

      .table-sortable th {
        cursor: pointer;
        background-color: #002d74;
        color: white;
      }

      .table-sortable .th-sort-asc::after{
        content: "\25b4";
      }

      .table-sortable .th-sort-desc::after{
        content: "\25be";
      }

      .table-sortable .th-sort-asc::after, .table-sortable .th-sort-desc::after {
        margin-left: 5px;
      }

      .table-sortable .th-sort-asc, .table-sortable .th-sort-desc {
        background: #00122d;
      }


      .table-sortable thead tr th {
        padding: 10px;
      }

      td {
        color: #444444;
        padding: 1em;
        vertical-align: top;
        border-top: 2px solid #e7e7e7;
        border-bottom: 2px solid #e7e7e7;
        border-right: 0;
        border-left: 0;
      }

      tbody tr {
        max-width: 100%;
        width: 100%;
        border: none;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 0.75em;
      }

      tbody th:first-child td {
        border-top: 0;
      }

      tbody th {
        padding: 1em;
      }
    </style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
			<b>Occurrence Harvester Reports</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				?>
        <?php
        echo '<h1>Current Occurrence Harvester Errors</h1>';
        echo '<p>Total number of samples with errors: '.$total.'</p>';
        echo '<p><em>Does not include OPAL samples and samples not checked-in yet.<em></p>';
        echo '<p class="helper"> <svg class="MuiSvgIcon-root jss173 MuiSvgIcon-fontSizeLarge" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg> Click columns names to sort (click again to toggle ascending/descending)</p>';
        if(!empty($reportsArr)){
          $reportsTable = $reports->htmlTable($reportsArr, $headerArr);
          echo $reportsTable;
          };
          ?>
				<?php
			} else {
        echo '<h3>Please login to get access to this page.</h3>';
      }
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
  </body>
  <script>
    /**
     * Sorts an HTML table.
     *
     * @param {HTML TableElement} table The table to sort
     * @param {*} column The index of column to sort
     * @param {*} asc Determines sorting = ascending
     */
    function sortTableByColumn(table, column, asc = true){
      const sorting = asc ? 1 : -1;
      const tBody = table.tBodies[0];
      const rows = Array.from(tBody.querySelectorAll("tr"));

      // Sorts rows
      const sortedRows = rows.sort((a,b) => {

        const qAS = a.querySelector(`td:nth-child(${ column + 1 })`).textContent;
        const qBS = b.querySelector(`td:nth-child(${ column + 1 })`).textContent;

        let aColText = '';
        let bColText = '';

        // Deal with numbers
        if (isNaN(parseInt(qAS))){
          aColText = qAS.trim().toLowerCase();
        } else {
          aColText = parseInt(qAS);
        }


        if (isNaN(parseInt(qBS))){
          bColText = qBS.trim().toLowerCase();
        } else {
          bColText = parseInt(qBS); }

        // const aColText = qAS.trim();
        // const bColText = qBS.trim();

        return aColText > bColText ? (1 * sorting) : (-1 * sorting);
      });

      // Remove all existing rows from the table
      while (tBody.firstChild){
        tBody.removeChild(tBody.firstChild);
      }

      // Re-add sorted rows
      tBody.append(...sortedRows);

      // Remember how column is sorted
      table.querySelectorAll("th").forEach(th => th.classList.remove("th-sort-asc", "th-sort-desc"));
      table.querySelector(`th:nth-child(${ column + 1 })`).classList.toggle("th-sort-asc", asc);
      table.querySelector(`th:nth-child(${ column + 1 })`).classList.toggle("th-sort-desc", !asc);
    }
    // sortTableByColumn(document.querySelector("table"), 1, true);
    document.querySelectorAll(".table-sortable th").forEach(headerCell => {
      headerCell.addEventListener("click", () => {
        const tableElement = headerCell.parentElement.parentElement.parentElement;
        const headerIndex = Array.prototype.indexOf.call(headerCell.parentElement.children, headerCell);
        const currentIsAsc = headerCell.classList.contains("th-sort-asc");

        sortTableByColumn(tableElement, headerIndex, !currentIsAsc);
      })
    })
  </script>
</html>
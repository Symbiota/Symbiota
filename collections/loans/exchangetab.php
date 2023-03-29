<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');

$collid = $_REQUEST['collid'];

$loanManager = new OccurrenceLoans();
if($collid) $loanManager->setCollId($collid);

$transInstList = $loanManager->getTransInstList($collid);
if(!$transInstList) echo '<script type="text/javascript">displayNewExchange();</script>';
?>
<div id="exchangeToggle" style="float:right;margin:10px;">
	<a href="#" onclick="displayNewExchange()">
		<img src="../../images/add.png" alt="Create New Exchange" />
	</a>
</div>
<div id="newexchangediv" style="display:<?php echo ($transInstList?'none':'block'); ?>;width:550px;">
	<form name="newexchangegiftform" action="exchange.php" method="post" onsubmit="return verfifyExchangeAddForm(this)">
		<fieldset>
			<legend>New Gift/Exchange</legend>
			<div style="padding-top:10px;float:left;">
				<span>
					<b>Transaction Number/Identifier:</b>
					<input type="text" autocomplete="off" name="identifier" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="" />
				</span>
			</div>
			<div style="clear:left;padding-top:6px;float:left;">
				<span>
					Transaction Type:
				</span><br />
				<span>
					<select name="transactiontype" style="width:100px;" >
						<option value="Shipment" SELECTED >Shipment</option>
						<option value="Adjustment">Adjustment</option>
					</select>
				</span>
			</div>
			<div style="padding-top:6px;margin-left:20px;float:left;">
				<span>
					Entered By:
				</span><br />
				<span>
					<input type="text" autocomplete="off" name="createdby" maxlength="32" style="width:100px;" value="<?php echo $PARAMS_ARR['un']; ?>" onchange=" " />
				</span>
			</div><br />
			<div style="padding-top:6px;float:left;">
				<span>
					Institution:
				</span><br />
				<span>
					<select name="iid" style="width:400px;" >
						<option value="">Select Institution</option>
						<option value="">------------------------------------------</option>
						<?php
						$instArr = $loanManager->getInstitutionArr();
						foreach($instArr as $k => $v){
							echo '<option value="'.$k.'">'.$v.'</option>';
						}
						?>
					</select>
				</span>
				<span>
					<a href="../misc/institutioneditor.php?emode=1" target="_blank" title="Add a New Institution">
						<img src="../../images/add.png" style="width:15px;" />
					</a>
				</span>
			</div>
			<div style="clear:both;padding-top:8px;">
				<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
				<input type="hidden" name="tabindex" value="2" />
				<input name="formsubmit" type="hidden" value="createExchange" />
				<button name="submitbutton" type="submit" value="createExchange">Create  Exchange</button>
			</div>
		</fieldset>
	</form>
</div>
<div style="margin-top:10px;">
	<?php
	if($transInstList){
		?>
		<h3>Transaction Records by Institution</h3>
		<ul>
			<?php
			foreach($transInstList as $k => $transArr){
				?>
				<li>
					<a href="#" onclick="toggle('<?php echo $k; ?>');"><?php echo ($transArr['institutioncode'] ? $transArr['institutioncode'] : ($transArr['institutionname'] ? $transArr['institutionname'] : '[no name]')); ?></a>
					<?php
					$bal = $transArr['invoicebalance'];
					echo '(Balance: '.($bal?($bal < 0?'<span style="color:red;font-weight:bold;">'.$bal.'</span>':$bal):0).')';
					?>
					<div id="<?php echo $k; ?>" style="display:none;">
						<ul>
							<?php
							$transList = $loanManager->getTransactions($collid,$k);
							foreach($transList as $t => $transArr){
								echo '<li>';
								echo '<a href="exchange.php?collid='.$collid.'&exchangeid='.$t.'">#'.$transArr['identifier'].' <img src="../../images/edit.png" style="width:12px" /></a>: ';
								if($transArr['transactiontype'] == 'Shipment'){
									if($transArr['in_out'] == 'Out'){
										echo 'Outgoing exchange; Sent ';
										echo $transArr['datesent'].'; Including: ';
									}
									else{
										echo 'Incoming exchange, received ';
										echo $transArr['datereceived'].', including: ';
									}
									echo ($transArr['totalexmounted']?$transArr['totalexmounted'].' mounted, ':'');
									echo ($transArr['totalexunmounted']?$transArr['totalexunmounted'].' unmounted, ':'');
									echo ($transArr['totalgift']?$transArr['totalgift'].' gift, ':'');
									echo ($transArr['totalgiftdet']?$transArr['totalgiftdet'].' gift-for-det, ':'');
									echo 'Balance: '.$transArr['invoicebalance'];
								}
								else{
									echo 'Adjustment of '.$transArr['adjustment'].' specimens';
								}
								echo '</li>';
							}
							?>
						</ul>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	else{
		echo '<div style="font-weight:bold;font-size:120%;margin-top:10px;">There are no transactions registered for this collection</div>';
	}
	?>
</div>
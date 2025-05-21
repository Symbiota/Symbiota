<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ChecklistVoucherAdmin.php');


$clid = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;

$voucherManager = new ChecklistVoucherAdmin();
$voucherManager->setClid($clid);

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $DEFAULT_TITLE . ': External Voucher Linking' ?></title>
		<?php include_once($SERVER_ROOT.'/includes/head.php'); ?>
		<script type="text/javascript">
			const iNaturalistApi = 'https://api.inaturalist.org/v1'
			const params = new URL(window.location.href).searchParams;

			async function runWithLoading(asyncCallback) {
				const external_vouchers_container = document.getElementById('external_vouchers_container')
				const voucher_loader = document.getElementById('voucher_loader')
				const voucher_error = document.getElementById('voucher_error')
				const voucher_submit_button = document.getElementById('voucher_submit_button')

				external_vouchers_container.style.display = 'none';
				voucher_error.style.display = 'none';
				voucher_error.textContent =	"";
				voucher_loader.style.display = 'block';
				if(voucher_submit_button) voucher_submit_button.disabled="true";

				try {
					await asyncCallback();

					voucher_loader.style.display = 'none';
					external_vouchers_container.style.display = '';
				} catch(e) {
					voucher_loader.style.display = 'none';
					voucher_error.style.display = '';
					voucher_error.textContent = e;	
				} finally {
					if(voucher_submit_button) voucher_submit_button.disabled = null;
				}
			}

			async function fetchObservations(taxon_name, external_id, page=1) {
				const url = `${iNaturalistApi}/observations?project_id=${external_id}&taxon_name=${taxon_name}&page=1`

				let response = await fetch(url, {
					method: "GET",
					mode: "cors",
				});

				let vouchers = await response.json();

				const template = document.getElementById('external_voucher_template')

				for(let voucher of vouchers.results) {
					let voucher_clone = template.content.cloneNode(true);
					voucher_clone.querySelector('.taxon_name').textContent = voucher.taxon.name;
					voucher_clone.querySelector('.locality').textContent = voucher.place_guess;
					voucher_clone.querySelector('.coordinates').textContent = voucher.location;
					voucher_clone.querySelector('.voucher_container').id = voucher.id;
					voucher_clone.querySelector('.link_checkbox').value = voucher.id;

					voucher_clone.querySelector('.external_source').href= voucher.uri;
					//voucher_clone.querySelector('.voucher_link_button').onclick = () => console.log('link ' + voucher.id);

					if(voucher.photos && voucher.photos.length) {
						//voucher_clone.querySelector('.display_img').src = voucher.photos[0].url;
					}

					external_vouchers.appendChild(voucher_clone);
				}
			}

			function create_voucher_html() {
			}

			function initExternalVouchers() {
				const taxon_name = params.get('taxon_name');
				const checklist_id = params.get('clid');
				const external_id = params.get('external_id');

				runWithLoading(async () => {
					if(!checklist_id) throw Error('A checklist id is required for this tool to function');

					await fetchObservations(taxon_name, external_id) 
				});
			}

			function external_vouchers_sumbit(e) {
				e.preventDefault();
				e.stopPropagation();

				const tid = params.get('target_tid');
				const clid = params.get('clid');

				const form_data = new FormData(e.target);
				console.log(form_data)
			}
		</script>
	</head>
	<body onload="initExternalVouchers()">	
		<div id="innertext" style="height:100vh; position:relative">
			<template id="external_voucher_template_old">
				<div class="voucher_container" style="display:flex; gap: 1rem; align-items:center">
					<div>
						<img class="display_img"  height="75px" width="75px"/>
					</div>
					<div>
						<h3 class="taxon_name" style="margin: 0"></h3>
						<div class="coordinates"></div>
						<div class="locality"></div>
					</div>

					<div style="flex-grow: 1; display:flex; justify-content:end">
						<div>
							<button class="button voucher_link_button">Link Voucher</button>
						</div>
					</div>
				</div>
			</template>

			<template id="external_voucher_template">
				<tr class="voucher_container">
					<td>
						<input class="link_checkbox" type="checkbox" name="link" value=""/>
					</td>
					<td class="taxon_name"></td>
					<td class="coordinates"></td>
					<td class="locality"></td>
					<td><a class="external_source" href="" target="_blank">Source Link</a></td>
				</tr>
			</template>

			<div id="external_vouchers_container">
			<?php if(!empty($clid)): ?>
				<form id="external_voucher_form" onsubmit="external_vouchers_sumbit(event)">
					<h1>External Voucher Linking - iNaturalist</h1>
					<table class="styledtable">
						<thead>
							<th><input type="checkbox" name="link-all"></th>
							<th>Taxon Name</th>
							<th>Coordinates</th>
							<th>Locality</th>
							<th>Source</th>
						</thead>
						<tbody id="external_vouchers">
						</tbody>
					</table>

					<button id="voucher_submit_button" class="button" style="margin-top:1rem">Submit</button>
				</form>
			<?php else: ?>
				You must provide a checklist id
			<?php endif ?>
			</div>

			<div id="voucher_loader" style="position:absolute; top:50%; width:100%; text-align:center">
				...Loading External Vouchers
			</div>

			<div id="voucher_error" style="display:none;position:absolute; top:50%; width:100%; text-align:center">
				...Loading External Vouchers
			</div>
		</div>
	</body>
</html>

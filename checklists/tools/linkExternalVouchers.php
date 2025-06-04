<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ChecklistVoucherAdmin.php');
include_once($SERVER_ROOT . '/classes/ChecklistManager.php');
include_once($SERVER_ROOT . "/classes/Sanitize.php");

$clid = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$target_tid = array_key_exists('target_tid', $_REQUEST) ? filter_var($_REQUEST['target_tid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$external_id = array_key_exists('external_id', $_REQUEST) ? filter_var($_REQUEST['external_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxon_name = array_key_exists('taxon_name', $_REQUEST) ? htmlspecialchars($_REQUEST['taxon_name']): '';
$external_service = array_key_exists('external_service', $_REQUEST) ? htmlspecialchars($_REQUEST['external_service']): '';

if(!($IS_ADMIN || (array_key_exists('ClAdmin',$USER_RIGHTS) && in_array($clid, $USER_RIGHTS['ClAdmin']))) ){
	header('location: ' . $CLIENT_ROOT . '/index.php');
	return;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$voucherManager = new ChecklistVoucherAdmin();
	$voucherManager->setClid($clid);
	
	if($_POST['external_voucher_link_json_data'] ?? false) {
		$voucher_json_data = json_decode($_POST['external_voucher_link_json_data'], true) ?? [];
		if($voucher_json_data) {
			$clean_data = Sanitize::in($voucher_json_data);
			$status = $voucherManager->addExternalVouchers($target_tid, $clean_data);
		}
	}
}


$clManager = new ChecklistManager();
$clManager->setClid($clid);
$clArray = $clManager->getClMetaData();

$linked_external_vouchers = [];
if($clManager->getAssociatedExternalService()) {
	$linked_external_vouchers = $clManager->getExternalVoucherArr($target_tid);
}

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
			let app_state = {
				vouchers: [],
			};

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

			async function fetchObservations(taxon_name, external_id, linked_external_vouchers = [], page=1) {
				const searchParams = new URLSearchParams();
				searchParams.set('project_id', external_id);
				searchParams.set('taxon_name', taxon_name);
				searchParams.set('page', page);

				for(let external_voucher_id of linked_external_vouchers) {
					searchParams.append('not_id', external_voucher_id);
				}

				const url = `${iNaturalistApi}/observations?${searchParams}`;

				let response = await fetch(url, {
					method: "GET",
					mode: "cors",
				});

				let vouchers = await response.json();

				//Save state for other operations
				app_state.vouchers = vouchers.results;
				app_state.vouchers_cnt = vouchers.total_results;
				app_state.voucher_page = vouchers.total_results;
				app_state.voucher_per_page = vouchers.per_page;

				const template = document.getElementById('external_voucher_template')

				for(let voucher of vouchers.results) {
					let voucher_clone = template.content.cloneNode(true);
					voucher_clone.querySelector('.taxon_name').textContent = voucher.taxon.name;
					voucher_clone.querySelector('.locality').textContent = voucher.place_guess;
					voucher_clone.querySelector('.date_observed').textContent = voucher.observed_on;
					voucher_clone.querySelector('.observer').textContent = voucher.user.name? voucher.user.name: voucher.user.login;
					voucher_clone.querySelector('.voucher_container').id = voucher.id;
					voucher_clone.querySelector('.external_id').textContent = voucher.id;
					voucher_clone.querySelector('.link_checkbox').value = voucher.id;
					voucher_clone.querySelector('.external_source').href= voucher.uri;
					external_vouchers.appendChild(voucher_clone);
				}
			}

			function initExternalVouchers() {
				const taxon_name = params.get('taxon_name');
				const checklist_id = params.get('clid');
				const target_tid = params.get('target_tid');
				const external_id = params.get('external_id');

				const data_store = document.getElementById('data-store');
				let linked_external_vouchers = [];

				try {
					let voucher_json = JSON.parse(data_store.getAttribute('data-linked_external_vouchers'));
					if(voucher_json[target_tid]) {
						for(let clCoordID in voucher_json[target_tid]) {
							const voucher = voucher_json[target_tid][clCoordID];
							linked_external_vouchers.push(voucher.id);
						}
					}
				} catch(err) {
					console.error(err);
				}

				runWithLoading(async () => {
					if(!checklist_id) throw Error('A checklist id is required for this tool to function');
					if(!target_tid) throw Error('A target taxon id is required for this tool to function');

					await fetchObservations(taxon_name, external_id, linked_external_vouchers); 

				}).then( () => {
					if(app_state.vouchers.length <= 0) {
						const external_vouchers_container = document.getElementById('external_vouchers_container')
						const all_vouchers_linked = document.getElementById('all_vouchers_linked')
						external_vouchers_container.style.display = 'none';
						all_vouchers_linked.style.display = null;
					}
				});
			}

			async function external_vouchers_sumbit(e) {
				const target_tid = params.get('target_tid');
				const clid = params.get('clid');
				const form_data = new FormData(e.target);
				const new_links = form_data.getAll('external_voucher_link[]')

				let json_data = [];

				for(let voucher of app_state.vouchers) {
					if(new_links.includes(`${voucher.id}`)) {
						let new_voucher = {
							// Casting as string to keep consistent with old storage type
							id: `${voucher.id}`,
							repository: 'iNat',
							date: voucher.observed_on,
							lat: 0,
							lng: 0,
						}

						if(voucher.user) {
							new_voucher.user = voucher.user.name? voucher.user.name: voucher.user.login
						} else {
							new_voucher.user = 'Unknown'
						}

						if(voucher.location) {
							const location_parts = voucher.location.split(',');

							if(location_parts.length === 2) {
								new_voucher.lat = location_parts[0];
								new_voucher.lng = location_parts[1];
							}
						}

						json_data.push(new_voucher)
					}
				}

				const checkboxes = e.target.querySelectorAll('input.link_checkbox').forEach(c => c.checked = null);
				e.target.querySelector('input[name="external_voucher_link_json_data"]').value = JSON.stringify(json_data);
			}

			function toggleChecked(toggle = false) {
				const checkboxes = document.querySelectorAll('input.link_checkbox');
				for(let checkbox of checkboxes) {
					checkbox.checked = toggle? true: null;
				}
			}

			function uncheck_all_if_needed(e) {
				const checked = e.target.checked;

				if(!checked) {
					document.getElementById('link-all').checked = null;
				}
			}
		</script>
	</head>
	<body onload="initExternalVouchers()">	
		<div id="data-store" data-linked_external_vouchers="<?= htmlspecialchars(json_encode($linked_external_vouchers))?>"></div>
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
						<input onchange="uncheck_all_if_needed(event)" class="link_checkbox" type="checkbox" name="external_voucher_link[]" value=""/>
					</td>
					<td class="taxon_name"></td>
					<td class="locality"></td>
					<td class="observer"></td>
					<td class="date_observed"></td>
					<td class="external_id"></td>
					<td><a class="external_source" href="" target="_blank">Source Link</a></td>
				</tr>
			</template>

			<div id="external_vouchers_container" style="display:none">
			<?php if(!empty($clid)): ?>
				<form method="POST" id="external_voucher_form" onsubmit="external_vouchers_sumbit(event)">
					<h1>External Voucher Linking - iNaturalist</h1>

					<input type="hidden" name="clid" value="<?= htmlspecialchars($clid) ?>">
					<input type="hidden" name="target_tid" value="<?= htmlspecialchars($target_tid) ?>">
					<input type="hidden" name="external_id" value="<?= htmlspecialchars($external_id) ?>">
					<input type="hidden" name="taxon_name" value="<?= $taxon_name ?>">
					<input type="hidden" name="external_service" value="<?= $external_service ?>">
					<input type="hidden" name="external_voucher_link_json_data" value="">

					<div style="margin-bottom:1rem"><b>Vouchers for: </b><?= $taxon_name ?></div>
					<table class="styledtable">
						<thead>
							<th><input id="link-all" onchange="toggleChecked(this.checked)" type="checkbox" name="link-all"></th>
							<th>Taxon Name</th>
							<th>Locality</th>
							<th>Observer</th>
							<th>Date Observed</th>
							<th>External ID</th>
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

			<div id="voucher_error" style="display:none;position:absolute; top:50%; width:100%; text-align:center"></div>
			<div id="all_vouchers_linked" style="display:none;position:absolute; top:50%; width:100%; text-align:center">
				All vouchers associated 
				<i><?= $taxon_name ?></i> in
				<a href="https://www.inaturalist.org/projects/<?= $external_id ?>" target="_blank">iNaturalist Project</a>
				have already been linked
			</div>
		</div>
	</body>
</html>

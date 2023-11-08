<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html>

<head>
	<title>NEON Biorepository Staff</title>
	<?php
	$activateJQuery = false;
	if (file_exists($SERVER_ROOT . '/includes/head.php')) {
		include_once($SERVER_ROOT . '/includes/head.php');
	} else {
		echo '<link href="' . $CLIENT_ROOT . '/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<style>
		h1 {
			margin-bottom: 3rem;
		}

		#staff-gallery {
			width: 90%;
			margin: 0 auto;
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			grid-gap: 1rem;
			justify-items: center;
		}

		/* Large screens */

		@media (min-width: 375px) {
			#staff-gallery {
				width: 100%;
			}
		}

		#staff-gallery img {
			max-width: 100%;
			height: auto;
			display: block;
			border-radius: 6px 6px 0 0;
		}

		.card {
			max-width: 240px;
		}

		.card-text {
			border: 1px solid #D7D9D9;
			border-top: none;
			box-sizing: border-box;
			border-radius: 0 0 6px 6px;
			padding: 0.75rem 0.5rem;
		}

		.card h4,
		.card h5 {
			margin: 0.5rem 0;
			line-height: 1.2;
		}

		.card p {
			font-size: 0.7rem;
			line-height: 1.4;
		}

		.card a {
			color: #0073CF !important;
		}

		.callout {
			display: inline-flex;
			flex-wrap: wrap;
			gap: 6px;
		}

		.callout-link {
			border: 1px solid #0073CF;
			border-radius: 2px;
			font-weight: bold;
			font-size: 0.6rem;
			line-height: 24px;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			text-decoration: none;
			color: #0073CF;
			margin: 0.25rem 0;
			padding: 0.25rem 0.75rem 0.25rem 0.75rem;
		}

		.callout-link:hover {
			border: 1px solid #0092E2;
			color: #0092E2;
			transition: all 0.25s;
			text-decoration: underline;
		}
	</style>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> >>
		<b>NEON Biorepository Staff</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1 style="text-align: center;">NEON Biorepository Staff</h1>
		<div id="staff-gallery"></div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
<script>
	// Fetch data from Google Spreadsheet via Google Sheets API + opensheet
	const sheetId = '1OzXLkkLe6SpXeLdjih1GihSoHnwe6EzE3d4Lee96Kko';
	const url = `https://opensheet.elk.sh/${sheetId}/Sheet1`;
	const gallery = document.getElementById('staff-gallery');

	function addStaff(info) {
		const person = document.createElement('article');
		person.className = 'card';
		const img = document.createElement('img');
		const text = document.createElement('div');
		text.className = 'card-text';
		const name = document.createElement('h4');
		name.innerText = info.Name;
		const position = document.createElement('h5');
		position.innerText = info.Position;
		const role = document.createElement('p');
		role.innerText = info.Role;
		const interests = document.createElement('p');
		interests.innerText = info.Interests;
		const callout = document.createElement('div');
		callout.className = 'callout';
		const email = document.createElement('a');
		email.href = `mailto:${info.Email}`;
		email.innerText = 'Contact';
		email.className = 'callout-link';
		const iSearch = document.createElement('a');
		iSearch.href = info.iSearch;
		iSearch.innerText = 'iSearch';
		iSearch.className = 'callout-link';
		// PhotoPublic should be a public image url
		// PhotoDrive is the url of a file in the Google Drive
		if (info.PhotoPublic) {
			img.src = info.PhotoPublic;
		} else if (info.PhotoDrive) {
			let photoDrive = info.PhotoDrive.split('/');
			console.log(photoDrive);
			img.src = `https://drive.google.com/uc?export=view&id=${photoDrive[5]}`;
		};
		img.width = '280';
		img.alt = info.Name;
		callout.append(email, iSearch);
		text.append(name, position, role, interests, callout);
		person.append(img, text);
		gallery.appendChild(person);
	}

	async function fetchStaff() {
		const response = await fetch(url);
		const data = await response.json();
		data.forEach(addStaff);
	}

	fetchStaff();
</script>

</html>
<?php
include_once('../config/symbini.php');
include_once ($SERVER_ROOT.'/classes/UtilityFunctions.php');
header("Content-Type: text/html; charset=" . $CHARSET);
$serverHost = UtilityFunctions::getDomain();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['DATA_USAGE_GUIDELINES']?></title>
	<?php

	include_once($SERVER_ROOT . '/includes/head.php');
	?>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '/index.php">' . $LANG['HOME'] . '</a> &gt;&gt' ?>;
		<b><?= $LANG['DATA_USAGE_GUIDELINES'] ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<?php
			if($LANG_TAG=='en'){
		?>
		<h1 class="page-heading">Guidelines for Acceptable Use of Data</h1>
		<h2>Recommended Citation Formats</h2>
		<p>Use one of the following formats to cite data retrieved from the <?php echo $DEFAULT_TITLE; ?> network:</p>
		<h3>General Citation</h3>
		<blockquote>
			<?php
			if (file_exists($SERVER_ROOT . '/includes/citationportal.php')) {
				include($SERVER_ROOT . '/includes/citationportal.php');
			}
			else {
				echo 'Biodiversity occurrence data published by: ';
				if ($DEFAULT_TITLE) {
					echo $DEFAULT_TITLE;
				}
				else {
					echo 'Name of people or institutional reponsible for maintaining the portal';
				};
				echo ' (accessed through the ';
				if ($DEFAULT_TITLE) {
					echo $DEFAULT_TITLE;
				}
				else {
					echo 'Name of people or institutional reponsible for maintaining the portal';
				};
				echo ' Portal, ' . $serverHost . $CLIENT_ROOT . ', ' . date('Y-m-d') . ').';
			};
			?>
		</blockquote>

		<h3>Usage of occurrence data from specific institutions</h3>
		<p>Access each collection profile page to find the available citation formats.</p>
		<h4>Example</h4>
		<blockquote>
			<?php
			$collData['collectionname'] = 'Name of Institution or Collection';
			$collData['dwcaurl'] = $serverHost . $CLIENT_ROOT . '/portal/content/dwca/NIC_DwC-A.zip';
			if (file_exists($SERVER_ROOT . '/includes/citationcollection.php')) {
				include($SERVER_ROOT . '/includes/citationcollection.php');
			} else {
				echo 'Name of Institution or Collection. Occurrence dataset ' . 'http://gh.local/Symbiota/portal/content/dwca/' . 'accessed via the' . 'Fresh Symbiota Install' . 'Portal, ' . 'http://gh.local/Symbiota' . ', 2022-07-25.';
			}
			?>
		</blockquote>

		<h2>Occurrence Record Use Policy</h2>
		<div>
			<ul>
				<li>
					While <?php echo $DEFAULT_TITLE; ?> will make every effort possible to control and document the quality
					of the data it publishes, the data are made available "as is". Any report of errors in the data should be
					directed to the appropriate curators and/or collections managers.
				</li>
				<li>
					<?php echo $DEFAULT_TITLE; ?> cannot assume responsibility for damages resulting from misuse or
					misinterpretation of datasets or from errors or omissions that may exist in the data.
				</li>
				<li>
					It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
					has resulted in data used in subsequent research. We encourages users to
					contact the original investigator responsible for the data that they are accessing.
				</li>
				<li>
					<?php echo $DEFAULT_TITLE; ?> asks that users not redistribute data obtained from this site without permission for data owners.
					However, links or references to this site may be freely posted.
				</li>
			</ul>
		</div>

		<h2>Images</h2>
		<p>Images within this website have been generously contributed by their owners to promote education and research. These contributors retain the full copyright for their images.
		Unless stated otherwise, images are made available under the Creative Commons Attribution-ShareAlike
		(<a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA</a>).
		Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution regarding the source of the content is made. If the content is altered, transformed,
		or enhanced, it may be re-distributed only under the same or similar license by which it was acquired.
		</p>

		<h2>Notes on Specimen Records and Images</h2>
		<p>Specimens are used for scientific research and because of skilled preparation and careful use they may last for hundreds of years. Some collections have specimens that were
		collected over 100 years ago that are no longer occur within the area. By making these specimens available on the web as images, their availability and value improves without
		an increase in inadvertent damage caused by use. Note that if you are considering making specimens, remember collecting normally requires permission of the landowner and,
		in the case of rare and endangered plants, additional permits may be required. It is best to coordinate such efforts with a regional institution that manages a publicly
		accessible collection.
		</p>

		<p><b>Disclaimer:</b> This data portal may contain specimens and historical records that are culturally sensitive. The collections include specimens dating back over 200 years
		collected from all around the world. Some records may also include offensive language. These records do not reflect the portal community's current viewpoint but rather the
		social attitudes and circumstances of the time period when specimens were collected or cataloged.
		</p>
	</div>
	<?php
		}
		else{
	?>
			<div id="innertext">
			<h1>Lineamientos para el Uso Aceptable de los Datos</h1><br />

			<h2>Formatos Recomendados de Citas</h2>
			<div style="margin:10px">
				Use uno de los siguientes formatos para citar los datos obtenidos del Portal de <?php echo $DEFAULT_TITLE; ?> :
				<div style="font-weight:bold;margin-top:10px;">
					Cita General:
				</div>
				<div style="margin:10px;">
					<?php
					$basePath = "http://";
					if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $basePath = "https://";
					$basePath .= $_SERVER["SERVER_NAME"];
					if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) $basePath .= ':'.$_SERVER["SERVER_PORT"];
					$basePath .= $CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/');
					echo $DEFAULT_TITLE.'. '.date('Y').'. ';
					echo $basePath.'index.php. ';
					echo 'Accesado en '.date('F d').'. ';
					?>
				</div>

				<div style="font-weight:bold;margin-top:10px;">
					Uso de datos de ocurrencia de instituciones específicas:
				</div>
				<div style="margin:10px;">
					Biodiversity occurrence data published by: &lt;List of Collections&gt;
					(Accesado por medio del Portal de <?php echo $DEFAULT_TITLE; ?> ,
					<?php echo $basePath.'index.php'; ?>, YYYY-MM-DD)<br/><br/>
					<b>Por ejemplo:</b><br/>
					Datos de ocurrencia de biodiversidad publicados por:
					Universidad del Valle de Guatemala, Escuela de Biología USAC, y Centro de Estudios Conservacionistas USAC
					(Accesado por medio del Portal de <?php echo $DEFAULT_TITLE; ?> ,
					<?php echo $basePath.'index.php, '.date('Y-m-d').')'; ?>
				</div>
			</div>
			<div>
			</div>

			<a name="occurrences"></a>
			<h2>Política de uso de Registros de Ocurrencia</h2>
		    <div style="margin:10px;">
				<ul>
					<li>
						Mientras que el Portal de <?php echo $DEFAULT_TITLE; ?> hará el mayor esfuerzo posible para controlar y documentar la calidad
						de los datos que se publican, los datos son compartidos "como están". Cualquier reporte de errores en los datos debería ser
						dirigido a los curadores o encargados de colecciones apropiados.
					</li>
					<li>
						<?php echo $DEFAULT_TITLE; ?> no puede asumir la responsabilidad por daños resultando por el mal uso o 
						mala interpretación de los conjuntos de datos, o de errores u omisiones que puedan existir en los datos.
					</li>
					<li>
						Se considera cuestión de ética profesional citar y reconocer el trabajo de otros científicos que
						ha resultado en el uso de los datos en investigaciones posteriores. Motivamos a los usuarios a 
						contactar a los investigadores originales responsables por los datos que están accesando.
					</li>
					<li>
						<?php echo $DEFAULT_TITLE; ?> solicita a los usuarios a no redistribuir los datos obtenidos desde este sitio sin el permiso de los propietarios.
						Sin embargo, enlaces o referencias a este sitio pueden ser publicadas con libertad.
					</li>
				</ul>
		    </div>

			<a name="images"></a>
			<h2>Images</h2>
		    <div style="margin:15px;">
		    	Las imágenes en este sitio web han sido generosamente compartidas por sus autores para
		    	promover la educación e investigación. Estos contribuyentes retienen los derechos de autor de
		    	sus imágenes. A menos que se indique lo contrario, las imágenes están disponibles bajo la licencia de Creative Commons
		    	Attribution-ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC BY-SA</a>), es decir, compartir con atribución.
				Los usuarios pueden copiar, transmitir, reusar o adaptar contenido, mientras una atribución
				indicando la fuente sea realizada. Si el contenido es alterado, transformado o mejorado, 
				puede ser distribuido únicamente bajo licencias iguales o similares a las originales.
		    </div>

			<h2>Notas acerca de Registros de Especímenes e Imágenes</h2>
		    <div style="margin:15px;">
				Los especímenes son utilizados para investigaciones científicas gracias a que las forma de preparación especializada
				permite su uso por cientos de años. Algunas colecciones poseen especímenes
				que fueron colectados hace más de 100 años y que ya no ocurren en el área registrada.
				Al hacer estos especímenes disponibles en la web como imágenes, su disponibilidad y
				valor aumenta sin incrementar los posibles daños causados por su uso. Notar que si 
				considera obtener especímenes, normalmente se requieren permisos de colecta u otros 
				permisos si se trata de especies amenazadas. Siempre es mejor coordinar los esfuerzos de
				colecta con instituciones científicas locales que cuentan con colecciones accesibles al público.
			</div>
		</div>
	<?php
		}
	?>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>
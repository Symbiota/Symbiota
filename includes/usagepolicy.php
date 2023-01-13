<?php
//error_reporting(E_ALL);
 include_once('../config/symbini.php');
 header("Content-Type: text/html; charset=".$CHARSET);

?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Data Usage Guidelines</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
      }
  ?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Data Usage Guidelines</b>
		</div>
		<!-- This is inner text! -->
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
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>

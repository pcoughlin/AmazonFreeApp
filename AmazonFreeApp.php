<?php

require_once('simple_html_dom.php');

define("BASE_URL_AMAZON", 'http://www.amazon.es/');
define("URL_TIENDA_AMAZON", 'http://www.amazon.es/mobile-apps/b/ref=nav_shopall_adr_app?ie=UTF8&node=1661649031');


$html = file_get_html(URL_TIENDA_AMAZON);

foreach ($html->find('h3[class=fad-widget-app-name]') as $enlace) {
	$EnlaceAppGratuita = $enlace->innertext;

	foreach ($enlace->find('a') as $enlac) {
		$URLAppGratuita = BASE_URL_AMAZON.$enlac->href;
		$NombreAppGratuita = $enlac->innertext;
	}
}

$html->clear();

if (isset($URLAppGratuita)) {

	$html = file_get_html($URLAppGratuita);

	$URLImagenAppGratuita = $html->getElementById("main-image")->src;

	$PrecioAplicacion = $html->getElementById("listPriceValue")->innertext;

	foreach ($html->find('div[class=bucket]') as $bloque)
		foreach ($bloque->find('div[class=content]') as $descripcion)
			$DescripcionAplicacion = trim($descripcion->innertext)."\n";

	$BloqueMiniaturas = $html->getElementById("thumbs-image");

	$URLMiniaturas = array();

	foreach ($BloqueMiniaturas->find('a') as $enlaceMiniaturas) {
		foreach ($enlaceMiniaturas->find('img') as $miniatura)
			$URLMiniaturas[] = $miniatura->src;
		break;
	}

	$html->clear();

	if (isset($DescripcionAplicacion)) {

		$CuerpoEmail = '
		<html>
		<head>
		</head>
		<body>
			<table border="0" width="550">
				<tr>
					<td width="200">
						<a href="'.$URLAppGratuita.'" style="border:0px;">
							<img src="'.$URLImagenAppGratuita.'" width="199" />
						</a>
					</td>
					<td width="*" valign="middle" align="left"><b>'
						.$NombreAppGratuita.'</b><br />Precio habitual: '.$PrecioAplicacion
					.'</td>
				</tr>';

			if (count($URLMiniaturas) > 0) {

				$CuerpoEmail .= '<tr><td colspan="2">';
				$PasaPrimera = false;
				foreach ($URLMiniaturas as $URLMiniatura)
					if ($PasaPrimera == true)
						//$CuerpoEmail .= '<img src="'.str_replace("._SS30_", "", $URLMiniatura).'" width="190" /> ';
						$CuerpoEmail .= '<img src="https://images1-focus-opensocial.googleusercontent'
.'.com/gadgets/proxy?container=focus&resize_w=190&refresh=86400&url='.str_replace("._SS30_", "", $URLMiniatura).'" /> ';
					else
						$PasaPrimera = true;
				$CuerpoEmail .= '</td></tr>';
			}

			$CuerpoEmail .= '
				<tr>
					<td colspan="2">'.$DescripcionAplicacion.'</td>
				</tr>
			</table>
		</body>
		</html>';

	} else
		$CuerpoEmail = 'No se ha podido procesar la página de la app gratuita.';

} else
	$CuerpoEmail = 'No se ha podido encontrar la URL de la app gratuita.';


mail("correo@electronico.com", "Aplicación gratuita de Amazon ".date('j/m'), $CuerpoEmail,
	'MIME-Version: 1.0'."\r\n"
	.'Content-type: text/html; charset=iso-8859-1'."\r\n"
	.'From: DreamHost <correo@electronico.com>'."\r\n");

?>

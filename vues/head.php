<?php
// Projet TraceGPS - version web mobile
// fichier : vues/head.php
// Rôle : fournit les données du bloc <head> de toutes les vues
// Dernière mise à jour : 19/12/2017 par JM CARTRON

// Ce fichier inclut les données du bloc <head> :
//  - jeu de caractères
//  - balises <meta>
//  - l'icone de l'application
//  - la feuille de styles et le framework JQuery Mobile (en fonction de la variable $version réglée dans index.php

?>

		<title>TraceGPS</title> 
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

		<!-- favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico" />
		<link rel="shortcut icon" type="image/png" href="./images/favicon.png">
		<link rel="icon" type="image/x-icon" href="./images/favicon.ico" />
		<link rel="icon" type="image/png" href="./images/favicon.png" />
		<link rel="apple-touch-icon" href="./images/apple-touch-icon.png" />

<?php
if ($version == "1.2.0" ) { ?>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css">
		<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
<?php };

if ($version == "1.2.1" ) { ?>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css" />
		<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
<?php };

if ($version == "1.3.2" ) { ?>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
<?php };

if ($version == "1.4.5" ) { ?>
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<?php }; ?>
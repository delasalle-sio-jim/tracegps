<?php
// Projet TraceGPS - version web mobile
// fichier : controleurs/CtrlTelechargerApk.php
// Rôle : traiter le téléchargement de l'application Android
// Dernière mise à jour : 2/4/2018 par JM CARTRON

// Nom du fichier à télécharger : "tracegps.apk"
// Dossier contenant ce fichier : "downloads"

// désactive le temps max d'exécution
//set_time_limit(0);

$name = "tracegps.apk";
$filename = "../downloads/tracegps.apk";
$size = filesize($filename);

// désactivation compression GZip
// if (ini_get("zlib.output_compression")) {
// 	ini_set("zlib.output_compression", "Off");
// }

// fermeture de la session
//session_write_close();

/*
// source de cette version 1 : http://programmation-web.net/2012/04/comment-forcer-le-telechargement-dun-fichier-en-php/
// désactive la mise en cache
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0");
header("Cache-Control: max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
// prépare le téléchargement
header("Content-Type: application/force-download");
header('Content-Disposition: attachment; filename="' . $name . '"');
header("Content-Length: " . $size);		// indique la taille du fichier à télécharger
*/

// source de cette version 2 : http://www.apprendre-php.com/tutoriels/tutoriel-25-forcer-le-telechargement-d-un-fichier.html
// désactive la mise en cache
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Content-Tranfer-Encoding: none');
header('Expires: 0');
// prépare le téléchargement
header('Content-Type: application/octet-stream');
header('Content-disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . $size);		// indique la taille du fichier à télécharger

// envoi le contenu du fichier
readfile($filename);

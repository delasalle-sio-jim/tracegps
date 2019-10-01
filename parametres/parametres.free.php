<?php
// Projet TraceGPS - version web mobile
// fichier : modele/parametres.free.php
// Rôle : inclure les paramètres de l'application (hébergement chez Free)
// Dernière mise à jour : 24/4/2018 par JM CARTRON

global $PARAM_HOTE, $PARAM_PORT, $PARAM_BDD, $PARAM_USER, $PARAM_PWD;

// paramètres de connexion chez Free -------------------------------------------------------------------------
$PARAM_HOTE = "jean.michel.cartron.sql.free.fr";	// le serveur de la bdd
$PARAM_PORT = "3306";								// le port utilisé par le serveur MySql
$PARAM_BDD = "jean_michel_cartron";					// nom de la base de données
$PARAM_USER = "jean.michel.cartron";				// nom de l'utilisateur
$PARAM_PWD = "lilas402";							// son mot de passe

// Autres paramètres -----------------------------------------------------------------------------------------
global $TITRE_APPLI, $NOM_APPLI, $CLE_API, $FREQUENCE_AFFICHAGE, $ADR_MAIL_EMETTEUR, $ADR_SERVICE_WEB;

// titre de l'application (en entête des vues)
$TITRE_APPLI = "TraceGPS";

// nom de l'application (en pied de page des vues)
$NOM_APPLI = "Suivi de parcours sportifs en extérieur";

// clé API pour Google Maps
$CLE_API = "AIzaSyCdcnXm0lYxmuWIWYPDO9jIpZSZgCvGzRw";

// valeur de la fréquence de réactualisation de l'affichage (en secondes) d'un parcours
$FREQUENCE_AFFICHAGE = 60;			// 60 sec ou 1 mn

// adresse de l'émetteur lors d'un envoi de courriel
$ADR_MAIL_EMETTEUR = "delasalle.sio.crib@gmail.com";

// adresse du service web en localhost -----------------------------------------------------------------------
//$ADR_SERVICE_WEB = "http://localhost/ws-php-cartron/tracegps/services/";
// adresse du service web chez Free --------------------------------------------------------------------------
$ADR_SERVICE_WEB = "http://jean.michel.cartron.free.fr/traceGPS/services/";

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
<?php
// Projet TraceGPS - services web
// fichier : api/services/GetUnParcoursEtSesPoints.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service permet à un utilisateur d'obtenir le détail d'un de ses parcours ou d'un parcours d'un membre qui l'autorise
// Le service web doit recevoir 4 paramètres :
//     pseudo : le pseudo de l'utilisateur qui demande à consulter
//     mdp : le mot de passe hashé en sha1 de l'utilisateur qui demande à consulter
//     idTrace : l'id de la trace à consulter
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution ainsi que la synthèse et la liste des points du parcours

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/GetUnParcoursEtSesPoints?pseudo=europa&mdp=13e3668bbee30b004380052b086457b014504b3e&idTrace=2&lang=xml

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();
	
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$idTrace = ( empty($this->request['idTrace'])) ? "" : $this->request['idTrace'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// initialisation
$laTrace = null;

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "" || $mdpSha1 == "" || $idTrace == "" )
    {	$msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
        {   $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 401;
        }
    	else 
    	{	// contrôle d'existence de idTrace
    	    $laTrace = $dao->getUneTrace($idTrace);
    	    if ($laTrace == null)
    	    {  $msg = "Erreur : parcours inexistant.";
    	       $code_reponse = 400;
    	    }
    	    else
    	    {   // récupération de l'id de l'utilisateur demandeur et du propriétaire du parcours
        	    $idDemandeur = $dao->getUnUtilisateur($pseudo)->getId();
        	    $idProprietaire = $laTrace->getIdUtilisateur();
        	    
        	    // vérification de l'autorisation
        	    if ( $idDemandeur != $idProprietaire && $dao->autoriseAConsulter($idProprietaire, $idDemandeur) == false )
        	    {  $msg = "Erreur : vous n'êtes pas autorisé par le propriétaire du parcours.";
        	       $code_reponse = 401;
        	       $laTrace = null;
        	    }
                else
                {   $msg = "Données de la trace demandée.";
                    $code_reponse = 200;
                }
    	    }
    	}
    }
}
// ferme la connexion à MySQL
unset($dao);

// création du flux en sortie
if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML($msg, $laTrace);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg, $laTrace);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================

// création du flux XML en sortie
function creerFluxXML($msg, $laTrace)
{	
    /* Exemple de code XML
        <?xml version="1.0" encoding="UTF-8"?>
        <!--Service web GetUnParcoursEtSesPoints - BTS SIO - Lycée De La Salle - Rennes-->
        <data>
            <reponse>Données de la trace demandée.</reponse>
            <donnees>
                <trace>
                    <id>2</id>
                    <dateHeureDebut>2018-01-19 13:08:48</dateHeureDebut>
                    <terminee>1</terminee>
                    <dateHeureFin>2018-01-19 13:11:48</dateHeureFin>
                    <idUtilisateur>2</idUtilisateur>
                </trace>
                <lesPoints>
                    <point>
                        <id>1</id>
                        <latitude>48.2109</latitude>
                        <longitude>-1.5535</longitude>
                        <altitude>60</altitude>
                        <dateHeure>2018-01-19 13:08:48</dateHeure>
                        <rythmeCardio>81</rythmeCardio>
                    </point>
                    ......................................
                    <point>
                        <id>10</id>
                        <latitude>48.2199</latitude>
                        <longitude>-1.5445</longitude>
                        <altitude>150</altitude>
                        <dateHeure>2018-01-19 13:11:48</dateHeure>
                        <rythmeCardio>90</rythmeCardio>
                    </point>
                </lespoints>
            </donnees>
        </data>
    */
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();
	
	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';
	
	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web GetUnParcoursEtSesPoints - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
	
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' dans l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);

	if ($laTrace != null)
	{
	    // place l'élément 'donnees' dans l'élément 'data'
	    $elt_donnees = $doc->createElement('donnees');
	    $elt_data->appendChild($elt_donnees);
	    
	    // place l'élément 'trace' dans l'élément 'donnees'
	    $elt_trace = $doc->createElement('trace');
	    $elt_donnees->appendChild($elt_trace);
	    
	    // place la description de la trace dans l'élément 'trace'
	    $elt_id = $doc->createElement('id', $laTrace->getId());
	    $elt_trace->appendChild($elt_id);
    	
    	$elt_dateHeureDebut = $doc->createElement('dateHeureDebut', $laTrace->getDateHeureDebut());
    	$elt_trace->appendChild($elt_dateHeureDebut);
  
    	$elt_terminee = $doc->createElement('terminee', $laTrace->getTerminee());
    	$elt_trace->appendChild($elt_terminee);
    	
    	if ($laTrace->getTerminee() == true)
    	{   $elt_dateHeureFin = $doc->createElement('dateHeureFin', $laTrace->getDateHeureFin());
            $elt_trace->appendChild($elt_dateHeureFin);
    	}
    	
    	$elt_idUtilisateur = $doc->createElement('idUtilisateur', $laTrace->getIdUtilisateur());
    	$elt_trace->appendChild($elt_idUtilisateur);
    		
    	// place l'élément 'lesPoints' dans l'élément 'donnees'
    	$elt_lesPoints = $doc->createElement('lesPoints');
    	$elt_donnees->appendChild($elt_lesPoints);
    	// traitement des points
    	if (sizeof($laTrace->getLesPointsDeTrace()) > 0) {
    	    foreach ($laTrace->getLesPointsDeTrace() as $unPointDeTrace)
    		{
    			// crée un élément vide 'point'
    		    $elt_point = $doc->createElement('point');
    		    // place l'élément 'point' dans l'élément 'lesPoints'
    		    $elt_lesPoints->appendChild($elt_point);
    		
    		    // crée les éléments enfants de l'élément 'point'
    		    $elt_id             = $doc->createElement('id', $unPointDeTrace->getId());
    		    $elt_point->appendChild($elt_id);
    		    
    		    $elt_latitude       = $doc->createElement('latitude', $unPointDeTrace->getLatitude());
    		    $elt_point->appendChild($elt_latitude);
    		    
    		    $elt_longitude      = $doc->createElement('longitude', $unPointDeTrace->getLongitude());
    		    $elt_point->appendChild($elt_longitude);
    		    
    		    $elt_altitude       = $doc->createElement('altitude', $unPointDeTrace->getAltitude());
    		    $elt_point->appendChild($elt_altitude);
    		    
    		    $elt_dateHeure      = $doc->createElement('dateHeure', $unPointDeTrace->getDateHeure());
    		    $elt_point->appendChild($elt_dateHeure);
    		    
    		    $elt_rythmeCardio       = $doc->createElement('rythmeCardio', $unPointDeTrace->getRythmeCardio());
    		    $elt_point->appendChild($elt_rythmeCardio);
    		}
    	}
	}
	// Mise en forme finale
	$doc->formatOutput = true;
	
	// renvoie le contenu XML
	return $doc->saveXML();
}

// ================================================================================================

// création du flux JSON en sortie
function creerFluxJSON($msg, $laTrace)
{
    /* Exemple de code JSON
        {
            "data": {
                "reponse": "Données de la trace demandée.",
                "donnees": {
                    "trace": {
                        "id": "2",
                        "dateHeureDebut": "2018-01-19 13:08:48",
                        "terminee: "1",
                        "dateHeureFin: "2018-01-19 13:11:48",
                        "idUtilisateur: "2"
                    }
                    "lesPoints": [
                        {
                            "id": "1",
                            "latitude": "48.2109",
                            "longitude": "-1.5535",
                            "altitude": "60",
                            "dateHeure": "2018-01-19 13:08:48",
                            "rythmeCardio": "81"
                        },
                        ..................................
                        {
                            "id"10</id>,
                            "latitude": "48.2199",
                            "longitude": "-1.5445",
                            "altitude": "150",
                            "dateHeure": "2018-01-19 13:11:48",
                            "rythmeCardio": "90"
                        }
                    ]
                }
            }
        }
     */
    
    if ($laTrace == null) {   
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg];
    }
    else {
        // construction de l'élément "trace"
        $unObjetTrace = array();
        $unObjetTrace["id"] = $laTrace->getId();
        $unObjetTrace["dateHeureDebut"] = $laTrace->getDateHeureDebut();
        $unObjetTrace["terminee"] = $laTrace->getTerminee();
        if ($laTrace->getTerminee() == true)
        {   $unObjetTrace["dateHeureFin"] = $laTrace->getDateHeureFin();
        }
        $unObjetTrace["idUtilisateur"] = $laTrace->getIdUtilisateur();
        
        // construction d'un tableau contenant les points
        $lesObjetsDuTableau = array();
        if (sizeof($laTrace->getLesPointsDeTrace()) > 0) {
            foreach ($laTrace->getLesPointsDeTrace() as $unPointDeTrace)
            {	// crée une ligne dans le tableau
                $unObjetPoint = array();
                $unObjetPoint["id"] = $unPointDeTrace->getId();
                $unObjetPoint["latitude"] = $unPointDeTrace->getLatitude();
                $unObjetPoint["longitude"] = $unPointDeTrace->getLongitude();
                $unObjetPoint["altitude"] = $unPointDeTrace->getAltitude();
                $unObjetPoint["dateHeure"] = $unPointDeTrace->getDateHeure();
                $unObjetPoint["rythmeCardio"] = $unPointDeTrace->getRythmeCardio();
    
                $lesObjetsDuTableau[] = $unObjetPoint;
            }
        }
        // construction de l'élément "donnees"
        $elt_donnees = ["trace" => $unObjetTrace, "lesPoints" => $lesObjetsDuTableau];
        
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg, "donnees" => $elt_donnees];
    }
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
?>

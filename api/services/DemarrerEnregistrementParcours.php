<?php
// Projet TraceGPS - services web
// fichier :  api/services/DemarrerEnregistrementParcours.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service permet à un utilisateur de démarrer l'enregistrement d'un parcours
// Le service web doit recevoir 3 paramètres :
//     pseudo : le pseudo de l'utilisateur
//     mdp : le mot de passe hashé en sha1
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution (avec les données de la trace créée)

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/DemarrerEnregistrementParcours?pseudo=europa&mdp=13e3668bbee30b004380052b086457b014504b3e&lang=xml

// connexion du serveur web à la base MySQL
$dao = new DAO();

// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
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
    if ( $pseudo == "" || $mdpSha1 == "" )
    {	$msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
        {   $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 401;
        }
        else
        {   // récupération de l'id de l'utilisateur
            $idUtilisateur = $dao->getUnUtilisateur($pseudo)->getId();
            
            // créer et enregistrer la trace
            $laTrace = new Trace(0, date('Y-m-d H:i:s', time()), null, "0", $idUtilisateur);
            $ok = $dao->creerUneTrace($laTrace);
            // récupération de l'id de la trace
            $idTrace = $laTrace->getId();
            
            $msg = "Trace créée.";
            $code_reponse = 200;
        }
    }
}
// ferme la connexion à MySQL :
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
        <!--Service web DemarrerEnregistrementParcours - BTS SIO - Lycée De La Salle - Rennes-->
        <data>
          <reponse>Trace créée.</reponse>
          <donnees>
            <trace>
              <id>23</id>
              <dateHeureDebut>2018-11-15 16:15:54</dateHeureDebut>
              <terminee>0</terminee>
              <idUtilisateur>3</idUtilisateur>
            </trace>
          </donnees>
        </data>
     */
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();	

	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';
	
	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web DemarrerEnregistrementParcours - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
		
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' juste après l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	if ($laTrace != null)
	{
	    // place l'élément 'donnees' dans l'élément 'data'
	    $elt_donnees = $doc->createElement('donnees');
	    $elt_data->appendChild($elt_donnees);
	    
	    // crée un élément vide 'trace'
	    $elt_trace = $doc->createElement('trace');
	    // place l'élément 'trace' dans l'élément 'donnees'
	    $elt_donnees->appendChild($elt_trace);
	    
	    // crée les éléments enfants de l'élément 'trace'
    	$elt_idTrace = $doc->createElement('id', $laTrace->getId());
    	$elt_trace->appendChild($elt_idTrace);
    	
    	$elt_dateHeureDebut = $doc->createElement('dateHeureDebut', $laTrace->getDateHeureDebut());
    	$elt_trace->appendChild($elt_dateHeureDebut);
    	
    	$elt_terminee = $doc->createElement('terminee', $laTrace->getTerminee());
    	$elt_trace->appendChild($elt_terminee);
    	
    	$elt_idUtilisateur = $doc->createElement('idUtilisateur', $laTrace->getIdUtilisateur());
    	$elt_trace->appendChild($elt_idUtilisateur);
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
                "reponse": "Trace cr\u00e9\u00e9e.",
                "donnees": {
                    "trace": {
                        "id": "23",
                        "dateHeureDebut": "2018-11-15 16:15:54",
                        "terminee: "0",
                        "idUtilisateur: "3"
                    }
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
        $unObjetTrace["idUtilisateur"] = $laTrace->getIdUtilisateur();
        
        // construction de l'élément "donnees"
        $elt_donnees = ["trace" => $unObjetTrace];
        
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
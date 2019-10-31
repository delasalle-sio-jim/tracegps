<?php
// Projet TraceGPS - services web
// fichier :  api/services/DemanderMdp.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service génère un nouveau mot de passe, l'enregistre en sha1 et l'envoie par mail à l'utilisateur
// Le service web doit recevoir 2 paramètres :
//     pseudo : le pseudo de l'utilisateur
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/DemanderMdp?pseudo=europa&lang=xml

// connexion du serveur web à la base MySQL
$dao = new DAO();

// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "")
    {	$msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {	if ( ! $dao->existePseudoUtilisateur($pseudo) ) {
    		$msg = "Erreur : pseudo inexistant.";
    		$code_reponse = 400;
        }
    	else {
    		// génération d'un nouveau mot de passe
    		$nouveauMdp = Outils::creerMdp();
    		// enregistre le nouveau mot de passe de l'utilisateur dans la bdd après l'avoir codé en MD5
    		$ok = $dao->modifierMdpUtilisateur ($pseudo, $nouveauMdp);
    	
    		if ( ! $ok ) {
    		    $msg = "Erreur : problème lors de l'enregistrement du mot de passe.";
    		    $code_reponse = 500;
    		}
    		else {
    		    // envoie un courriel  à l'utilisateur avec son nouveau mot de passe 
    		    $ok = $dao->envoyerMdp($pseudo, $nouveauMdp);
    		    if (! $ok) {
        		    $msg = "Enregistrement effectué ; l'envoi du courriel  de confirmation a rencontré un problème.";
        		    $code_reponse = 500;
    		    }
    		    else {
        		    $msg = "Vous allez recevoir un courriel avec votre nouveau mot de passe.";
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
    $donnees = creerFluxXML ($msg);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON ($msg);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================

// création du flux XML en sortie
function creerFluxXML($msg)
{	// crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();
	
	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';
	
	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web DemanderMdp - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
	
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' juste après l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	// Mise en forme finale
	$doc->formatOutput = true;
	
	// renvoie le contenu XML
	return $doc->saveXML();
}

// ================================================================================================

// création du flux JSON en sortie
function creerFluxJSON($msg)
{
    /* Exemple de code JSON
     {
        "data": {
            "reponse": "Erreur : pseudo inexistant."
        }
     }
     */
    
    // construction de l'élément "data"
    $elt_data = ["reponse" => $msg];
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
?>

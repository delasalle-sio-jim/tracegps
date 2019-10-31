<?php
// Projet TraceGPS - services web
// fichier : api/services/RetirerUneAutorisation.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service permet à un utilisateur de supprimer une autorisation qu'il avait accordée à un autre utilisateur
// Le service web doit recevoir 5 paramètres :
//     pseudo : le pseudo de l'utilisateur qui retire l'autorisation
//     mdp : le mot de passe hashé en sha1 de l'utilisateur qui retire l'autorisation
//     pseudoARetirer : le pseudo de l'utilisateur à qui on veut retirer l'autorisation
//     texteMessage : le texte d'un message accompagnant la suppression
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/RetirerUneAutorisation?pseudo=callisto&mdp=13e3668bbee30b004380052b086457b014504b3e&pseudoARetirer=europa&texteMessage=C'est fini&lang=xml

// connexion du serveur web à la base MySQL
$dao = new DAO();
	
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$pseudoDestinataire = ( empty($this->request['pseudoARetirer'])) ? "" : $this->request['pseudoARetirer'];
$texteMessage = ( empty($this->request['texteMessage'])) ? "" : $this->request['texteMessage'];
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
    if ( $pseudo == "" || $mdpSha1 == "" || $pseudoDestinataire == "" )
    {	$msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
        {   $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 401;
        }
    	else 
    	{	// contrôle d'existence de $pseudoAretirer
    	    $utilisateurDestinataire = $dao->getUnUtilisateur($pseudoDestinataire);
    	    if ($utilisateurDestinataire == null)
    	    {  $msg = "Erreur : pseudo utilisateur inexistant.";
    	       $code_reponse = 400;
    	    }
    	    else
    	    {   $utilisateurAutorisant = $dao->getUnUtilisateur($pseudo);
        	    $idAutorisant = $utilisateurAutorisant->getId();
        	    $idAutorise = $utilisateurDestinataire->getId();
        	    
        	    if ( $dao->autoriseAConsulter($idAutorisant, $idAutorise) == false ) {
    	            $msg = "Erreur : l'autorisation n'était pas accordée.";
    	            $code_reponse = 400;
    	        }
    	        else {
    	            // suppression de l'autorisation
    	            $ok = $dao->supprimerUneAutorisation($idAutorisant, $idAutorise);
    	            if ( ! $ok ) {
                        $msg = "Erreur : problème lors de la suppression de l'autorisation.";
                        $code_reponse = 500;
                    }
                    else {
                        // la suppression a fonctionné
                        if ($texteMessage == '') {
                            // l'utilisateur ne souhaite pas envoyer de courriel
                            $msg = "Autorisation supprimée.";
                            $code_reponse = 200;
                        }
                        else {
                            // l'utilisateur souhaite envoyer un courriel
                            $adrMail = $utilisateurDestinataire->getAdrMail();
                            $sujetMail = "Suppression d'autorisation de la part d'un utilisateur du système TraceGPS";
                            $contenuMail = "Cher ou chère " . $pseudoDestinataire . "\n\n";
                            $contenuMail .= "L'utilisateur " . $pseudo . " du système TraceGPS vous retire l'autorisation de suivre ses parcours.\n\n";
                            $contenuMail .= "Son message : " . $texteMessage . "\n\n";
                            $contenuMail .= "Cordialement.\n";
                            $contenuMail .= "L'administrateur du système TraceGPS";
                            
                            // cette variable globale est définie dans le fichier modele/parametres.php
                            global $ADR_MAIL_EMETTEUR;
                            
                            $ok = Outils::envoyerMail($adrMail, $sujetMail, $contenuMail, $ADR_MAIL_EMETTEUR);
                            if ( ! $ok ) {
                                $msg = "Erreur : autorisation supprimée ; l'envoi du courriel de notification a rencontré un problème.";
                                $code_reponse = 400;
                            }
                            else {
                                // tout a fonctionné
                                $msg = "Autorisation supprimée ; " . $pseudoDestinataire . " va recevoir un courriel de notification.";
                                $code_reponse = 200;
                            }
                        }
    
                    }
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
    $donnees = creerFluxXML($msg);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg);
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
	$elt_commentaire = $doc->createComment('Service web RetirerUneAutorisation - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
	
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' dans l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	// place l'élément 'donnees' dans l'élément 'data'
	$elt_donnees = $doc->createElement('donnees');
	$elt_data->appendChild($elt_donnees);

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
                "reponse": "Erreur : authentification incorrecte."
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

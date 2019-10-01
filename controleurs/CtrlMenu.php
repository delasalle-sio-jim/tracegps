<?php
// Projet TraceGPS - version web mobile
// fichier : controleurs/CtrlMenu.php
// Rôle : traiter la demande d'accès au menu
// Dernière mise à jour : 9/1/2018 par JM CARTRON

// on vérifie si le demandeur de cette action est bien authentifié
if ( $_SESSION['niveauConnexion'] == 0) {
    // si le demandeur n'est pas authentifié, il s'agit d'une tentative d'accès frauduleux
    // dans ce cas, on provoque une redirection vers la page de connexion
    header ("Location: index.php?action=Deconnecter");
}
else {
    // connexion du serveur web à la base MySQL
    include_once ('modele/DAO.class.php');
    $dao = new DAO();
    
    // affiche la vue
    include_once ('vues/VueMenu.php');
}
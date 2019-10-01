<?php
    // Projet TraceGPS - version web mobile
    // fichier : vues/VueConnecter.php
    // Rôle : visualiser la vue de connexion d'un utilisateur
    // cette vue est appelée par le contrôleur controleurs/CtrlConnecter.php
    // Dernière mise à jour : 11/1/2018 par JM CARTRON

	// pour obliger la page à se recharger
// 	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
// 	header('Pragma: no-cache');
// 	header('Content-Tranfer-Encoding: none');
// 	header('Expires: 0');

?>
<!doctype html>
<html>
	<head>	
		<?php include_once ('vues/head.php'); ?>
		
		<script>
			// version jQuery activée
			
			// associe une fonction à l'événement pageinit
			$(document).bind('pageinit', function() {
				// l'événement "click" de la case à cocher "caseAfficherMdp" est associé à la fonction "afficherMdp"
				$('#caseAfficherMdp').click( afficherMdp );
				
				// selon l'état de la case, le type de la zone de saisie est "text" ou "password"
				afficherMdp();
				
				// affichage du dernier mot de passe saisi (désactivé ici, car effectué dans le code HTML du formulaire)
				// $('#txtMdp').attr('value','<?php echo $mdp; ?>');
				
				<?php if ($typeMessage != '') { ?>
					// affiche la boîte de dialogue 'affichage_message'
					$.mobile.changePage('#affichage_message', {transition: "<?php echo $transition; ?>"});
				<?php } ?>
			} );

			// selon l'état de la case, le type de la zone de saisie est "text" ou "password"
			function afficherMdp() {
				// tester si la case est cochée
				if ( $("#caseAfficherMdp").is(":checked") ) {
					// la zone passe en <input type="text">
					$('#txtMdp').attr('type', 'text');
				}
				else {
					// la zone passe en <input type="password">
					$('#txtMdp').attr('type', 'password');
				};
			}
			// fin de la version jQuery
			
			/*
			// version javaScript désactivée
			
			// selon l'état de la case, le type de la zone de saisie est "text" ou "password"
			function afficherMdp()
			{	// document.getElementById("caseAfficherMdp").checked = ! document.getElementById("caseAfficherMdp").checked;
				if (document.getElementById("caseAfficherMdp").checked == true)
					document.getElementById("txtMdp").type="text";
				else
					document.getElementById("txtMdp").type="password";
			}
			
			function initialisation()
			{	afficherMdp();
				document.getElementById("txtMdp").innerText="<?php //echo $mdp; ?>"
			}
			// fin de la version javaScript
			*/
		</script>
	</head>
	
	<body>
		<div data-role="page" id="page_principale">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4><?php echo $TITRE_APPLI; ?></h4>
			</div>
			
			<div data-role="content">
				<div data-role="collapsible-set">
					<div data-role="collapsible">
						<h3>Bienvenue sur TraceGPS...</h3>
						<p>L'application <b>TraceGPS</b> vous propose les services suivants :</p>
						<ul>
							<li>Créer une trace de votre activité sportive</li>
							<li>Autoriser un proche à voir vos traces</li>
							<li>Demander l'autorisation de suivre les traces d'un autre membre</li>
							<li>Consulter une trace (si autorisation)</li>
						</ul>
					</div>
			
					<div data-role="collapsible" <?php if($divCreerCompteDepliee == true) echo ('data-collapsed="false"'); ?>>
						<h3>Créer mon compte</h3>
						<p>Si vous n'avez pas encore de compte, commencez par le créer.</p>
						<p>Après validation, vous recevrez un mail de confirmation avec votre mot de passe (que vous pourrez ensuite modifier).</p>
						<p style="margin-top: 0px; margin-bottom: 0px;">
							<a href="index.php?action=CreerUtilisateur" data-role="button" data-mini="true" data-ajax="false">Créer mon compte</a>
						</p>
					</div>
					
					<div data-role="collapsible" <?php if($divConnecterDepliee == true) echo ('data-collapsed="false"'); ?>>
						<h3>Accéder à mon compte</h3>
						<form name="form1" id="form1" action="index.php?action=Connecter" data-ajax="false" method="post" data-transition="<?php echo $transition; ?>">
							<div data-role="fieldcontain" class="ui-hide-label">
								<label for="txtPseudo">Utilisateur :</label>
								<input type="text" name="txtPseudo" id="txtPseudo" data-mini="true" placeholder="Mon pseudo" required value="<?php echo $pseudo; ?>" >
		
								<label for="txtMdp">Mot de passe :</label>
								<input type="<?php if($afficherMdp == 'on') echo 'text'; else echo 'password'; ?>" name="txtMdp" id="txtMdp" data-mini="true" 
									required placeholder="Mon mot de passe" value="<?php echo $mdp; ?>" >
							</div>														
							<div data-role="fieldcontain" data-type="horizontal" class="ui-hide-label">
								<label for="caseAfficherMdp">Afficher le mot de passe en clair</label>
								<input type="checkbox" name="caseAfficherMdp" id="caseAfficherMdp" onclick="afficherMdp();" data-mini="true" <?php if ($afficherMdp == 'on') echo 'checked'; ?>>
							</div>
							<div data-role="fieldcontain" style="margin-top: 0px; margin-bottom: 0px;">
								<p style="margin-top: 0px; margin-bottom: 0px;">
									<input type="submit" name="btnConnecter" id="btnConnecter" data-mini="true" data-ajax="false" value="Me connecter">
								</p>
							</div>
						</form>
					</div>						
						
					<div data-role="collapsible" <?php if($divDemanderMdpDepliee == true) echo ('data-collapsed="false"'); ?>>
						<h3>J'ai oublié mon mot de passe</h3>
						<p>Cette option permet de regénérer un nouveau mot de passe qui vous sera immédiatement envoyé par mail.</p>
						<p>Il est conseillé de le changer aussitôt.</p>
						<p style="margin-top: 0px; margin-bottom: 0px;">
							<a href="index.php?action=DemanderMdp" data-role="button" data-mini="true" data-transition="<?php echo $transition; ?>">Obtenir un nouveau mot de passe</a>
						</p>
					</div>

					
					<div data-role="collapsible">
						<h3>Télécharger l'application Android</h3>
						<?php if ($OS == "Android") { ?>
							<p>Vous disposez d'un appareil fonctionnant sous Android.</p>
							<p>Vous pouvez télécharger la version Android de cette application.</p>
							<p style="margin-top: 0px; margin-bottom: 0px;">
								<a href="./controleurs/CtrlTelechargerApk.php" data-role="button" data-mini="true" data-transition="<?php echo $transition; ?>">Télécharger l'application Android</a>
							</p>
						<?php } else {;?>
							<p>Vous ne disposez pas d'un appareil fonctionnant sous Android.</p>
							<p>Vous ne pouvez pas télécharger la version Android de cette application.</p>
						<?php };?>
					</div>
				
				</div>	
			</div>
			
			<div data-role="footer" data-position="fixed" data-theme="<?php echo $themeNormal; ?>">
				<h4><?php echo $NOM_APPLI; ?></h4>
			</div>
		</div>
		
		<?php include_once ('vues/dialog_message.php'); ?>
		
	</body>
</html>
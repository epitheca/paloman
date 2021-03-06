<?php
// Formulaire de saisie/modification d'un observateurs

include ("Listes.php");
require ("Observateurs_fonctions.php");

function Formobservateurs ($mode, $code, $bd) 
{ 
	if ($mode == "MAJperso") 
  {
  $resultat = $bd->execRequete ("SELECT * FROM observateurs Where code_obs = '$code'");
  	 while ($bo = $bd->objetSuivant ($resultat))
	 { 
		 
?>
<div class="bloc-50pc-gauche">
	<form method="post" action="ObservateursMAJ.php">
        
	<div class="titre">Votre identité</div>
		<input type="hidden"  name="mode"  value="MAJperso">
		<input type="hidden"  name="code_obs"  value="<?php echo $code?>">
		Prénom :<input type="text" value="<?php echo $bo->prenom;?>" name="prenom"><br>
		NOM : <input type="text" value="<?php echo $bo->nom;?>" name="nom"><br>
		Courriel: <input type="email" value="<?php echo $bo->email;?>"  name="email">
		<br><br>
		<div class="titre">Changer votre mot de passe</div>
		Nouveau :
		<input type="password" value=""  style="width:60%;" name="nouveau_mot_de_passe" autocomplete="off"><br>
		Confirmation :
		<input type="password" value="" style="width:60%;"  name="conf_passe" autocomplete="off">

		<p align=center><INPUT type="submit" name="maj" value="Valider"><br>Vous serez automatiquement déconnecté</p>
	
	</form>
</div>

<div class="bloc-50pc-droit">
	<div class="titre">Vos préférences</div>
		
<?php
		//Vérification de la liste des observateurs associés
	$obs_associe=observateursAssocies ($code, $bd);
	
		//Proposition d'association
	$obs_demande=observateursAssociesReverse ($code, $bd);
	
		?>
</div>
	<?php	
         }
        }
  
 if ($mode == "INSERTION")
   {	   
	   ?>
<div class="demi-gauche">
	<form method="post" action="Observateurs_maj_admin.php">  
		<div class="bloc-50%-clair-gauche">			
			<div class="titre">Identité</div>
			<input type="hidden"  name="mode"  value="INSERTION">
			<input type="hidden"  name="code_obs"  value="">
			<input type="hidden"  name="nouveau_mot_de_passe"  value="">
			<input type="hidden"  name="conf_passe"  value="">

		Nom: <input type="text" value="" style="margin-left: 22px; width:350px" name="nom"><br>
		Prénom: <input type="text" value="" style="width:350px" name="prenom"><br>
		Courriel: <input type="email" value="" style="margin-left: 0px; width:350px" name="email">
		<p align=right><INPUT type="submit" name="maj" value="Créer un compte"></p>
		</div>
	</form>
</div>
 <?php  
}}
?>

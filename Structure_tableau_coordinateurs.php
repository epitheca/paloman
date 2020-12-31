<!--
Copyright Mathieu MONCOMBLE (contact@epitheca.fr) 2009-2020

This file is part of epitheca.

    epitheca is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License.

    epitheca is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with epitheca.  If not, see <https://www.gnu.org/licenses/>.
-->

<?php
// Tableau pour les coordinateurs ayant accepté
function TabStructureCoordinateurs ($code, $bd)
{
	//Réinitialisation de la valeur i
	$i=0;
	
	//Test de la requête
	$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs WHERE code_structure='$code'");
	while ($bo = $bd->objetSuivant ($resultat))
	$i=1;

	//Il n'y a pas d'occurence.
	if ($i==0) echo "Il n'existe actuellement aucun coordinateur pour votre structure."; 

	//Il y a au moins une occurence
	if ($i<>0)
	{
		?>
		<p class="sous-titre">Voici la liste des coordinateurs pour votre structure.</p>
		<div class="spacer"></div>
		<div class="tableau-donnees-1">Coordinateur(s)</div>
		<div class="tableau-donnees-1">Taxons</div>
		<div class="tableau-donnees-1">Suppression</div>
		<div class="spacer"></div>
		<?php
		
		//Recquête pour la sélection des données
		$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs WHERE code_structure='$code'");
		while ($bo = $bd->objetSuivant ($resultat))
		{
			//On récupére les informations sur l'observateur
			$observateur=Chercheobservateursaveccode ($bo->code_observateur, $bd, $format=FORMAT_OBJET) ;

			//On récupère le nom du taxon
			$requete=$bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre = '$bo->taxon'");
			while ($bc = $bd->objetSuivant ($requete))
			$taxon=$bc->Classe_ordre;

			//Ancre pour la suppression
			$textesup = Ancre_renomme ("Structure.php?numero=$bo->numero&mode=def", "Supprimer");	
	   
			?>
			<div class="tableau-donnees-1"><?php echo "$observateur->prenom $observateur->nom";?></div>
			<div class="tableau-donnees-1"><?php echo $taxon;?></div>
			<div class="tableau-donnees-1"><?php echo $textesup;?></div>
			<div class="spacer"></div>
			<?php
		}
	}
}

// Tableau pour les coordinateurs une demande en cours
function TabStructureCoordinateursDemande ($code, $bd)
{
	//Réinitialisation de la valeur i
	$i=0;
	
	//Test de la requête
	$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs_temporaire WHERE code_structure='$code'");
	while ($bo = $bd->objetSuivant ($resultat))
	$i=1;

	//Il n'y a pas d'occurence
	if ($i==0) echo "<br><br><p class='sous-sous-titre'>Il n'existe actuellement aucune demande en cours.</p>"; 

	//Entête de tableau
	if ($i<>0)
	{
		?>
		<br><br>
		<div class="spacer"></div>
		<p class='sous-sous-titre'>Voici les demandes en cours</p>
		<div class="spacer"></div>
		<div class="tableau-donnees-1">Coordinateur(s)</div>
		<div class="tableau-donnees-1">Taxons</div>
		<div class="tableau-donnees-1">Suppression</div>
		<div class="spacer"></div>
		<?php

		//Recquête pour la sélection des données
			$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs_temporaire WHERE code_structure='$code'");
			while ($bo = $bd->objetSuivant ($resultat))
			{
				//On récupére les informations sur l'observateur
				$observateur=Chercheobservateurs ($bo->code_obs, $bd, $format=FORMAT_OBJET) ;

				//On récupère le nom du taxon
				$requete=$bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre = '$bo->Classe_ordre'");
				while ($bc = $bd->objetSuivant ($requete))
				$taxon=$bc->Classe_ordre;

				//suppression
				$textesup = Ancre_renomme ("Structure.php?numero=$bo->numero&mode=temp", "Supprimer");	
		?>

				<div class="tableau-donnees-1"><?php echo "$observateur->prenom $observateur->nom";?></div>
				<div class="tableau-donnees-1"><?php echo $taxon;?></div>
				<div class="tableau-donnees-1"><?php echo $textesup; ?></div>
				<div class="spacer"></div>
		<?php
			}
	}
}

// Tableau pour les observateurs ayant accepté
function TabStructureObservateurs ($code, $bd)
{
	//Réinitialisation de la valeur i
	$i=0;
	
	//Test de la requête
	$resultat = $bd->execRequete ("SELECT * FROM structure_observateurs WHERE code_structure='$code'");
	while ($bo = $bd->objetSuivant ($resultat))
	$i=1;

	//Il n'y a pas d'occurence.
	if ($i==0) echo "Il n'existe actuellement aucun observateur pour votre structure."; 

	//Il y a au moins une occurence
	if ($i<>0)
	{
		?>
		<p class="sous-sous-titre">Voici la liste des observateurs qui collaborent avec votre structure.</p>
		<div class="tableau-donnees-1">Observateurs</div>
		<div class="tableau-donnees-1">Nombre de données</div>
		<div class="tableau-donnees-1">Contacter</div>
		<div class="spacer"></div>
		<?php
		
		//Recquête pour la sélection des données
		$resultat = $bd->execRequete ("SELECT * FROM structure_observateurs WHERE code_structure='$code' AND mode='accepte'");
		while ($bo = $bd->objetSuivant ($resultat))
		{
			//On récupére les informations sur l'observateur
			$observateur=Chercheobservateursaveccode ($bo->code_observateur, $bd, $format=FORMAT_OBJET) ;

			//Ancre pour le contact
			$textecontact = Ancre_renomme ("mailto:$observateur->email", "Contacter");	
	   
			// Structure
			$structure=ChercheStructureAvecCodeStructure ($code, $bd);

			// Compte du nombre de données
			$resultat = $bd->execRequete  ("SELECT COUNT(*) as nombre FROM donnees WHERE  
				(obs_1= '$bo->code_observateur' 	
				OR obs_2= '$bo->code_observateur' 
				OR obs_3= '$bo->code_observateur') 
				AND latitude BETWEEN '$structure->y_latitude' AND '$structure->x_latitude' 
				AND longitude BETWEEN '$structure->x_longitude' AND '$structure->y_longitude'");
				 while ($bi = $bd->objetSuivant ($resultat))
				 $nombre=$bi->nombre;

			?>
			<div class="tableau-donnees-1"><?php echo "$observateur->prenom $observateur->nom";?></div>
			<div class="tableau-donnees-1"><?php echo $nombre;?></div>
			<div class="tableau-donnees-1"><?php echo $textecontact;?></div>
			<div class="spacer"></div>
			<?php
		}
	}
}
?>
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

//Requete
$requete="SELECT COUNT(*) AS nbr, numero, obs_1, date, longitude, latitude, corine_1, corine_2, corine_3, corine_4, vent, meteo, temperature, riviere, route  
FROM donnees
WHERE 
(obs_1= '$code_obs' 
OR obs_2= '$code_obs' 
OR obs_3= '$code_obs') 
AND date BETWEEN '$dateenmin' AND '$dateenmax'
AND latitude BETWEEN '$latitude_Y' AND '$latitude_X' 
AND longitude BETWEEN '$longitude_X' AND '$longitude_Y'
$filtre
GROUP BY obs_1, date, longitude, latitude, corine_1, corine_2, corine_3, corine_4, vent, meteo, temperature, riviere, route 
ORDER by numero DESC, date DESC, longitude, latitude, Date DESC LIMIT $debut, $nombre_resultats";

//Affichage des fiches

//Ajout de la pagination
if ($num_rows<>0)
{
//echo '<CENTER>';
//Début du formulaire pour garder la requête groupée en cas de multi page
?>
<!--ouverture du bloc page-->
<div id="bloc-page">
	<div class="bloc-75pc-gauche">
		<div class="titre">Vos fiches</div>
			
			<!--Formulaire pour le passage des pages-->
			<form method="post" action="Consultation.php">
			<input type="hidden"  name="page"  value="<?php echo $page;?>">
				<?php
				$nombredepage=$num_rows/$nombre_resultats;
				$nombredepage=round($nombredepage,0);
				$pageprecedente=$page-1;
				$pagesuivante=$page+1;
				echo "<p align=center>";
				   if($page != 1)
				   {
			?>
					
					<INPUT type="submit" name="precedent" value="Fiches précédentes (<?php echo "$pageprecedente/$nombredepage)";?>">
			<?php
					}
				  
					if($page*$nombre_resultats < $num_rows)
					{
			?>
					<INPUT type="submit" name="suivant" value="Fiches suivantes (<?php echo "$pagesuivante/$nombredepage)";?>">
					<br>
			<?php
					}
			?>
			
			<br>
			
			<!--Conteneur pour les fiches-->
			<div class="conteneur">
			<?php
				//Début de la requête pour les fiches
				$resultat = $bd->execRequete ($requete); 
				while ($bo = $bd->objetSuivant ($resultat))
				{
					//Transformation de la date
					$datefr =dateservertosite ($bo->date);
			?>
					<!--début de la fiche--->
					<p class="flottante"> 		
					<br>
					<?php
					echo $datefr;
					echo "<br>";
					
					echo "<br>"."long: $bo->longitude"; 
					echo "<br>"."lat: $bo->latitude";
					
					echo"<br>";
					if ($bo->nbr==1) $ortho="";
					else $ortho="s";
					echo "<br>";
                    
					//Création des listes d'espèces
					$liste="";
					$resultata = $bd->execRequete ("SELECT * FROM donnees WHERE latitude='$bo->latitude' AND longitude='$bo->longitude' AND (obs_1='$code_obs' OR obs_2='$code_obs' OR obs_3='$code_obs') AND date='$bo->date'");
					while ($bs = $bd->objetSuivant ($resultata))
					{
						$i++;
                        $spscien=ChercheSpavecCode ($bs->espece, $bd);
                        $liste .="$spscien->NOM_VALIDE<br>";
					}
				?>
				<span class="info-droite-gauche" href="#"><?php echo "$bo->nbr donnée$ortho";?><span>
				<?php echo $liste;?></span></span>
				<?php
				echo"<br>";
				//Recherche de formulaire spécial
				$completer="Ajout.php?mode=completer&amp;numero=$bo->numero";
				?>
				<a href="<?php echo $completer;?>" class="lien_fonce">Voir la fiche /<br>Compléter</a><br>
				<?php
			}	
				?>		
		<!--Fermeture du conteneur-->
		</div>

	<!--Fermeture du 75%-->
	</div>
	<?php
}
else
{
	?>
	<!--ouverture du bloc page-->
<div id="bloc-page">
	<div class="bloc-75pc-gauche">
		<div class="titre">Il n'y a aucune donnée à afficher</div>
	</div>

		<?php
}
	


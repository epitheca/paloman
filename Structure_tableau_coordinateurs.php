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
// Tableau pour les coordinateurs
function TabStructureCoordinateurs ($code, $bd)
{
	//Réinitialisation de la valeur i
	$i=0;
	
//Test de la requête
$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs WHERE code_structure='$code'");
while ($bo = $bd->objetSuivant ($resultat))
{$i=1;}

if ($i==0) echo "Il n'existe actuellement aucun coordinateur pour votre structure."; 

//Entête de tableau
if ($i<>0)
{
	?>
	
<div class="tableau-donnees-1">
Coordinateur(s)
</div>
<div class="tableau-donnees-1">
Taxons
</div>
<div class="tableau-donnees-1">
Suppression
</div>

<div class="spacer"></div>
<?php


$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs WHERE code_structure='$code'");
while ($bo = $bd->objetSuivant ($resultat))
{
//On récupére les informations sur l'observateur
$observateur=Chercheobservateursaveccode ($bo->code_observateurs, $bd, $format=FORMAT_OBJET) ;

//On récupère le nom du taxon
$requete=$bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre = '$bo->taxon'");
	while ($bc = $bd->objetSuivant ($requete))
$taxon=$bc->Classe_ordre;

	//suppression
	$textesup = Ancre_renomme ("Structure.php?numero=$bo->numero", "Supprimer");	
	   
?>

<div class="tableau-donnees-1">
<?php echo "$observateur->prenom $observateur->nom";?>
</div>
<div class="tableau-donnees-1">
<?php echo $taxon;?>
</div>
<div class="tableau-donnees-1">
<?php echo $textesup; ?>
</div>
<div class="spacer"></div>
<?php
}}}

function TabStructureCoordinateursDemande ($code, $bd)
{
	//Réinitialisation de la valeur i
	$i=0;
	
//Test de la requête
$resultat = $bd->execRequete ("SELECT * FROM structure_coordinateurs_temporaire WHERE code_structure='$code'");
while ($bo = $bd->objetSuivant ($resultat))
{$i=1;}

if ($i==0) echo "Il n'existe actuellement aucune demande en cours."; 

//Entête de tableau
if ($i<>0)
{
	?>
<br><br>
<div class="spacer"></div>
Voici les demandes en cours
<div class="spacer"></div>
<div class="tableau-donnees-1">
Coordinateur(s)
</div>
<div class="tableau-donnees-1">
Taxons
</div>
<div class="tableau-donnees-1">
Suppression
</div>

<div class="spacer"></div>
<?php


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
	$textesup = Ancre_renomme ("Structure.php?numero=$bo->numero&mode=sup", "Supprimer");	
	   
?>

<div class="tableau-donnees-1">
<?php echo "$observateur->prenom $observateur->nom";?>
</div>
<div class="tableau-donnees-1">
<?php echo $taxon;?>
</div>
<div class="tableau-donnees-1">
<?php echo $textesup; ?>
</div>
<div class="spacer"></div>
<?php
}}}
?>

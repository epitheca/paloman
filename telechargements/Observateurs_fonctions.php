<?php

//Fonction pour la recherche et l'affichage des observtateurs ayant acceptés l'association.
function observateursAssocies ($code_obs, $bd)
{
   //déclaration de la variable pour compter les associés
   $i=0; 
   
   //déclarration de la variable pour la première occurence 
   $premiere=1;
    
    //déclaration de la variable pour la liste;
    $liste="";

    $requete=$bd->execRequete ("SELECT * FROM observateurs WHERE code_obs = '$code_obs'");
	while ($bo = $bd->objetSuivant ($requete))
	{
		while ($i<20)
		{
			$i++;
			$association = "association_$i";
			if ($bo->$association==null) $association = "0";
			else
			{
				$association = $bo->$association;
				//Recherche du nom de l'observateur
				$obs = Chercheobservateursaveccode ($association, $bd, $format=FORMAT_OBJET);
				//Ajout du titre car c'est la première occurence
					$premiere=$premiere++;
					if ($premiere==1) 	echo "<br><br><u>Vous avez autorisé les observateurs suivants à vous associer à leurs données :</u><br>";
					echo "<br> $obs->prenom $obs->nom";
			}
		}
	}
}

function observateursAssociesReverse ($code_obs, $bd)
{
  ?>
  <br><br><u>Effectuer une demande d'association à un observateur.</u><br>
  	
  	<form method="post" action="Observateurs_demande.php">
	<input type="hidden"  name="code_obs"  value="<?php echo $code_obs?>">
	<input type="email"  name="email"  placeholder="Saisissez l'adresse" required >
	<input type="submit" name="valider" value="Valider">
	</form>
	<?php
}

?>

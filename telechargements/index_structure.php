<?php

	//Mise à 0 du compteur
	$i=0;
	
	//Initialisation du tableau
		 $tableau = [];
		 
	//Recherche des structures
	$res = $bd->execRequete  ("SELECT * FROM structure");
     while ($bo = $bd->objetSuivant ($res))
     {
		 $i++;
		 $resultat = $bd->execRequete  ("SELECT COUNT(*) as nombre FROM donnees WHERE  
	(obs_1= '$code_obs' 	
	OR obs_2= '$code_obs' 
	OR obs_3= '$code_obs') 
	AND latitude BETWEEN '$bo->y_latitude' AND '$bo->x_latitude' 
	AND longitude BETWEEN '$bo->x_longitude' AND '$bo->y_longitude'");
     while ($bi = $bd->objetSuivant ($resultat))
     {
		 $a="$i-nom";
		$tableau_nom[$i] = "$bo->nom";
		$tableau_nombre[$i]="$bi->nombre";
		$tableau_logo[$i]="$bo->fichier_logo";
	 }

}

if ($i<>0)
{
    ?>
    <div class='titre'>Vos données pourraient servir !</div>
Vous voyez apparaître ici le nom des associations qui souhaitent pouvoir accéder à vos données. Vous pouvez accepter de leur offrir dans les conditions qu’ils vous proposent dans la charte. À tout moment, vous pouvez arrêter de partager vos données avec cette association dans l'onglet "mon compte". Par contre, cette association aura toujours accès aux données que vous aviez accepté de partager précédemment.    <br><br>
<?php

    for ($j = 1; $i >=$j; $j++) {{
	echo "<div class='flux_donnees_gauche'>";
		echo "<div class='index_structure_logo'>";
		echo "<img src='images/logo/".$tableau_logo[$j]."' width='50'></div>";
		echo "<div class='index_structure_nom'>";
		echo $tableau_nom[$j]. "<br>";
	    echo $tableau_nombre[$j]." données sont concernées pour le moment.<br></div>";
	    echo "<div class='index_structure_nom'>";
	    echo "<a href='un lien'><img src='images/pdf.jpg' width='50' title='Lire la charte'></a>";
	    ?>
	     <form action='structure_acceptation.php' method='post'>
			 <input type='hidden' name='structure'>
			 <input type='submit' name='acceptation' class='vert' value='accepter'>
			 <input type='submit' name='refus' class='vert' value='refuser'>
			 </form></div>
			 <div class='lisere-clair'></div>
	    <?php
	    echo "<div class='spacer'></div>";
	   
    echo "</div><br><br>";
}}}

?>

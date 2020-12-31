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
session_start ();
require("Util.php");
require_once ("Ajout_formulaire.php");
require_once ("Ajout_tableaudonnees.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

if (isset($_POST['acces_rapide']))
{
	//Récupération de la valeur
	if ($_POST['numero']<>"")
	{
		$controle= Controle_droit_donnee ($_POST['numero'], $_POST['code_obs'], $bd);
			
			//Construction de l'URL en fonction des droits
			if ($controle =="oui") $url="Ajout.php?mode=completer&numero=$numero";
			else $url="https://epitheca.fr";
				?>	
				<SCRIPT>
				document.location.href="<?php echo $url; ?>"
				</SCRIPT>
				<?php
	}
	else 
	{
		?>
			<SCRIPT>
				document.location.href="https://epitheca.fr"
			</SCRIPT>
	<?php
	}
}
?>

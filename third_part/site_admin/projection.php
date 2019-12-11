<html>
	<head>
		<title>Projection</title>
	</head>	
	<body>
		<h2>Recherche :</h2>
		<form method="POST" action="projection.php">
			<table>
				<tr>
				<td>Nom du film :</td>
				<td><input type="text" name="film"></td>
				</tr>		
				<tr>
				<td>Num de salle :</td>
				<td><input type="text" name="salle"></td>
				</tr>		
				<tr>
				<td>Nom du cinéma :</td>
				<td><input type="text" name="cinema"></td>
				</tr>
				<tr>
				<td>Jour :</td>
				<td><input type="date" name="jour" size="8" placeholder="yyyy-mm-dd"></td>
				</tr>
				<tr>
				<td>Heure :</td>
				<td><input type="date" name="heure" size="8" placeholder="hh:mm:ss"></td>
				</tr>
				<tr>
				<td>Version :</td>
				<td><input type="radio" name="version" value="vf" >vf
				    <input type="radio" name="version" value="vo" >vo</td>
				</tr>
			</table>
			<input type="submit" value="rechercher">
			<input type="reset" value="annuler">
		</form>
		<?php
			session_start();
			if(!isset($_SESSION['admin']))  {
				header("Location: se_connecter.php");
				exit();
			}
			
			$link = new mysqli("localhost", "Admin", "admin");
			if($link->connect_errno) {
				die ("erreur connection");
			}
			$link->select_db('Projet') or die("Erreur de selection de la BD: " . $link->error);
			
			/* SURTOUT NE PAS EFFACER */
			/* C'EST LA RECHERCHE */
			$array = array();
			$have = 0;
			
			if(isset($_POST['jour'])) {
				$jour = $_POST['jour'];
				$array[] = "s.jour like \"%$jour%\"";
				$have++;
			}
			if(isset($_POST['heure'])) {
				$heure = $_POST['heure'];
				$array[] = "s.heure like \"%$heure%\"";
				$have++;
			}	
			if(isset($_POST['version'])) {
				$version = $_POST['version'];
				$array[] = "s.version like \"%$version%\" ";
				$have++;
			}
			if(isset($_POST['film'])) {
				$film = $_POST['film'];
				$array[] = "s.num_film IN (SELECT f.num_film FROM Film f WHERE f.nom like \"%$film%\")";
				$have++;
			}
			if(isset($_POST['salle']) and is_numeric($_POST['salle'])) {
				$salle = $_POST['salle'];
				$array[] = "s.num_salle = $salle";
				$have++;
			}
			if(isset($_POST['cinema'])) {
				$cinema = $_POST['cinema'];
				$array[] = "s.nom_du_cinema like \"%$cinema%\"";
				$have++;
			}
			
			if($have == 0) {
				$query = "SELECT s.*, f.nom as nom_film FROM Se_joue_dans s, Film f where s.num_film = f.num_film;";
			}
			else {
				$query = "SELECT s.*, f.nom as nom_film FROM Se_joue_dans s, Film f where s.num_film = f.num_film and ";
				$query = $query . " " . $array[$have-1];
				$have--;
				while($have > 0) {
					$query = $query . " and " . $array[$have-1];
					$have--;
				}
				$query = $query . " ;" ;
			}
			/* SURTUOT NE PAS EFFACER */
			
			$result = $link->query($query) or die("erreur select");
			
			print "<h2>Résultat :</h2>";
			
			if(isset($_GET['modif'])) {
				$ancien_nom = $_GET['modif'];
				print "erreur modification de la projection $ancien_nom";
			}
			
			print "<table border><tr><th>ID</th><th>Date</th><th>Heure</th><th>Version</th><th>Nom du film</th><th>Num de Salle</th><th>Nom du Cinéma</th></tr>";
			$nb_res = 0;
			while($tuple = mysqli_fetch_object($result)) {
				$nb_res++;
				print "
					<tr>
					<form method=\"POST\" action=\"modifier_projection.php\">
						<td><input type=\"text\" value=\"$tuple->num_se_joue\" name=\"num_se_joue\" size=\"5\" placeholder=\"min 1\"></td>
						<td><input type=\"text\" value=\"$tuple->jour\" name=\"jour\" size=\"8\"></td>
						<td><input type=\"text\" value=\"$tuple->heure\" name=\"heure\" size=\"8\"></td>
						<td><input type=\"text\" value=\"$tuple->version\" name=\"version\" size=\"5\"></td>
						<td><input type=\"text\" value=\"$tuple->nom_film\" name=\"nom_film\"</td>
						<td><input type=\"text\" value=\"$tuple->num_salle\" name=\"num_salle\" size=\"5\" ></td>
						<td><input type=\"text\" value=\"$tuple->nom_du_cinema\" name=\"nom_cinema\" ></td>
						<td><input type=\"submit\" value=\"modifier\"></td>
					</form>
					<form method=\"POST\" action=\"supprimer_projection.php\">
						<td><input type=\"text\" value=\"$tuple->num_se_joue\" name=\"num\" hidden>
						<input type=\"submit\" value=\"supprimer\"></td>
					</form>
					</tr>
				";
			}
			print "</table>";
			if($nb_res == 0) {
				print "<h3>Aucun Résultat</h3>";
			}
		?>
		<h2>Insérer :</h2>
		<?php
			if(isset($_GET['inser']) and isset($_GET['num'])) {
				$ancien_num = $_GET['num'];
				print "impossible d'insérer la personne $ancien_num";
			}
		?>
		<table border>
			<form method="POST" action="inserer_projection.php">
				<tr><th>jour<th>heure<th>version<th>nom du film<th>numéro de la salle<th>nom du cinéma</th></tr>
				<tr>
					<td><input type="date" name="jour" size="8" minlength="10" maxlength="10" placeholder="yyyy-mm-dd"></td>
					<td><input type="date" name="heure" size="8" minlength="5" maxlength="10" placeholder="hh:mm:ss"></td>
					<td><input type="radio" name="version" value="vo" minlength="1" placeholder="min 1" checked> vo
					    <input type="radio" name="version" value="vf" minlength="1" placeholder="min 1"> vf </td>
					<td><input type="text" name="nom_film" size="15" minlength="1" placeholder="min 1"></td>
					<td><input type="number" name="num_salle" size="2" minlength="1" placeholder="min 1"></td>
					<td><input type="text" name="nom_cinema" size="15" minlength="1" placeholder="min 1"></td>
					<td><input type="submit" value="insérer"></td>
				</tr>
			</form>
		</table>
	</body>
</html>

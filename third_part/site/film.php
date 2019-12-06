<?php
	
	print "<html><head><title>Film</title></head><body>";
	
	$num_film = $_GET['num_film'];
	
	$link = new mysqli("localhost", "Anonyme", "anonyme");
	if($link->connect_errno) {
		die ("Erreur de connexion : errno: " . $link->errno . " error: "  . $link->error);
	}
	
	$link->select_db('Projet') or die("Erreur de selection de la BD: " . $link->error);
	
	$query = "SELECT F.*, avg(N.note) AS moyenne, COUNT(N.num_client) AS nb_note FROM Note N, Film F WHERE F.num_film = $num_film and N.num_film = $num_film;";
	$result = $link->query($query) or die("erreur select");
	
	$query_suiv = "
		select F_suiv.num_film as num, F_suiv.nom as nom
		from Film F_prec, Film F_suiv, Suit S
		where F_prec.num_film = S.num_film_prec
		and F_suiv.num_film = S.num_film_suiv
		and S.num_film_prec = $num_film;
	";
	$film_suiv = $link->query($query_suiv) or die("erreur select");
	
	$query_prec = "
		select F_prec.num_film as num, F_prec.nom as nom
		from Film F_prec, Film F_suiv, Suit S
		where F_prec.num_film = S.num_film_prec
		and F_suiv.num_film = S.num_film_suiv
		and S.num_film_suiv = $num_film;
	";
	$film_prec = $link->query($query_prec) or die("erreur select");
	
	print "<table>";
	while ($tuple = mysqli_fetch_object($result)){ 
		print "	<tr>
						<td> $tuple->nom </td>
						</tr>
						<tr>
						<td>genre: $tuple->genre  -  origine: $tuple->origine  -  duree: $tuple->duree  -  version: $tuple->version_disponible</td>
						</tr>
						<tr>
						<td>Note : $tuple->moyenne - $tuple->nb_note votes</td>
						</tr>
						<tr>
						<td><a href=\"projection_film.php?num_film=$tuple->num_film\">Voir les projections</a></td>
						</tr>
						<tr>
						<td><a href=\"formulaire_noter.php?num_film=$tuple->num_film\">Noter le film</a></td>
						</tr>
		";
		$prec = mysqli_fetch_object($film_prec);
		if($prec) {
			print "
				<tr>
				<td>Film précédent : <a href=\"film.php?num_film=$prec->num\">$prec->nom</a></td>
				</tr>
			";
		}
		$suiv = mysqli_fetch_object($film_suiv);
		if($suiv) {
			print "
				<tr>
				<td>Film suivant : <a href=\"film.php?num_film=$suiv->num\">$suiv->nom</a></td>
				</tr>
			";
		}
	}
	print "</table>";
	
	$result->close();
	$link->close();
	
	print "</body></html>";
?>

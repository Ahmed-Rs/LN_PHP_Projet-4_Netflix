<?php

	session_start();

	require('src/log.php');


	if (isset($_SESSION['connect'])) {
		header('location: index.php');
		exit();
	}

	if (!empty($_POST['email']) && !empty($_POST['password'] && !empty($_POST['password_two']))) {

		require('src/connect.php');

		// VARIABLES
		$email 		  = htmlspecialchars($_POST['email']);
		$password 	  = htmlspecialchars($_POST['password']);
		$password_two = htmlspecialchars(($_POST['password_two']));


		// VERIFICATIONS

		// MOTS DE PASSE
		if ($password != $password_two) {
			
			header('location: inscription.php?error=1&message=Vos mots de passe ne sont pas identiques.');
			exit();
		}


		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			
			header('location: inscription.php?error=1&message=Votre adresse email est invalide.');
			exit();
		}


		$req = $db->prepare('SELECT COUNT(*)
							 AS x
							 FROM user
							 WHERE email = ?');

		$req->execute(array($email));

		while ($data = $req->fetch()) {
			
			if ($data['x'] != 0) {

				header('location: inscription.php?error=1&message=Cette adresse email est déjà utilisée.');
				exit();

			}

		}

		// CHIFFRAGE DU MOT DE PASSE
		$password = "gt5".sha1($password."659")."54";

		// HASH
		$secret = sha1(($password)."hg65".time())."kji5";
		$secret = sha1($secret).time();

		// ENVOI
		$req = $db->prepare('INSERT INTO user(email, password, secret)
					 VALUES (?, ?, ?)');

		$req->execute(array($email, $password, $secret));

		header('location: inscription.php?success=1');
		
	}







?>




<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="desig/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php 

				if (isset($_GET['error'])) {

					if (isset($_GET['message'])) {
						echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}

				} elseif (isset($_GET['success'])) {
					echo '<div class="alert success">Votre inscription a été prise en compte avec success!<a href="index.php"><br>Connectez-vous</a></div>';
				}

			?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email"/>
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>
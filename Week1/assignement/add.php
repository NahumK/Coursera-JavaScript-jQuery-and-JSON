<?php

require_once "pdo.php";
session_start();

if(!isset($_SESSION["name"]))
	die("ACCESS DENIED");

if(isset($_POST["cancel"]))
{
	// Redirect to index.php
	header("Location: index.php");
	return;
}

// Check to see if we have some POST data, if we do , store it in SESSION
if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) 
	&& isset($_POST["summary"]))
{
	$_SESSION["first_name"] = $_POST["first_name"];
	$_SESSION["last_name"] = $_POST["last_name"];
	$_SESSION["email"] = $_POST["email"];
	$_SESSION["headline"] = $_POST["headline"];
	$_SESSION["summary"] = $_POST["summary"];

	header("Location: add.php");
	return;
}

if(isset($_SESSION["first_name"]) && isset($_SESSION["last_name"]) && isset($_SESSION["email"]) && isset($_SESSION["headline"]) 
	&& isset($_SESSION["summary"]))
{
	$first_name = $_SESSION["first_name"];
	$last_name = $_SESSION["last_name"];
	$email = $_SESSION["email"];
	$headline = $_SESSION["headline"];
	$summary = $_SESSION["summary"];
	unset($_SESSION["first_name"]);
	unset($_SESSION["last_name"]);
	unset($_SESSION["email"]);
	unset($_SESSION["headline"]);
	unset($_SESSION["summary"]);

	if(strlen($first_name) < 1 || strlen($last_name) < 1 || strlen($email) < 1 || strlen($headline) < 1 || strlen($summary) < 1)
		$_SESSION["error"] = "All field are required";
	else if(strpos($email, "@") === false)
		$_SESSION["error"] = "Email address must contain @";
	else
	{
		$sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array(":uid" => $_SESSION["user_id"], ":fn" => $first_name, ":ln" => $last_name, ":em" => $email, 
			":he" => $headline, ":su" => $summary));
		$_SESSION["success"] = "Profile added";
		header("Location: index.php");
		return;
	}


}

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>Noumi Kouotou Nahum Asaph - Profile Add</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">
			<h1>Adding Profile for <?php echo(htmlentities($_SESSION["name"])); ?></h1>
			<?php
				if(isset($_SESSION["error"]))
				{
					echo('<p style = "color:red;">' . $_SESSION["error"] . "</p>\n");
					unset($_SESSION["error"]);
				}
			?>
			<form method="post">
				<p>
					First Name :
					<input type="text" name="first_name" size = "60">
				</p>
				<p>
					Last Name :
					<input type="text" name="last_name" size = "60">
				</p>
				<p>
					Email :
					<input type="text" name="email" size = "30">
				</p>
				<p>
					Headline :
					<input type="text" name="headline" size = "80">
				</p>
				<p>
					Summary :<br>
					<textarea name="summary" rows = "8" cols = "80"></textarea>
				</p>
				<input type="submit" value = "Add">
				<input type="submit" name="cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>
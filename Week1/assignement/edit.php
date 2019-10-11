<?php

require_once "pdo.php";

session_start();

if(!isset($_SESSION["name"]))
	die("ACCESS DENIED");

if(isset($_POST["cancel"]))
{
	header("Location: index.php");
	return;
}

if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) 
	&& isset($_POST["summary"]) && isset($_POST["profile_id"]))
{
	$_SESSION["first_name"] = $_POST["first_name"];
	$_SESSION["last_name"] = $_POST["last_name"];
	$_SESSION["email"] = $_POST["email"];
	$_SESSION["headline"] = $_POST["headline"];
	$_SESSION["summary"] = $_POST["summary"];
	$_SESSION["profile_id"] = $_POST["profile_id"];

	header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
	return;
}

if(isset($_SESSION["first_name"]) && isset($_SESSION["last_name"]) && isset($_SESSION["email"]) && isset($_SESSION["headline"]) 
	&& isset($_SESSION["summary"]))
{
	$firstName = $_SESSION["first_name"];
	$lastName = $_SESSION["last_name"];
	$email = $_SESSION["email"];
	$headline = $_SESSION["headline"];
	$summary = $_SESSION["summary"];
	$profile_id = $_SESSION["profile_id"];
	unset($_SESSION["first_name"]);
	unset($_SESSION["last_name"]);
	unset($_SESSION["email"]);
	unset($_SESSION["headline"]);
	unset($_SESSION["summary"]);
	unset($_SESSION["profile_id"]);

	if(strlen($firstName) < 1 || strlen($lastName) < 1 || strlen($email) < 1 || strlen($headline) < 1 || strlen($summary) < 1)
		$_SESSION["error"] = "All field are required";
	else if(strpos($email, "@") === false)
		$_SESSION["error"] = "Email address must contain @";
	else
	{
		$sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :sum WHERE profile_id = :profile_id";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array(":fn" => $firstName, ":ln" => $lastName, ":em" => $email, ":he" => $headline, ":sum" => $summary, ":profile_id" => $profile_id));
		$_SESSION["success"] = "Profile updated";
		header("Location: index.php");
		return;
	}
}

if(!isset($_GET["profile_id"]))
{
	$_SESSION["error"] = "Missing profile_id";
	header("Location: index.php");
	return;
}

if($_GET["profile_id"] == "")
{
	$_SESSION["error"] = "Could not load profile";
	header("Location: index.php");
	return;
}

$sql = "SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = " . $_GET["profile_id"];
$stmt = $pdo -> query($sql);

if($stmt -> rowCount() == 0)
{
	$_SESSION["error"] = "Could not load profile";
	header("Location: index.php");
	return;
}


$row = $stmt -> fetch(PDO::FETCH_ASSOC);

$fn = $row["first_name"];
$ln = $row["last_name"];
$em = $row["email"];
$he = $row["headline"];
$sum = $row["summary"];

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>Noumi Kouotou Nahum Asaph - Edit</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">
			<h1>Editing Profile for <?php echo(htmlentities($_SESSION["name"])); ?></h1>
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
					<input type="text" name="first_name" size = "60" value="<?php echo($fn) ?>">
				</p>
				<p>
					Last Name :
					<input type="text" name="last_name" size = "60" value="<?php echo($ln) ?>">
				</p>
				<p>
					Email :
					<input type="text" name="email" size = "30" value="<?php echo($em) ?>">
				</p>
				<p>
					Headline :
					<input type="text" name="headline" size = "80" value="<?php echo($he) ?>">
				</p>
				<p>
					Summary :<br>
					<textarea name="summary" rows = "8" cols = "80"><?php echo($sum) ?></textarea>
				</p>
				<input type="hidden" name="profile_id" value="<?php echo($_GET['profile_id']) ?>">
				<input type="submit" value = "Save">
				<input type="submit" name="cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>
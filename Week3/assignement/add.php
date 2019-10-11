<?php

require_once "pdo.php";
require_once "util.php";

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

	for($i = 1; $i <= 9; $i++)
	{
		$year = "year" . $i;
		$desc = "desc" . $i;
		if(isset($_POST[$year]) && isset($_POST[$desc]))
		{
			$_SESSION[$year] = $_POST[$year];
			$_SESSION[$desc] = $_POST[$desc];
		}
	}

	header("Location: add.php");
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
    unset($_SESSION["first_name"]);
    unset($_SESSION["last_name"]);
    unset($_SESSION["email"]);
    unset($_SESSION["headline"]);
    unset($_SESSION["summary"]);

	if(validateProfile($firstName, $lastName, $email, $headline, $summary) === true && validatePos() === true)
	{
		$sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(array(":uid" => $_SESSION["user_id"], ":fn" => $firstName, ":ln" => $lastName, ":em" => $email, 
			":he" => $headline, ":su" => $summary));

		// Insert the position entries
		$profileID = $pdo -> lastInsertId();
		$rank = 1;

		for($i = 1; $i <= 9; $i++)
		{
			$year = "year" . $i;
        	$desc = "desc" . $i;

        	if(isset($_SESSION[$year]) && isset($_SESSION[$desc]))
		    {
		        $yearVal = $_SESSION[$year];
		        $descVal = $_SESSION[$desc];
		        unset($_SESSION[$year]);
	         	unset($_SESSION[$desc]);

	         	$sql = "INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :descr)";
	         	$stmt = $pdo -> prepare($sql);
	         	$stmt -> execute(array(":pid" => $profileID, ":rank" => $rank, ":year" => $yearVal, ":descr" => $descVal));
		    }
		    $rank++;
		}

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
		<?php require_once "head.php" ?>
	</head>

	<body>
		<div class = "container">
			<h1>Adding Profile for <?php echo(htmlentities($_SESSION["name"])); ?></h1>
			<?php flashmessages(); ?>
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
				<p>
					Position:
					<input type = "submit" id = "addPos" value="+">
					<div id="position_fields">
					</div>
				</p>
				<input type="submit" value = "Add">
				<input type="submit" name="cancel" value = "Cancel">
			</form>
			<script type="text/javascript">

				countPos = 0;

				$(document).ready(
					function()
					{
						window.console && console.log("Document ready called");
						$("#addPos").click(
							function(event)
							{
								event.preventDefault();
								if(countPos >= 9)
								{
									alert("Maximum of nine position entries exceeded");
									return;
								}

								countPos++;
								window.console && console.log("Adding position" + countPos);

								$("#position_fields").append(
									'<div id="position' + countPos + '">  \
										<p>  \
										    Year :  \
										    <input type = "text" name="year' + countPos + '" value = "" /> \
										    <input type="button" value="-"  \
										        onclick="$(\'#position' + countPos + '\').remove(); return false;">  \
										</p>  \
										<textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>  \
									</div>'
								);
								
							}
						);
					}
				);
			</script>
		</div>
	</body>

</html>
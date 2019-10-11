<?php

//util.php
function flashMessages()
{
    if(isset($_SESSION["success"]))
    {
    	echo('<p style="color:green;">' . $_SESSION["success"] . "</p>\n");
    	unset($_SESSION["success"]);
    }
    if(isset($_SESSION["error"]))
    {
    	echo('<p style="color:red;">' . $_SESSION["error"] . "</p>\n");
    	unset($_SESSION["error"]);
    }
}


function validateProfile($firstName, $lastName, $email, $headline, $summary)
{
    if(strlen($firstName) < 1 || strlen($lastName) < 1 || strlen($email) < 1 || strlen($headline) < 1 || strlen($summary) < 1)
    {
        $_SESSION["error"] = "All field are required";
        return false;
    }
    if(strpos($email, "@") === false)
    {
        $_SESSION["error"] = "Email address must contain @";
        return false;
    }

    return true;
}

function validatePos()
{
    for($i = 1; $i <= 9; $i++)
    {
        $year = "year" . $i;
        $desc = "desc" . $i;

        if(isset($_SESSION[$year]) && isset($_SESSION[$desc]))
        {
            $yearVal = $_SESSION[$year];
            $descVal = $_SESSION[$desc];

            if(strlen($yearVal) == 0 || strlen($descVal) == 0)
            {
                $_SESSION["error"] = "All field are required";
                return false;
            }

            if(!is_numeric($yearVal))
            {
                $_SESSION["error"] = "Position year must be numeric";
                return false;
            }

        }
    }

    return true;
}

function loadPos($pdo, $profileID)
{
    $sql = "SELECT * FROM position WHERE profile_id = :prof ORDER BY rank";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(":prof" => $profileID));
    $positions = array();

    while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
        $positions[] = $row;

    return $positions;
}

?>
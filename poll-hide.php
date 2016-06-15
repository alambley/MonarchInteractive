<?php

$conn = new mysqli('localhost', 'root', 'blackteam', 'blackteam');

if (mysqli_connect_error()) {
    echo "Datbase connection error<br>Try again later";
}

$pollName = $_POST['pollid'];

$pollName = substr($pollName,5);

$setHidden = "UPDATE Questions SET Questions.isHidden = 1 WHERE Questions.Question_ID = '$pollName';";
        


if($conn->query($setHidden) === TRUE)
{
    
}

?>
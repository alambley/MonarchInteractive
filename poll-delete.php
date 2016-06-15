<?php

$conn = new mysqli('localhost', 'root', 'blackteam', 'blackteam');

if (mysqli_connect_error()) {
    echo "Datbase connection error<br>Try again later";
}

$pollid = $_POST['pollid'];

$pollid = substr($pollid,5);


$deleteVotes = "DELETE FROM Votes WHERE Votes.Question = '$pollid'";
$deleteAnswers = "DELETE FROM Answers WHERE Answers.Answer_question = '$pollid'";
$deleteQuestion = "DELETE FROM Questions WHERE Questions.Question_ID = '$pollid'";

if($conn->query($deleteVotes) === TRUE)
{
    if($conn->query($deleteAnswers) === TRUE)
    {
        if($conn->query($deleteQuestion) === TRUE)
        {
 
        }
    }
}

?>
<?php 

$conn = new mysqli('localhost', 'root', 'blackteam', 'blackteam');

if (mysqli_connect_error()) {
    echo "Datbase connection error<br>Try again later";
}


$cDate = date("Y-m-d");

$currenturl = $_SERVER['HTTP_REFERER'];

//only need question text for hidden polls
$sql = "SELECT Questions.Question_ID, Questions.Question_text FROM Questions 
WHERE (Questions.Question_article = '$currenturl' OR Questions.Question_article = 'GLOBAL') AND Questions.isHidden = 1";
/*
$sql = "SELECT Questions.Question_ID, Questions.Question_text, Answers.Answer_text, Answers.Vote_count FROM Answers, Questions 
WHERE Answers.Answer_question = Questions.Question_ID 
AND (Questions.Question_article = '$currenturl' OR Questions.Question_article = 'GLOBAL') AND Questions.isHidden = 1";*/

$result = $conn->query($sql);

if($result->num_rows > 0)
{
while($row = $result->fetch_assoc())
{
    echo "<div id='poll-display-results'>";
    echo "<div id='poll-title' name='".$row["Question_ID"]. "'>";
    echo $row["Question_text"] . "</div>";
    echo "<button id='unhide-poll' onclick='unhidePoll(" . $row['Question_ID'] . ")' class='w3-btn-block w3-light-grey'>Unhide this poll</button></div>";
                                   
    echo "<br></div>";
}
}
else
{
	echo "There are no hidden polls for this page";
}
?>
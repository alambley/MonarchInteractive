<?php 

$conn = new mysqli('localhost', 'root', 'blackteam', "blackteam");

if (mysqli_connect_errno()) {
    echo "failed";
}
//echo "connected successfully";
$previousPage = $_SERVER['HTTP_REFERER'];

$numEntries = $_POST["numChoices"];
$numEntries = $numEntries + 1;
$question = $_POST["pollq"];
$global = $_POST["global"];
$expDate = $_POST["expDate"];


$choices = array();

for ($x = 1; $x < $numEntries; $x++) {
    $s = "choice".$x;
    $t = $_POST[$s];
    $choices[$x - 1] = $t;

}

if ($global == "true") {
    $previousPage = "GLOBAL";
}

$sql = "INSERT INTO Questions (Question_text,Question_article, Expiration_date) VALUES ('$question','$previousPage', '$expDate')";
$last_id;
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;

} else {
    echo "Error:   ".$sql."<br>".$conn->error;
}
foreach($choices as $x) {
    $answer_sql = "INSERT INTO Answers (Answer_question, Answer_text) VALUES('$last_id','$x')";
    if ($conn->query($answer_sql) === TRUE) {

    }
}

?>
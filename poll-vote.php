<?php

$conn = new mysqli('localhost', 'root', 'blackteam', 'blackteam');

if (mysqli_connect_error()) {
    echo "Datbase connection error<br>Try again later";
}

$pollName = $_POST['q-title'];

$pollChoice = $_POST['choice'];

//$pollName = str_replace("-"," ", $pollName);
$pollName = substr($pollName,5);
$pollVoter = $_POST['current-user'];

$testVar = strpos($pollVoter,"GUEST");
//test if this voter is a guest
//if so, need to create a guest user so sql will work
if((strpos($pollVoter,"GUEST")) !== false)
{
    //first check if guest user already exists
    $userCheck = "SELECT * FROM User WHERE Username = '$pollVoter'";
    
    $userResult = $conn->query($userCheck);
    
    
    if($userResult->num_rows > 0)
    {
        //user already exists
        //do nothing
        
    }
    //need to create a guest user with IP address here
    else 
    {
        
        $createGuest = "INSERT INTO User Set User.Username = '$pollVoter', User.Password = 'blackteam';";
        if($conn->query($createGuest) === TRUE)
        {
            echo "all good";
        }
        else {
            echo "all bad";
        }
    }
}


$sql = "INSERT INTO Votes Set Votes.User = (SELECT User.User_ID FROM User WHERE User.Username = '$pollVoter'), 
Votes.Question = '$pollName', 
Votes.Answer = (SELECT Answers.Answer_ID FROM Answers WHERE Answers.Answer_text = '$pollChoice')";

if($conn->query($sql) === TRUE)
{
    $sql2 = "UPDATE Answers Set Answers.Vote_count = (Answers.Vote_count + 1) WHERE Answers.Answer_text = '$pollChoice'"; 
    if($conn->query($sql2) === TRUE)
    {
        
        echo "You vote has been recorded</div>";
        
    }
    else {
        echo "error vote answer ";
    }
}
else {
    echo "<br>error query dead<br>";
}
?>
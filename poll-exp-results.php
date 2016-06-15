<?php 
class Answer {
    //text for this answer
    public $answerText = '';
    //votes for this answer only
    public $voteCount = 0;
}
class Poll {
    public $pollTitle = '';
    public $pollChoices = array();
    public $userVote = '';
}

$conn = new mysqli('localhost', 'root', 'blackteam', 'blackteam');

if (mysqli_connect_error()) {
    echo "Datbase connection error<br>Try again later";
}


$cDate = date("Y-m-d");

$currenturl = $_SERVER['HTTP_REFERER'];
$currentuser = $_POST["user"];

$sql = "SELECT Questions.Question_ID, Questions.Question_text, Answers.Answer_text, Answers.Vote_count FROM Answers, Questions 
WHERE Answers.Answer_question = Questions.Question_ID 
AND (Questions.Question_article = '$currenturl' OR Questions.Question_article = 'GLOBAL') AND '$cDate' > Questions.Expiration_date";

$result = $conn->query($sql);

//sql query that returns all polls this user has voted in
$sql2 = "SELECT Questions.Question_ID, Answers.Answer_text FROM Questions,Answers, Votes WHERE (Questions.Question_article='$currenturl' OR Questions.Question_article='GLOBAL')
AND Votes.Question = Questions.Question_ID AND Answers.Answer_ID = Votes.Answer AND Votes.User = 
(SELECT User.User_ID FROM User WHERE User.Username = '$currentuser')";
$userVoteResult = $conn->query($sql2);

$userAnswers = array();


//build associative array of user answers and corresponding question id
while ($p = $userVoteResult->fetch_assoc()) {
    $userAnswers[$p["Question_ID"]] = $p["Answer_text"];

}

//build array of Polls
$allPolls = array();
$counter = 0;


//builds array of Polls
if ($result->num_rows > 0) {
    //holds poll answers for a single poll
    $a = array();
    //poll title
    $t = "";
    //user's choice 
    $u = "";
    while ($row = $result->fetch_assoc()) {
        $currentQuestion = $row["Question_text"];
        if ($currentQuestion == $t || $t == "") {
            //add this answer to array for this poll
            $response = new Answer();
            $response->answerText = $row["Answer_text"];
            $response->voteCount = $row["Vote_count"];
            array_push($a, $response);
            //set title of this question
            $t = $currentQuestion;
            //if user choice has not been set and one exists for this poll
            if ($u == "") {
                //if the question id exists set u to the user's vote
                if (array_key_exists($row["Question_ID"], $userAnswers)); {

                    $u = $userAnswers[$row["Question_ID"]];

                }

            }


        }
        //otherwise, we have started a new poll
        else {

            //add the poll to all polls

            $p = new Poll();
            $p->pollTitle = $t;

            $p->pollChoices = $a;

            $p->userVote = $u;

            $allPolls[] = $p;

            $total = count($allPolls);

            //empty array and re-create
            unset($a);
            $a = array();
            //push current answer
            $response = new Answer();
            $response->answerText = $row["Answer_text"];
            $response->voteCount = $row["Vote_count"];

            array_push($a, $response);
            $t = $currentQuestion;
            $u = "";

        }


    }
    //last poll still needs to be created

    $p = new Poll();
    $p->pollTitle = $t;

    $p->pollChoices = $a;

    $p->userVote = $u;

    $allPolls[] = $p;

    $total = count($allPolls);

} else {
    echo "No polls exist for this page";
}


//if there are polls to output
if (count($allPolls) > 0) {

    for ($x = 0; $x < count($allPolls); $x++) 
    {

        //if a user vote exists
        //if ($allPolls[$x]->userVote != "") {
            echo "<div id='poll-display-results'>";
            echo "<div id='poll-title'>".$allPolls[$x]->pollTitle."</div>";

            echo "<div id='poll-entry'>";
            for ($e = 0; $e < count($allPolls[$x]->pollChoices); $e++) 
            {
                if ($allPolls[$x]->pollChoices[$e]->answerText == $allPolls[$x]->userVote) 
                {
                    echo "<div id='poll-user-vote'>";
                    echo $allPolls[$x]->pollChoices[$e]->answerText." with ".$allPolls[$x]->pollChoices[$e]->voteCount." votes<br></div>";
                } else 
                {
                    echo $allPolls[$x]->pollChoices[$e]->answerText." with ".$allPolls[$x]->pollChoices[$e]->voteCount." votes<br>";
                }

           }


            echo "</div></div><br>";
        }
        
    }


?>
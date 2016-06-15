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
    public $pollId = 0;
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
AND (Questions.Question_article = '$currenturl' OR Questions.Question_article = 'GLOBAL') AND '$cDate' <= Questions.Expiration_date
AND Questions.isHidden = 0";

$result = $conn->query($sql);

//sql query that returns all polls this user has voted in
$sql2 = "SELECT Questions.Question_ID, Answers.Answer_text FROM Questions,Answers, Votes WHERE (Questions.Question_article='$currenturl' OR Questions.Question_article='GLOBAL')
AND Votes.Question = Questions.Question_ID AND Answers.Answer_ID = Votes.Answer AND Votes.User = 
(SELECT User.User_ID FROM User WHERE User.Username = '$currentuser')";
$userVoteResult = $conn->query($sql2);

$userAnswers = array();

//if current user is an admin, display the poll creation button

$isAdminSql = "SELECT Is_admin FROM User WHERE User.Username = '$currentuser';";
$adminResult= $conn->query($isAdminSql);
$adminRow = $adminResult->fetch_assoc();
if($adminRow["Is_admin"] == 1)
{
    
    echo "<div id='poll-button-area'>";
    echo "<button id='create-poll-button' onclick='document.getElementById(\"poll-create\").style.display=\"block\"' class='w3-btn-floating w3-blue'>+</button>";
    echo "Create a poll<br></div>";
    
    
}

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
            $pId = $row["Question_ID"];
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
            $p->pollId = $pId;
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
    $p->pollId= $pId;
    $p->pollChoices = $a;

    $p->userVote = $u;

    $allPolls[] = $p;

    $total = count($allPolls);

} else {
    echo "No polls exist for this page";
}


//if there are polls to output
if (count($allPolls) > 0) {

    for ($x = 0; $x < count($allPolls); $x++) {

        //if a user vote exists
        if ($allPolls[$x]->userVote != "") {
            echo "<div id='poll-display-results'>";
            echo "<div id='poll-title'>".$allPolls[$x]->pollTitle."</div>";

            echo "<div id='poll-entry'>";
            for ($e = 0; $e < count($allPolls[$x]->pollChoices); $e++) {
                if ($allPolls[$x]->pollChoices[$e]->answerText == $allPolls[$x]->userVote) {
                    echo "<div id='poll-user-vote'>";
                    echo $allPolls[$x]->pollChoices[$e]->answerText." with ".$allPolls[$x]->pollChoices[$e]->voteCount." votes<br></div>";
                } else {
                    echo $allPolls[$x]->pollChoices[$e]->answerText." with ".$allPolls[$x]->pollChoices[$e]->voteCount." votes<br>";
                }

            }
            if($adminRow["Is_admin"] == 1)
            {
    
                echo "<div id=poll-delete>";
            
            echo "<div id='poll-button-area'>";
            echo "<button id='delete-poll-button' onclick='deletePoll(\"poll-" . $allPolls[$x]->pollId . "\")' class='w3-btn-block w3-light-grey'>Delete this poll</button></div>";
            
            
            
            echo "<button id='hide-poll-button' onclick='hidePoll(\"poll-" . $allPolls[$x]->pollId . "\")' class='w3-btn-block w3-light-grey'>Hide this poll</button></div>";
            
            
            echo "</div>";
            
    
    
            }
            echo "</div></div><br>";
            
        }
        //otherwise output as votable
        else {

            echo "<div id='poll-display-results'>";
            echo "<div id='poll-title'>".$allPolls[$x]->pollTitle."</div>";

            echo "<div id='poll-entry'>";
            echo "<form id='voting-form'>";
            $pollName = str_replace(" ", "-", $allPolls[$x]->pollTitle);

            for ($e = 0; $e < count($allPolls[$x]->pollChoices); $e++) {


                echo "<input class='w3-radio' type='radio' name='poll-";
                echo $allPolls[$x]->pollId;
                echo "' value='";
                echo $allPolls[$x]->pollChoices[$e]->answerText;
                echo "'>";
                echo "<label class='w3-validate'>".$allPolls[$x]->pollChoices[$e]->answerText."</label><br>";

            }

            echo "<br><input class='w3-btn w3-light-grey w3-btn-block' type='button' value='Submit your vote' onClick='castVote(\"poll-";
            echo $allPolls[$x]->pollId."\");'>";
            echo "</form>";
            
              if($adminRow["Is_admin"] == 1)
            {
    
                echo "<div id=poll-delete>";
            
            echo "<div id='poll-button-area'>";
            echo "<button id='delete-poll-button' onclick='deletePoll(\"poll-" . $allPolls[$x]->pollId . "\")' class='w3-btn-block w3-light-grey'>Delete this poll</button></div>";
            
            
            
            echo "<button id='hide-poll-button' onclick='hidePoll(\"poll-" . $allPolls[$x]->pollId . "\")' class='w3-btn-block w3-light-grey'>Hide this poll</button></div>";
            
            
            echo "</div>";
           
    
    
            }
   
             echo "</div></div><br>";
           
        }
    }
     if($adminRow["Is_admin"] == 1)
            {
                echo "<div id='show-hidden'>";
                echo '<input type="button" name="show-hidden-button" id="show-hidden-button" value="Show hidden polls"  onclick="showHiddenPolls()">';
                echo "</div>";
            }
}

?>
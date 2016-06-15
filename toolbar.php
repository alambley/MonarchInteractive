<script src="cookie.js"></script>
<script src="poll.js"></script>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script> 
$(function(){
	$("#viewercount").load("viewercount.php");
	$("#sharing").load("sharing.html");
});
</script> 
<script>
    $(function() {
        $("#datepicker").datepicker();
    });
</script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php 
//code for loading comments
require_once('db.php');
$conn = connectDB();
$items = array();
$currentpage = $_SERVER['SCRIPT_FILENAME'];
$stmt = $conn->query('SELECT Comment_ID,Commenter_name,Comment_text,hidden_user,hidden_admin FROM Comments WHERE Article_title="'.$currentpage.'";');
if($stmt->rowCount() > 0){
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $stmt->fetch()) {
	    $items[] = array($row['Comment_ID'],$row['Commenter_name'],$row['Comment_text'],$row['hidden_user'],$row['hidden_admin']);	    
	}
	$js_array = json_encode($items);
	echo "<script>var getComments = ". $js_array . ";\n</script>";
}else{
	echo "<script>var getComments = {}</script>";
}
//code for loading admin list
$items = array();
$stmt = $conn->query('SELECT Username FROM User WHERE Is_admin=1;');
if($stmt->rowCount() > 0){
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $stmt->fetch()) {
	    $items[] = $row['Username'];	    
	}
	$js_array = json_encode($items);
	echo "<script>var getAdmin = ". $js_array . ";\n</script>";
}else{
	echo "<script>var getAdmin = {}</script>";
}
//code for loading banned list
$items = array();
$stmt = $conn->query('SELECT Username FROM User WHERE Comment_ban=1;');
if($stmt->rowCount() > 0){
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $stmt->fetch()) {
	    $items[] = $row['Username'];	    
	}
	$js_array = json_encode($items);
	echo "<script>var getBanned = ". $js_array . ";\n</script>";
}else{
	echo "<script>var getBanned = {}</script>";
}
//code for loading bookmarks
$items = array();
$usernametemp = $_COOKIE['username'];
if($usernametemp  == ""){

}else{
	$stmt = $conn->query('SELECT Bookmark_article,Bookmark_Title FROM Bookmarks WHERE username="'.$usernametemp.'";');
	if($stmt->rowCount() > 0){
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($row = $stmt->fetch()) {
	    $items[] = array($row['Bookmark_article'],$row['Bookmark_Title']);	    
	}
	$js_array = json_encode($items);
	echo "<script>var getBookmarks = ". $js_array . ";\n</script>";
}else{
	echo "<script>var getBookmarks = {}</script>";
}
}
?>

<script type="text/javascript">
 function showhide(id) {
 	var divs = ["polls","bookmarks","comment","account"];
 	delete divs[divs.indexOf(id)];
 	for(var x in divs){
 		var e = document.getElementById(divs[x]);
 		e.style.display = 'none';
 	}
 	var e = document.getElementById(id);
    e.style.display = (e.style.display == 'block') ? 'none' : 'block';
}

function writeToElement(element,message){
	document.getElementById(element).innerHTML += message;
}

</script>

<html>

<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="http://w3schools.com/lib/w3.css">

<div id="toolbar" style="padding: 0px 0 0 0px; height: 50px; width: 90%;position:fixed;left:5%;bottom:30px;background-color: rgb(64,64,64);">
	
		<ul id="navbar">
		<li><span style="color: white; cursor: pointer" onclick="showhide('polls');showPollVotes();">Polls</span></li>
		<li><span style="color: white; cursor: pointer"; onclick="showhide('bookmarks')">Bookmarks</span></li>
		<li><div style="color: white" id = "viewercount"></div></li>
		<li><div style="color: transparent" id = "sharing"></div></li>
		<li><span style="color: white;cursor: pointer;" onclick="showhide('comment')">Comments</span></li>
		<li><span style="color: white;cursor: pointer;" onclick="showhide('account')">
			<script type="text/javascript">
				var username = getCookie("username");
				if(username != ""){
					document.write(username);
				}else{
					document.write("Account");
				}
			</script>
		</span></li>
		</ul>

</div>

<div id="comment" style="padding: 0px 0 0 0px; height: 400px; width: 40%;position:fixed;left:55%;bottom:80px;background-color: white;display:none;border-style:solid;border-color:rgb(64,64,64);">
		<script type="text/javascript">
			if (comments){
				var html = ""
				html += '<div id="commentBody" style="overflow-y: scroll; height:71%; margin-left:10px; margin-top:10px">'
				for (index = 0; index < getComments.length; ++index) {	//for every comment
					if(checkString(getCookie('username'),getComments[index][1])){	// if it is your comment
						html += '<form action = "<?php $_PHP_SELF ?>" method = "POST">'

						if(getComments[index][3] == 0){	//if the comment isn't hidden
							html += '<input name="userhide" title="Hide" type="image" src="toolbarresources/hide.png" style="margin-left: 5px;margin-right: 5px;" value="'+ getComments[index][0] +'"></button>'
						}else{	//if the comment is hidden
							html += '<input name="userunhide" title="Show" type="image" src="toolbarresources/show.png" style="margin-left: 5px;margin-right: 5px;" value="'+ getComments[index][0] +'"></button>'
						}
						html += getComments[index][1] + ':' + getComments[index][2] + '<br>'
						html += '</form>'
					}else if(!checkString(getCookie('username'),getComments[index][1]) && include(getAdmin,getCookie('username'))){	//if it is not your comment, and youre an admin
						html += '<form action = "<?php $_PHP_SELF ?>" method = "POST">'
						if(!include(getBanned,getComments[index][1])){		//if commenter isnt banned
							html += '<input name="ban" title="Ban" type="image" src="toolbarresources/ban.png" style="width: 22px;margin-left: 5px;" value="'+ getComments[index][1] +'"></button>'
						}else{		//if commenter is banned
							html += '<input name="unban" title="Unban" type="image" src="toolbarresources/unban.png" style="width: 22px;margin-left: 5px;" value="'+ getComments[index][1] +'"></button>'
						}
						if(getComments[index][4] == 0){	//if the comment isn't hidden
							html += '<input type="image" title="Hide" name="adminhide" src="toolbarresources/hide.png" style="margin-left: 5px;" value="'+ getComments[index][0] +'"></button>'
						}else{	//if the comment is hidden
							html += '<input type="image" title="Show" name="adminunhide" src="toolbarresources/show.png" style="margin-left: 5px;" value="'+ getComments[index][0] +'"></button>'
						}
						html += '<input name="delete" title="Delete" type="image" src="toolbarresources/delete.png" style="width:22px;margin-left: 5px;margin-right: 5px;" value="'+ getComments[index][0] +'"></button>'
						html += getComments[index][1] + ':' + getComments[index][2] + '<br>'
						html += '</form>'
					}
					else{	//if it is not your comment and youre not an admin
						if(getComments[index][3] == 0 && getComments[index][4] == 0){	//if the comment is not hidden by user or admin
							html += getComments[index][1] + ':' + getComments[index][2] + '<br>'
						}
					}				
				}
				html += '</div>'
				html += '<div id="commentSubmit"; height:25%; style="height:70%;margin-left:10px">'
				if(getCookie('username') == ""){
					html += 'You must be logged in to comment.<br>'
				}else{
					if(!include(getBanned,getCookie('username'))){
						html += '<form action = "<?php $_PHP_SELF ?>" method = "POST">'
						html += '<textarea name="comment" id="commentInput" cols=90 rows=4 style="overflow:auto;resize: none;width: 85%;">'
						html += '</textarea>'
						html += '<button class="commentbutton"type="submit">Post</button>'					
						html += '</form>'
					}else{
						html += 'You have been banned from commenting.<br>'
					}				
				}
				html += '</div>'
				document.write(html)
			}else{

				html = '<div style="margin-left: 20px;margin-top: 20px;">'
				html += 'This page has no comments associated with it.'
				html += '</div>'
				document.write(html)
			}
		</script>
</div>

<!-- INCLUDE THIS FOR POLLS TO WORK-->

<div id ="poll-notifications" style="display:none">
	
	
</div>

<!--- polls results go here --->
<div id="polls" style="display:none;">
    <!-- Only display poll creation button if the user is an admin-->


    <!--placeholder for poll display area-->
    <div id="poll-display">

    </div>
<div id="show-expired">
    <input type="button" name="show-all-button" id="show-all-button" value="Show expired polls" style="margin-bottom:20px;" onclick="showExpPolls()">
</div>

<!-- End of polls -->

</div>

<div id="poll-create" class="w3-modal">
    <!--- poll modal window --->
    <div id="creation-window" class="w3-modal-content w3-card-8 w3-animate-zoom" style="max-width:600px;z-index:999999999999999;">

        <!--- poll setup --->
        <div class="w3-container">
            <div class="w3-section">
                <label<b> Poll text</b>
                    </label>
                    <form id="creation-form">
                        <input class="w3-input w3-border w3-margin-bottom" id="poll-question-text" type="text" placeholder="What do you want to ask?" name="poll-text">


                        <table class="w3-table">
                            <tr>
                                <td>
                                    <div id="dynamic-input">
                                        <!--- Always start out with two inputs -->
                                        <input type="w3-input w3-border w3-margin-bottom" placeholder="Choice text" name="poll-inputs[]">
                                        <br>
                                        <input type="w3-input w3-border w3-margin-bottom" placeholder="Choice text" name="poll-inputs[]">
                                    </div>
                                    <!-- button to add an input box -->
                                    <input class="w3-btn w3-light-grey" type="button" value="Add a choice" onClick="addInput('dynamic-input');">
                                    <br>
                                    <br>
                                    <td>
                                        <td>
                                            <!-- can be empty, never expire-->
                                            Poll expiration date (not required)
                                            <br>
                                            <input type="text" id="datepicker">
                            </tr>
                        </table>
                        <!--if checked, poll is global and will appear on all pages -->
                        <input id="global-checkbox" class="w3-check" type="checkbox">
                        <label class="w3-validate">Show this poll on all pages?</label>
                        <br>
                        <br>

                        <input class="w3-btn w3-light-grey w3-btn-block" type="button" value="Submit poll" onClick="createPoll()">
                    </form>
            </div>
        </div>

        <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
            <button onclick="document.getElementById('poll-create').style.display='none'" type="button" class="w3-btn w3-red">Cancel</button>
        </div>
    </div>
</div>

<div id="bookmarks" style="padding: 0px 0 0 0px; height: 400px; width: 40%;position:fixed;left:7%;bottom:80px;background-color: white;display:none;border-style:solid;border-color:rgb(64,64,64);">
	<p>
		<script type="text/javascript">
			var getLogin = getCookie("username");
			if(getLogin == ""){
				html = '<div style="margin-left: 25px;">'
				html += 'You must be logged in to access bookmarks.';
				html += '</div>'
				document.write(html);
			}else{
				html = '<form action = "<?php $_PHP_SELF ?>" style="margin-left:25px;" method = "POST">'
				html += '<button name="addBookmark" value="' + article + '" type="submit">Bookmark this page</button>'
				html += '</form>'
				html += '<br>'
				html += '<div id="bookmarkBody" style="overflow-y: scroll; height:80%;margin-left:25px;">'
				for (index = 0; index < getBookmarks.length; ++index){
					html += '<form action = "<?php $_PHP_SELF ?>" method = "POST">'
					html += '<input name="deleteBookmark" title="Delete" type="image" src="toolbarresources/delete.png" style="margin-right: 5px;" value="'+ getBookmarks[index][0] +'"></button>'
					html += '<a href="' + getBookmarks[index][0] + '">' + getBookmarks[index][1] + '</a><br>'
					html += '</form>'
				}
				html += '</div>'
				document.write(html);
			}			
		</script>
	</p>
</div>


<div id="account" style="padding: 0px 0 0 0px; height: 400px; width: 32%;position:fixed;left:63%;bottom:80px;background-color:white;display:none;border-style:solid;border-color:rgb(64,64,64);overflow:scroll;">
	<p>
		<script type="text/javascript">
			var getLogin = getCookie("username");
			if(getLogin == ""){
				html = '<span style="float:left;margin-left:15px;">'
				html += '<form action = "<?php $_PHP_SELF ?>" style="float:left;margin-left: 15px;" method = "POST">'
				html +='<p>Login</p>'
				html +='<p>Username</p>'
				html +='<input type = "text" name = "desiredUsername" /><br>'
				html +='<p>Password</p>'
				html +='<input type = "password" name = "desiredPassword"/><br>'
				html += '<button type="submit" style="margin-top: 25px;">Login</button>'	
        		html += '</form>'
        		html += '</span>'
        		html += '<span style="float:left;margin-left:15px;">'
        		html += '<form action = "<?php $_PHP_SELF ?>" style="margin-left:15px;" method = "POST">'
				html +='<p>Register</p>'
				html +='<p>Username</p>'
				html +='<input type = "text" name = "regUsername" /><br>'
				html +='<p>Password</p>'
				html +='<input type = "password" name = "regPass"/><br>'
				html +='<p>Verify Password</p>'
				html +='<input type = "password" name = "regPassV"/><br>'
				html += '<button type="submit" style="margin-top: 25px;">Register</button>'	
        		html += '</form>'
        		html += '</span>'
				document.write(html);
			}else{
				html = '<span style="margin-left:50px;">'
				html += '<form action = "<?php $_PHP_SELF ?>" style="margin-left:50px;" method = "POST">'
				html += '<button name="logout" value="yes" type="submit">Logout</button>'
				html += '</form>'
				html += '<form action = "<?php $_PHP_SELF ?>" style="margin-left:50px;" style="clear: right;margin-right: 20px; " method = "POST">'
				html +='<p>Change Password</p>'
				html +='<p>Old Password</p>'
				html +='<input type = "password" name = "passChangeOld" /><br>'
				html +='<p>New Password</p>'
				html +='<input type = "password" name = "passChangeNew"/><br>'
				html +='<p>Verify New Password</p>'
				html +='<input type = "password" name = "passChangeNewV"/><br>'
				html += '<button type="submit" style="margin-top: 20px;">Change Password</button>'
        		html += '</form>'
        		html += '</span>'
				document.write(html);
			}
		</script>
	</p>
</div>
<iframe name="frame" style="display:none;"></iframe>
</html>

<?php 

require_once('db.php');
$conn = connectDB();

session_start();
if($_POST == $_SESSION['oldPOST']) $_POST = array(); else $_SESSION['oldPOST'] = $_POST;

$banned_words = ["fuck","shit","bitch","ass"];

if($_POST["comment"]){
	$found = False;
	$username = $_COOKIE[username];
	$comment = $_POST["comment"];
	$url = $_SERVER['SCRIPT_FILENAME'];

	for($i = 0; $i < sizeof($banned_words); $i++){
		if (strpos(strtolower($comment), $banned_words[$i]) !== false){
			$found = True;
			break;
		}
	}
	if(!$found){
		$stmt = $conn->query('INSERT INTO Comments(Comment_text,Commenter_name,Article_title)VALUES("'.$comment.'","'.$username.'","'.$url.'");');	
		echo('<script>window.location=window.location;</script>');	
	}else{
		echo('<script>alert("Your comment contained a banned word, and was not posted.")</script>');
		echo('<script>window.location=window.location;</script>');	
	}


	exit;
}

if($_POST["userhide"]){
	$stmt = $conn->query('UPDATE Comments SET hidden_user=1 WHERE Comment_ID="'.$_POST["userhide"].'"');
	echo('<script>window.location=window.location;</script>');
}

if($_POST["adminhide"]){
	$stmt = $conn->query('UPDATE Comments SET hidden_admin=1 WHERE Comment_ID="'.$_POST["adminhide"].'"');
	echo('<script>window.location=window.location;</script>');
}

if($_POST["userunhide"]){
	$stmt = $conn->query('UPDATE Comments SET hidden_user=0 WHERE Comment_ID="'.$_POST["userunhide"].'"');
	echo('<script>window.location=window.location;</script>');
}

if($_POST["adminunhide"]){
	$stmt = $conn->query('UPDATE Comments SET hidden_admin=0 WHERE Comment_ID="'.$_POST["adminunhide"].'"');
	echo('<script>window.location=window.location;</script>');
}

if($_POST["delete"]){
	$stmt = $conn->query('DELETE FROM Comments WHERE Comment_ID="'.$_POST["delete"].'"');
	echo('<script>window.location=window.location;</script>');
	exit;
}

if($_POST["ban"]){
	$stmt = $conn->query('UPDATE User SET Comment_ban=1 WHERE Username="'.$_POST["ban"].'"');
	echo('<script>window.location=window.location;</script>');
	exit;
}

if($_POST["unban"]){
	$stmt = $conn->query('UPDATE User SET Comment_ban=0 WHERE Username="'.$_POST["unban"].'"');
	echo('<script>window.location=window.location;</script>');
	exit;
}

if( $_POST["desiredUsername"] || $_POST["desiredPassword"] ) {       
		$con = mysqli_connect('127.0.0.1','root','blackteam','blackteam');
		$sql = "SELECT Password FROM User WHERE Username = '".$_POST["desiredUsername"]."'";
		echo '<script>console.log("'.$sql.'")</script>';
		$res = mysqli_query($con, $sql);
		$hash = $res->fetch_object();
		$hash = $hash->Password;
		if(password_verify($_POST["desiredPassword"], $hash)){
			echo '<script>document.cookie="username='.$_POST["desiredUsername"].';path=/";</script>';
          	echo('<script>window.location=window.location;</script>');
		}else{
			$errorMsg = "Username/Password mismatch<br/>";
			echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
		}
        exit;
     }

if($_POST["logout"]){
	echo '<script>document.cookie="username=; expires=Thu, 18 Dec 2013 12:00:00 UTC; path=/";</script>';
    echo('<script>window.location=window.location;</script>');
    exit;
}

if( $_POST["regUsername"] || $_POST["regPass"] || $_POST["regPassV"]){
	$con = mysqli_connect('127.0.0.1','root','blackteam','blackteam');
	$sql = "SELECT * FROM User WHERE Username = '".$_POST["regUsername"]."'";
	$res = mysqli_query($con,$sql);
	if (mysqli_num_rows($res) != 0 || strlen($_POST["regUsername"]) == 0)
	{
		if (strlen($_POST["regUsername"]) > 0){
			$errorMsg = "Username already in use<br/>";
			echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
		}else{
			$errorMsg = "Must enter a valid username<br/>";
			echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
			$username = "";
		}
	}
	elseif ($_POST["regPass"] != $_POST["regPassV"])
	{
		$errorMsg = "Passwords do not match<br/>";
		echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
	}
	elseif ((strlen($_POST["regPass"]) < 8 || !preg_match('#\d#', $_POST["regPass"])) && strlen($_POST["regPass"]) != 0)
	{
		$errorMsg = "Password has less than 8 characters or has no numbers in it<br/>";
		echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
	}
	else
	{
		$password = $_POST["regPass"];
		$password = password_hash($password, PASSWORD_DEFAULT);
		mysqli_query($con, "INSERT INTO User (Username, Password) VALUES ('".$_POST["regUsername"]."', '$password')");
		echo '<script>document.cookie="username='.$_POST["regUsername"].';path=/";</script>';
		echo('<script>window.location=window.location;</script>');
	}

	mysqli_close($con);
}

if( $_POST["passChangeOld"] || $_POST["passChangeNew"] || $_POST["passChangeNewV"]){
	$con = mysqli_connect('127.0.0.1','root','blackteam','blackteam');
	$sql = "SELECT Password FROM User WHERE Username = '".$_COOKIE['username']."'";
	$res = mysqli_query($con, $sql);
	$hash = $res->fetch_object();
	$hash = $hash->Password;
	if(password_verify($_POST["passChangeOld"], $hash)){
		if($_POST["passChangeNew"] == $_POST["passChangeNewV"]){
			if((strlen($_POST['passChangeNew']) < 8 || !preg_match('#\d#', $_POST['passChangeNew'])) && strlen($_POST['passChangeNew']) != 0){
				$errorMsg = "Password has less than 8 characters or has no numbers in it<br/>";
				echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
			}else{
				$password = $_POST["passChangeNew"];
				$password = password_hash($password, PASSWORD_DEFAULT);
				mysqli_query($con, "UPDATE User SET Password = '$password' WHERE Username = '".$_COOKIE['username']."'");
				echo('<script>window.location=window.location;</script>');
			}
		}else{
			$errorMsg = "New passwords do not match<br/>";
			echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
		}
	}else{
		$errorMsg = "Old password does not match<br/>";
		echo('<script>writeToElement("account","'.$errorMsg.'")</script>');
	}
}

if($_POST["addBookmark"]){
	$username = $_COOKIE[username];
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$title = $_POST["addBookmark"];
	$stmt = $conn->query('SELECT * FROM Bookmarks WHERE Bookmark_article ="'.$url.'" AND username ="'.$username.'"');
	$row_count = $stmt->rowCount();
    if($row_count === 0){
    	$stmt = $conn->query('INSERT INTO Bookmarks(username,Bookmark_article,Bookmark_Title)VALUES("'.$username.'","'.$url.'","'.$title.'");');	
    }else{
    }		
	echo('<script>window.location=window.location;</script>');
}

if($_POST["deleteBookmark"]){
	$stmt = $conn->query('DELETE FROM Bookmarks WHERE Bookmark_article="'.$_POST["deleteBookmark"].'"');
	echo('<script>window.location=window.location;</script>');
}
?>
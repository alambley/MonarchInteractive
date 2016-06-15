function showhide(id) {
    var e = document.getElementById(id);
    e.style.display = (e.style.display == 'inline-block') ? 'none' : 'inline-block';
}

function addInput(divName)
{
    var newdiv = document.createElement('div');
    var inputArray = document.getElementById('dynamic-input');
    var numInputs = inputArray.getElementsByTagName('poll-inputs[]').length;
    newdiv.innerHTML = "<input classname='w3-input w3-border w3-margin-bottom' type='text' placeholder='Choice text' name='poll-inputs[]'>";
    document.getElementById(divName).appendChild(newdiv);
}

function castVote(pollTitle)
    {
        //need voter, poll name, and choice
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                //document.getElementById('bookmarks').innerHTML = xmlhttp.responseText;
                //showhide("bookmarks");
                showhide('polls');
                alert("Your vote has been cast");
            }
            
        }
        xmlhttp.open("POST","poll-vote.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        //get the currently logged in user
        var voter=getCookie("username");
        //check if there is not loggen in user
        //and if not, get the guest cookie
        if(voter == "")
        {
            voter = getCookie("MaceGuest");
        }
        
       // var voter = <?php echo "'" . $currentuser . "'"?>;
        console.log("voter is " + voter);
        var questionTitle = document.getElementsByName(pollTitle)[0].value;
        var c = 'input[name=';
        var e = ']:checked';
        c = c.concat(questionTitle,e);
        var choice = document.querySelector('input[name='+pollTitle+']:checked').value;
        
        var final = "q-title="+pollTitle+"&current-user="+voter+"&choice="+choice;
        //var final = 
        xmlhttp.send("q-title="+pollTitle+"&current-user="+voter+"&choice="+choice);
       
        
        
    }
    
    function showPollVotes()
    {
       
       var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                document.getElementById('poll-display').innerHTML = xmlhttp.responseText;
            }
            else
            {
                document.getElementById('poll-display').innerHTML = xmlhttp.responseText;
            }
            
        }
        xmlhttp.open("POST","poll-results.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        //get the currently logged in user
        var voter=getCookie("username");
        //check if there is not logged in user
        //and if not, get the guest cookie
        if(voter == "")
        {
            voter = getCookie("MaceGuest");
        }
        console.log("showPollVotes() current user is " + voter);
        //urlStr = <?php echo "'" . $currenturl . "'"?>;
        //userStr = <?php echo "'" . $currentuser . "'"?>;
        
        //var fullString = "url="+urlStr+"&user="+userStr;
        xmlhttp.send("user="+voter);
    }
    function hidePoll(pollId)
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
               showhide("polls"); 
            }
            else
            {
               
            }
                       
        }
        xmlhttp.open("POST","poll-hide.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        console.log("poll id is " + pollId);
        
        xmlhttp.send("pollid=" + pollId);
    }
    function deletePoll(pollId)
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
               showhide("polls"); 
            }
            else
            {
               
            }
                       
        }
        xmlhttp.open("POST","poll-delete.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        console.log("poll id is " + pollId);
        
        xmlhttp.send("pollid=" + pollId);
    }
    function showHiddenPolls()
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                document.getElementById('show-hidden').innerHTML = xmlhttp.responseText;
            }
            else
            {
               
            }
                       
        }
        xmlhttp.open("POST","poll-hidden.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
        
        xmlhttp.send();
    }
    function unhidePoll(id)
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                //showhide("polls");
                showPollVotes();
                showHiddenPolls();
            }
            else
            {
               
            }
                       
        }
        xmlhttp.open("POST","poll-unhide.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
        
        xmlhttp.send("pollid="+id);
    }
    function showExpPolls()
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                document.getElementById('show-expired').innerHTML = xmlhttp.responseText;
            }
            else
            {
                //document.getElementById('poll-display').innerHTML = xmlhttp.responseText;
            }
            
        }
        xmlhttp.open("POST","poll-exp-results.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        //get the currently logged in user
        var voter=getCookie("username");
        //check if there is not loggen in user
        //and if not, get the guest cookie
        if(voter == "")
        {
            voter = getCookie("MaceGuest");
        }
        console.log("showPollVotes() current user is " + voter);
      
        xmlhttp.send("user="+voter);
    }
     function createPoll()
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status == 200)
            {
                
                document.getElementById('poll-create').style.display='none';   
                
                showhide('polls');
                alert("Your poll has been created");
            }
            
        }
        //need the poll text and choices and flag for number of poll choices
        xmlhttp.open("POST","poll-create.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        var pollResponses = document.getElementsByName('poll-inputs[]');
        var numResponses = pollResponses.length;
        
        var finalChoices = "";
        var lead = "&choice"
        var eq = "="
        //build poll choices string with number of inputs
        for(i = 0; i < numResponses; i++)
        {
            finalChoices = finalChoices.concat(lead,(i+1),eq,pollResponses[i].value);
        }
        var q = document.getElementById('poll-question-text').value;
        //get value of checkbox true for global poll, false otherwise
        var global = document.getElementById('global-checkbox').checked;

        
        var eDate = document.getElementById('datepicker').value;
        if(eDate == "")
        {
            //a year before mysql max datetime
            eDate="01/01/2038";
        }
        eDate = eDate.split("/");
        
        var expDate = eDate[2] + "-" + eDate[0] +"-" +eDate[1];
        var fullstring = "";
        var s = "pollq=";
        var num = "&numChoices="
       
        fullstring = fullstring.concat(s,q,num,numResponses,finalChoices);
        xmlhttp.send(fullstring+"&expDate=" + expDate + "&global="+global);
    }
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0){
            return c.substring(name.length, c.length);
        } 
    }
    return "";
}

function deleteCookie() {
 document.cookie="username=; expires=Thu, 18 Dec 2013 12:00:00 UTC; path=/";
}

function insertHTML(id, html) {
    var el = document.getElementById(id);
    
    if(!el) {
        alert('Element with id ' + id + ' not found.');
    }
    
    el.innerHTML = html;
}

function checkString(str1,str2){
    if(new String(str1).valueOf() === new String(str2).valueOf()){
        return true;
    }
    return false;
}

function include(arr, obj) {
    for(var i=0; i<arr.length; i++) {
        if (checkString(arr[i],obj)) return true;
    }
    return false;
}






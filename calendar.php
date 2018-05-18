<!DOCTYPE html>
<?php
ini_set("session.cookie_httponly", 1);
session_start();
session_destroy();
?>
<html>
<head><title>Calendar</title>
<link type = "text/css" rel="stylesheet" href="calendar.css">
<script>

(function(){Date.prototype.deltaDays=function(c){return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)};Date.prototype.getSunday=function(){return this.deltaDays(-1*this.getDay())}})();
function Week(c){this.sunday=c.getSunday();this.nextWeek=function(){return new Week(this.sunday.deltaDays(7))};this.prevWeek=function(){return new Week(this.sunday.deltaDays(-7))};this.contains=function(b){return this.sunday.valueOf()===b.getSunday().valueOf()};this.getDates=function(){for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));return b}}
function Month(c,b){this.year=c;this.month=b;this.nextMonth=function(){return new Month(c+Math.floor((b+1)/12),(b+1)%12)};this.prevMonth=function(){return new Month(c+Math.floor((b-1)/12),(b+11)%12)};this.getDateObject=function(a){return new Date(this.year,this.month,a)};this.getWeeks=function(){var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);return c}};
// For our purposes, we can keep the current month in a variable in the global scope
 // October 2017

// Change the month when the "next" button is pressed
	var currentMonth = new Month(2018, 2);
	
	function next_month(event){
	
	currentMonth = currentMonth.nextMonth(); // Previous month would be currentMonth.prevMonth()
	//updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
	updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
	//alert("The new month is "+currentMonth.month+" "+currentMonth.year);
}

	function prev_month(event){
	currentMonth = currentMonth.prevMonth(); // Previous month would be currentMonth.prevMonth()
	updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
	//alert("The new month is "+currentMonth.month+" "+currentMonth.year);
	//document.getElementById('month').innerHTML=currentMonth.month+" " +currentMonth.year;
}

function updateCalendar(){
	document.getElementById('title').innerHTML=monthString(currentMonth.month)+" " +currentMonth.year;
	var weeks = currentMonth.getWeeks();
	var counter=1;
	for(var w=0; w<weeks.length; w++){
		//alert(w);
		var days = weeks[w].getDates();
		// days contains normal JavaScript Date objects.
		
		//alert("Week starting on "+days[0]);
		for(var d =0; d<days.length; d++){
			// You can see console.log() output in your JavaScript debugging tool, like Firebug,
			// WebWit Inspector, or Dragonfly.
			//alert(d);
			var date = days[d].toISOString();
			var date_day = date.charAt(8)+date.charAt(9);
			var index = w.toString()+d.toString();
			
			if(counter==parseInt(date_day)){
				counter++;
				loadEvents(days[d],index);
			}
			else{
				document.getElementById(index).innerHTML="";
			}		
		
		}
	}
}

function loadEvents(date,index){
	var xmlHttp = new XMLHttpRequest();
	var year = date.getFullYear().toString();
	var month = (date.getMonth()+1).toString();
	var day = date.getDate().toString();

	var date_num = year+"-"+month+"-"+day;
	var dataString = "date=" + encodeURIComponent(date_num);
	xmlHttp.open("POST", "search_date.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener('load',function(event){
	var data= JSON.parse(event.target.responseText);
	document.getElementById(index).innerHTML="<strong>"+day+"<strong><br>";
	if(data.success){
		if(data.exist){
			for(var e in data.events){
				if(data.events.hasOwnProperty(e)){
				document.getElementById(index).innerHTML+="<span class = "+data.colors[e]+">" + data.events[e] + " at " + data.times[e] + "</span><br>";	
				}
				
			}
		}
	}
	},false);
	xmlHttp.send(dataString);
}

function monthString(m){
	if(m===0){
		return("January");
	}
	else if(m==1){
		return("February");
	}
	else if(m==2){
		return("March");
	}
	else if(m==3){
		return("April");
	}
	else if(m==4){
		return("May");
	}
	else if(m==5){
		return("June");
	}
	else if(m==6){
		return("July");
	}
	else if(m==7){
		return("August");
	}
	else if(m==8){
		return("September");
	}
	else if(m==9){
		return("October");
	}
	else if(m==10){
		return("November");
	}
	else{
		return("December");
	}
}


function loginAjax(event){
	var username = document.getElementById("username").value; // Get the username from the form
	var password = document.getElementById("password").value; // Get the password from the form
	
	// Make a URL-encoded string for passing POST data:
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "login.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		if(jsonData.success){  // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
			
			document.getElementById('logout_btn').setAttribute('type','submit');
			document.getElementById('username').setAttribute('type','hidden');
			document.getElementById('password').setAttribute('type','hidden');
			document.getElementById('login_btn').setAttribute('type','hidden');
			document.getElementById('register_btn').setAttribute('type','hidden');
			document.getElementById('username_label').innerHTML="You are now logged in!";
			document.getElementById('password_label').innerHTML=" ";
			document.getElementById('event_management').innerHTML="Event Management";
			document.getElementById('share_label').innerHTML="Enter user to share your calendar with them";
			document.getElementById('user_to_share').setAttribute("type","text");
			document.getElementById('share_button').setAttribute("type","submit");
		}else{
			alert("You were not logged in.  "+jsonData.message);
		}
		
	}, false); // Bind the callback to the load even
	xmlHttp.send(dataString);
	 // Send the data
	updateCalendar();
	eventManagement();
	updateCalendar();
	
}

function registerAjax(event){
	var username = document.getElementById("username").value; // Get the username from the form
	var password = document.getElementById("password").value; // Get the password from the form
	
	// Make a URL-encoded string for passing POST data:
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "register.php", true); // Starting a POST request (NEVER send passwords as GET variables!!!)
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
	xmlHttp.addEventListener("load", function(event){
		//STOPS FUNCTIONING HERE
		console.log('checking');
		var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		if(jsonData.success){  // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
			alert("You've been registered as a new user!");
			document.getElementById('logout_btn').setAttribute('type','submit');
			document.getElementById('username').setAttribute('type','hidden');
			document.getElementById('password').setAttribute('type','hidden');
			document.getElementById('login_btn').setAttribute('type','hidden');
			document.getElementById('register_btn').setAttribute('type','hidden');
			document.getElementById('username_label').innerHTML="You are now registered!";
			document.getElementById('password_label').innerHTML=" ";
			document.getElementById('event_management').innerHTML="Event Management";
			document.getElementById('share_label').innerHTML="Enter user to share your calendar with them";
			document.getElementById('user_to_share').setAttribute("type","text");
			document.getElementById('share_button').setAttribute("type","submit");
		}else{
			alert("You were not registered as a new user.  "+jsonData.message);
		}
	}, false); // Bind the callback to the load event
	xmlHttp.send(dataString);
	updateCalendar();
	eventManagement(); // Send the data
}

function logoutAjax(event){
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	console.log('check');
	xmlHttp.open("GET", "logout.php", true);	
	xmlHttp.send(null);
	alert("You have been logged out!");
	document.getElementById('logout_btn').setAttribute('type','hidden');
	document.getElementById('username').setAttribute('type','text');
	document.getElementById('password').setAttribute('type','password');
	document.getElementById('password').setAttribute('value','');
	document.getElementById('login_btn').setAttribute('type','submit');
	document.getElementById('register_btn').setAttribute('type','submit');
	document.getElementById('username_label').innerHTML="Username";
	document.getElementById('password_label').innerHTML="Password";
	document.getElementById('event_management').innerHTML=" ";
	document.getElementById('all_events').innerHTML=" ";
	document.getElementById('share_label').innerHTML=" ";
	document.getElementById('user_to_share').setAttribute("type","hidden");
			document.getElementById('share_button').setAttribute("type","hidden");
	updateCalendar();
	
}

function createEventAjax(){
	var event_title = document.getElementById("event_title").value;
	var date = document.getElementById("date").value;
	var time = document.getElementById("time").value;
	var color = document.getElementById("color").value;
	var dataString = "event_title=" + encodeURIComponent(event_title) + "&date=" + encodeURIComponent(date)+ "&time=" + encodeURIComponent(time) + "&color=" + encodeURIComponent(color);
	var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
	xmlHttp.open("POST", "create_event.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
	var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
		if(jsonData.success){
		alert("Event has been created!");
	}
		else{
			alert(jsonData.message);
		}
		},false);
	xmlHttp.send(dataString);
	updateCalendar();
	eventManagement();
}

function eventManagement(){
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("GET", "view_events.php", true);
	xmlHttp.addEventListener('load',function(event){
	console.log('help');
	var data= JSON.parse(event.target.responseText);
	document.getElementById('all_events').innerHTML=" ";
	if(data.success){
		for(var e in data.id){
			if(data.id.hasOwnProperty){
			var id = data.id[e].toString();
			document.getElementById('all_events').innerHTML += "<li id="+id+">"+data.events[e]+"</li>";
			document.getElementById('all_events').innerHTML += "<button id= delete"+id+" onClick=deleteEventAjax("+id+")>Delete</button><br><br>";
			document.getElementById('all_events').innerHTML += "<button id= share"+id+" onClick=shareEventAjax("+id+")>Share</button><br>";
			document.getElementById('all_events').innerHTML += "<label id = label_share_username"+id+"></label><input type = hidden id=share_username_"+id+">";
			document.getElementById('all_events').innerHTML += "<input type = hidden id=submit_share_"+id+" value = 'Share'><br>";
			document.getElementById('all_events').innerHTML += "<button id= edit"+id+" onClick=editEventAjax("+id+")>Edit</button><br>";
			document.getElementById('all_events').innerHTML += "<label id = labeltitle"+id+"></label><input type = hidden id=edittitle"+id+">";
			document.getElementById('all_events').innerHTML += "<label id = labeldate"+id+"></label><input type = hidden id=editdate"+id+">";
			document.getElementById('all_events').innerHTML += "<label id = labeltime"+id+"></label><input type = hidden id=edittime"+id+">";
			document.getElementById('all_events').innerHTML += ('<select id = editcolor'+id+'> <option value = "None">None</option><option value = "Red">Red</option><option value = "Blue">Blue</option><option value = "Green">Green</option><option value = "Purple">Purple</option><option value = "Orange">Orange</option></select>');
			document.getElementById('editcolor'+id).style.visibility = 'hidden';
			document.getElementById('all_events').innerHTML += "<input type = hidden id=submit"+id+" value = 'Update'><br>";
		}
	}
	}
	},false);
	xmlHttp.send(null);
}

function deleteEventAjax(id){
	var dataString = "id="+encodeURIComponent(id);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST","delete_events.php",true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		alert("Event has been deleted");
	},false);
	xmlHttp.send(dataString);
	eventManagement();
	updateCalendar();
}

function editEventAjax(id){
	document.getElementById('edittitle'+id).setAttribute("type","text");
	document.getElementById('editdate'+id).setAttribute("type","date");
	document.getElementById('edittime'+id).setAttribute("type","time");
	document.getElementById('editcolor'+id).style.visibility = 'visible';
	document.getElementById('submit'+id).setAttribute("type","submit");
	document.getElementById('labeltitle'+id).innerHTML="Title";
	document.getElementById('labeldate'+id).innerHTML="Date";
	document.getElementById('labeltime'+id).innerHTML="Time";
	document.getElementById('submit'+id).addEventListener('click',function(event){
		var new_title = document.getElementById('edittitle'+id).value;
		var new_date = document.getElementById('editdate'+id).value;
		var new_time = document.getElementById('edittime'+id).value;
		var new_color = document.getElementById('editcolor'+id).value;
		var dataString = "id="+encodeURIComponent(id) + "&new_title=" + encodeURIComponent(new_title) + "&new_date=" + encodeURIComponent(new_date) + "&new_time=" + encodeURIComponent(new_time)+ "&new_color=" + encodeURIComponent(new_color);
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("POST","edit_events.php",true);
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlHttp.addEventListener("load", function(event){
		alert("Event was updated!");
	},false);
	xmlHttp.send(dataString);
	eventManagement();
	updateCalendar();
		
	},false);
}

function shareEventAjax(id){
	document.getElementById('label_share_username'+id).innerHTML="Enter Username";
	document.getElementById('share_username_'+id).setAttribute("type","text");
	document.getElementById('submit_share_'+id).setAttribute("type","submit");
	document.getElementById('submit_share_'+id).addEventListener('click',function(event){
		var send_to_username = document.getElementById("share_username_"+id).value;
		var dataString = "send_to_username="+encodeURIComponent(send_to_username) + "&id=" + encodeURIComponent(id);
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("POST","share_events.php",true);
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlHttp.addEventListener("load", function(event){
			var jsonData = JSON.parse(event.target.responseText);
			if(jsonData.success){
				alert("Your event was shared with " + send_to_username);
			}
			else{
				alert("You did not enter a valid username. Please try again");
			}
		},false);
		xmlHttp.send(dataString);
		eventManagement();
		updateCalendar();
	},false);
}

function shareCalendar(){
	var send_to_username = document.getElementById("user_to_share").value;
	var dataString = "send_to_username="+encodeURIComponent(send_to_username);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST","share_calendar.php",true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
			var jsonData = JSON.parse(event.target.responseText);
			if(jsonData.success){
				alert("Your calendar was shared with " + send_to_username);
			}
			else{
				alert("You did not enter a valid username. Please try again");
			}
		},false);
		xmlHttp.send(dataString);
}

</script>

</head>
<body>
<p id = "login">
<label id = "username_label" for = "username">Username:</label><input type = "text" name = "username" id = "username">
<label id = "password_label" for = "password">Password:</label><input type = "password" name = "password" id="password">
<input type = "submit" name= "submit" id="login_btn" value = "Login">
<input type = "submit" name = "submit" id= "register_btn" value= "Register">
</p>
<input type = "hidden" id= "logout_btn" value="Logout">
<div>
<h1 id="title">Month</h1>
	<button class="prev" id="prev">&#10094;</button>
    <button class="next" id ="next">&#10095;</button>
</div>
<table id="Calendar">
	
        <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
        </tr>
        <tr id="week0"><td id ="00"></td><td id ="01"></td><td id ="02"></td><td id ="03"></td><td id ="04"></td><td id ="05"></td><td id ="06"></td></tr>
        <tr id="week1"><td id ="10"></td><td id ="11"></td><td id ="12"></td><td id ="13"></td><td id ="14"></td><td id ="15"></td><td id ="16"></td></tr>
        <tr id="week2"><td id ="20"></td><td id ="21"></td><td id ="22"></td><td id ="23"></td><td id ="24"></td><td id ="25"></td><td id ="26"></td></tr>
        <tr id="week3"><td id ="30"></td><td id ="31"></td><td id ="32"></td><td id ="33"></td><td id ="34"></td><td id ="35"></td><td id ="36"></td></tr>
        <tr id="week4"><td id ="40"></td><td id ="41"></td><td id ="42"></td><td id ="43"></td><td id ="44"></td><td id ="45"></td><td id ="46"></td></tr>
        <tr id="week5"><td id ="50"></td><td id ="51"></td><td id ="52"></td><td id ="53"></td><td id ="54"></td><td id ="55"></td><td id ="56"></td></tr>
</table>

<label for = "event_title">Title:</label><input type="text" name = "event_title" id="event_title">
<label for = "date">Date</label><input type="date" name = "date" id="date">
<label for = "time">Time</label><input type="time" name = "time" id="time">
<select id = "color" name = "color">
	<option value = "None">None</option>
	<option value = "Red">Red</option>
	<option value = "Blue">Blue</option>
	<option value = "Green">Green</option>
	<option value = "Purple">Purple</option>
	<option value = "Orange">Orange</option>
</select>
<input type = "submit" name = "event_submit" id = "event_submit" value = "Create Event">

<h2 id = "event_management">To view events, please log in</h2>
<p id = "all_events">
</p>

<p>
<label id = "share_label"></label>
<input type = "hidden" id = "user_to_share">
<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
<input type = "hidden" id="share_button" value = "Share">
</p>
<script>
document.addEventListener("DOMContentLoaded", updateCalendar, false);
document.getElementById("next").addEventListener("click", next_month, false);
document.getElementById("prev").addEventListener("click", prev_month, false);
document.getElementById("login_btn").addEventListener("click", loginAjax, false);
document.getElementById("register_btn").addEventListener("click", registerAjax, false);
document.getElementById("logout_btn").addEventListener("click", logoutAjax, false);
document.getElementById("event_submit").addEventListener("click", createEventAjax, false);
document.getElementById("share_button").addEventListener("click",shareCalendar,false);
</script>
</body>
</html>
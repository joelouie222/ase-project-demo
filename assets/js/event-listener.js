// JavaScript Document
//var elMsg = document.getElementById('feedback');     			
var elUsername = document.getElementById('username');
var elPassword = document.getElementById('password');
var elEmail = document.getElementById('email');
function checkData(minLength,input_div,feedback) 
{                             							
    var elMsg = document.getElementById(feedback);
    var el = document.getElementById(input_div);
    if (el.value.length < minLength) 
    {                   								
        elMsg.innerHTML = input_div.toUpperCase()+' must be '+minLength+' characters or more';
        mainDiv.classList.add("has-error");
    } 
    else 
    {                                              						
        elMsg.innerHTML = '';
        mainDiv.classList.remove("has-error");
    }
}
function validateEmail(email)
{
    var validRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (email.value.match(validRegex))
    {
        //code to dispaly a valid email was entered
    }
    else
    {
        //code to dispaly email is invalid   
    }
}
elUsername.addEventListener('blur', function() {
	checkData(5,'username','unFeedback');
	},false);
elPassword.addEventListener('blur', function() {
	checkData(8,'password','pwFeedback');
	},false);
elEmail.addEventListener('blur', function() {
	validateEmail('email');
	},false);

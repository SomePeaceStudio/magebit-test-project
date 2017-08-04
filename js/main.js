window.onload = function(e){
	// Add event listener for window width change 
   	var mediaQueryList = window.matchMedia("(max-width: 960px)");
	mediaQueryList.addListener(handleWidthChange);
	handleWidthChange(window.matchMedia("(max-width: 960px)"));

	// Add event listener for login form submitted
	var loginBtn = document.querySelector('#login form');
	loginBtn.addEventListener("submit", function(e){
		e.preventDefault();
		login();
	}, false);

	// Add event listener for signup form submitted
	var signupBtn = document.querySelector('#signup form');
	signupBtn.addEventListener("submit", function(e){
		e.preventDefault();
		signup();
	}, false);

	// Add event listener for input change in forms to change icons 
	var inputs = document.getElementsByClassName("text-input");
	for (var i = 0; i < inputs.length; i++) {
		inputs[i].addEventListener("input", function(e){
			iconChange(e);
		}, false);
	}

};

// changeActiveContent(button identifier string)
// slides the active content window and swaps the inner content
function changeActiveContent(clicked){
	// set movement axis
	var dimension = 'X'
	if(window.matchMedia('(max-width: 960px)').matches){
		dimension = 'Y'
	}
	var contentE = document.getElementsByClassName("content-active")
	// move the active content to the left &
	// display a sign-up window
	if(clicked == 'signup'){
		var oldContE = document.getElementsByClassName("active-right")
		var newContE = document.getElementsByClassName("active-left")
		contentE[0].style.transform = 'translate'+dimension+'(-420px)';
	}else if(clicked == 'login'){
	// move the active content to the right &
	// display a login window
		var oldContE = document.getElementsByClassName("active-left")
		var newContE = document.getElementsByClassName("active-right")
		contentE[0].style.transform = 'translate'+dimension+'(0px)';
	}else{
		return;
	}
	// swap visibility
	oldContE[0].className += " invisible"
	setTimeout(function() {
		oldContE[0].className += " hidden"
		newContE[0].classList.remove("hidden");
	}, 500);
	setTimeout(function() {
		newContE[0].classList.remove("invisible");	
	}, 600);
}

// handleWidthChange(mql)
// handle width change modifying
// the active content transformation
function handleWidthChange(mql) {
	var contentE = document.getElementsByClassName("content-active")
	var transformation = contentE[0].style.transform;
	if(	transformation == null || transformation ==  undefined || 
	   	transformation ==  ''){
		return;
	}
	if (mql.matches) {
        // window width went small
		if(transformation == 'translateX(-420px)'){
			contentE[0].style.transform = 'translateY(-420px)'
		}
    }else{
    	// window width got big
    	if(transformation == 'translateY(-420px)'){
			contentE[0].style.transform = 'translateX(-420px)'
		}
    }
}

// login(string email, string password)
// Call server to login the user. If arguments are not passed then they are 
// taken from login form.
function login(mail, pass) {

	var email = mail ? mail : $('#login input[name="txt_email"]').val();
	var password = pass ? pass : $('#login input[name="txt_password"]').val();
	$.ajax({
	url: 'api.php',
	type: 'post',
	data: {'login': true, 'txt_email': email, 'txt_password': password},
	success: function(json) {
		if(json.status == 'success'){
			window.location.replace("home.php");
		}
		else if(json.status == 'error'){
			$('#login .error').html(json.data);
		}
	},
	error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
	}	
	});
}

// signup()
// Takes arguments from sign-up form and requests the server to
// register a user.
function signup() {

	var name = $('#signup input[name="txt_name"]').val();
	var email = $('#signup input[name="txt_email"]').val();
	var password = $('#signup input[name="txt_password"]').val();

	$.ajax({
	url: 'api.php',
	type: 'post',
	data: {	'signup': true, 
			'txt_name': name, 
			'txt_email': email, 
			'txt_password': password
	},
	success: function(json) {
		if(json.status == 'success'){
			alert("You have successfully registered!");
			login(email, password);
		}
		else if(json.status == 'error'){
			$('#signup .error').html(json.data);
		}
	},
	error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
	}	
	}); 
}

// iconChange(event from input field that was changed)
// Takes closest previous icon and changes it accordingly to 
// validity of the input.
function iconChange(e){
	var icon = $(e.target).prev(".input-label").children(".input-icon");
	var clases = icon.attr('class');
	var regex = /^icon-*-*/
	clases = clases.split(/[ ,]+/);
	var oldIcon = '';
	for (var i = 0; i < clases.length; i++) {
		if(regex.test(clases[i])){
			oldIcon = clases[i];
		}
	}
	if(oldIcon==''){
		return;
	}
	var iconName = oldIcon.split(/-[a-z]*$/).filter(Boolean)[0];
	var oldState = oldIcon.split(/^icon-[a-z]*-/).filter(Boolean)[0];
	var valid = e.target.checkValidity();
	if(valid && oldState=="inactive"){
		icon.removeClass(oldIcon);
		icon.addClass(iconName+"-active");
		return;
	}
	if(!valid && oldState=="active"){
		icon.removeClass(oldIcon);
		icon.addClass(iconName+"-inactive");
		return;
	}
}

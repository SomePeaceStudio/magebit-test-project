<?php

require_once 'dbconfig.php';

header('Content-type: application/json');

if(isset($_POST['login'])){
	$email = trim($_POST['txt_email']);
	$password = trim($_POST['txt_password']);

	$response_array = [];
	if($user->login($email,$password)){
		$response_array['status'] = 'success';
	}
	else{
    	$response_array['status'] = 'error'; 
    	$response_array['data'] = 'The password you’ve entered is incorrect'; 
	}
	echo json_encode($response_array);
}

if(isset($_POST['signup'])){

	$name = trim($_POST['txt_name']);
	$email = trim($_POST['txt_email']);
	$password = trim($_POST['txt_password']);
	$response_array = [];

	if($name==""){
		$error[] = "Please enter your full name"; 
	}
	if($email==""){
		$error[] = "Email is missing."; 
	}
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error[] = 'Please enter a valid email address.';
	}
	if($password==""){
		$error[] = "Password is missing.";
	}
	else if(strlen($password) < 8){
		$error[] = "Password must be at least 8 characters."; 
	}
	if(count($error) == 0){
		try{
			$query = $DB_con->prepare("SELECT user_email FROM users WHERE user_email=:email");

			$query->execute(array(':email'=>$email));
			$row = $query->fetch(PDO::FETCH_ASSOC);

			if($row['user_email']==$email){
			    $error[] = "Sorry, email already taken !";
			}
			else
			{
			    if($user->register($name,$email,$password)){ 
			    	$response_array['status'] = 'success';
				}
			}
		}
		catch(PDOException $e){
			$response_array['status'] = 'error';
			$response_array['data'] = $e->getMessage();
		}
	}
	if($response_array['status']!='success'){
    	$response_array['status'] = 'error';
    	$response_array['data'] = $error;
	}
	echo json_encode($response_array);
}

<?php
include_once 'dbconfig.php';

if(!$user->is_loggedin()){
 	$user->redirect('/');
}
if($_GET['logout']){
	$user->logout();
	$user->redirect('/');
}
$user_id = $_SESSION['user_session'];
$query = $DB_con->prepare("SELECT * FROM users WHERE user_id=:user_id");
$query->execute(array(":user_id"=>$user_id));
$userRow=$query->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>welcome - <?php print($userRow['user_email']); ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="all">
	<link rel="stylesheet" href="css/home-style.css" type="text/css"  />
</head>

<body>
	<div class="header">
	    <div>
			<label><a href="?logout=true">logout</a></label>
	    </div>
	</div>
	<div class="content">
		welcome : <?php print($userRow['user_name']); ?>
	</div>
</body>
</html>
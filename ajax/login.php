<?php
require_once '../globals.php';
if(isset($_SESSION['uid'])) {
	//we already have our session set, we're good to go!
	exit();
}
$payload = json_decode(file_get_contents("https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=".$_POST['idtok']), true);
if ($payload && isset($payload['email']) && !isset($payload['error_description']) && $payload['azp'] == "962755034364-b2kppi91a9qjf4dbcfol4nfttob3hrmv.apps.googleusercontent.com" && $payload['aud'] == "962755034364-b2kppi91a9qjf4dbcfol4nfttob3hrmv.apps.googleusercontent.com") {
	$userid = $payload['sub'];
	$emailAddr = $payload['email'];
	$nameFirst = $payload['given_name'];
	$nameLast = $payload['family_name'];
	$picture = $payload['picture'];
	if(isset($payload['hd']))
		$domain = $payload['hd'];
	$latinSt = ($domain == "mvla.net");
	//do the database work
	$mysql = dbConnection();
	getUser: //this is hopefully the only time i'll be lazy enough to actually use my name-sake operator.
	$selectQuery = "SELECT uid,email,name_given,name_family,student,latin_period,photo FROM users WHERE g_id=?";
    $sth = $mysql->prepare($selectQuery);
	echo $mysql->error;
    $sth->bind_param('s', $userid);
    $sth->execute();
    $sth->bind_result($uid, $email, $name_given, $name_family, $student, $latinper,$picture);
    $sth->store_result();
    $sth->fetch();
	if($sth->num_rows()) {
		//let's update our session variables
		$_SESSION['uid'] = $uid;
		$_SESSION['name_given'] = $name_given;
		$_SESSION['name_family'] = $name_family;
		$_SESSION['email'] = $email;
		$_SESSION['g_id'] = $userid;
		$_SESSION['pic'] = $picture;
		if($student)
			$_SESSION['period'] = $latinper;
		
		$updateQuery = "UPDATE users SET lastSignin=now() WHERE g_id=?";
	    $sth2 = $mysql->prepare($updateQuery);
	    $sth2->bind_param('s', $userid);
	    $sth2->execute();
		http_response_code(200);
		exit();
	}
	else {
		//Insert
		$insertQuery = "INSERT INTO `users` (`email`, `name_given`, `name_family`, `g_id`, `student`, `lastSignin`, `photo`) VALUES(?,?,?,?,?,now(),?)";
	    $sth2 = $mysql->prepare($insertQuery);
	    $sth2->bind_param('ssssis', $emailAddr, $nameFirst, $nameLast, $userid, $latinSt, $picture);
	    $sth2->execute();
		$sth2->close();
		goto getUser;
	}
}
http_response_code(400);
exit();
?>
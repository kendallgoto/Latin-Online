<?php
require_once '../globals.php';
if(!isset($_SESSION['uid'])) {
	http_response_code(404);
	exit();
}
$wordID = $_POST['wordId'];
$guessList = $_POST['guesses'];
$endpoint = $_POST['endpoint'];
$freeplay = (strpos($endpoint, "fp/") !== FALSE);
$date = $_POST['postDate'];
$accr = $_POST['right'];
$session = $_POST['sessionid'];
if(strlen($session) > 40)
	exit('inv. session code (len)');
if(explode("_", $session)[0] != "".$_SESSION['uid'])
	exit('inv. session code (id)');
$mysql = dbConnection();
getSession:
$selectQuery = "SELECT id FROM sessions WHERE uid=? AND `unique`=?";
$sth = $mysql->prepare($selectQuery);
echo $mysql->error;
$sth->bind_param('is', $_SESSION['uid'], $session);
$sth->execute();
$sth->bind_result($session_id);
$sth->store_result();
$sth->fetch();
if($sth->num_rows()) {
	foreach($guessList as $guess) {
		if($guess['right'] === 'true')
			$guess['right'] = 1;
		else
			$guess['right'] = 0;
		if(strlen($guess['guess']) > 100)
			continue;
		$newElement = "INSERT INTO `session_events` (`word`, `guess`, `postDate`, `sessionIdent`, `timing`, `right`) VALUES(?,?,?,?,?,?)";
	    $sth2 = $mysql->prepare($newElement);
	    $sth2->bind_param('issiii', $wordID, $guess['guess'], date("Y-m-d H:i:s", $date), $session_id, $guess['time'], $guess['right']);
	    $sth2->execute();
	}
	http_response_code(200);
	exit();
}
else {
	//Insert
	$insertQuery = "INSERT INTO `sessions` (`uid`, `freeplay`, `endpoint`, `unique`) VALUES(?,?,?,?)";
    $sth2 = $mysql->prepare($insertQuery);
    $sth2->bind_param('iiss', $_SESSION['uid'], $freeplay, $endpoint, $session);
    $sth2->execute();
	$sth2->close();
	goto getSession;
}
?>
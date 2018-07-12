<?php
require_once '../globals.php';
if(!isset($_SESSION['uid'])) {
	http_response_code(404);
	exit();
}
$type = $_POST['type']; //verb, noun, etc.
if(strlen($type) > 7) {
	http_response_code(400);
	exit();
}
$chapter_min = intval($_POST['start']);
$chapter_max = intval($_POST['end']);
$random = boolval($_POST['randomized']);
$reqSize = intval($_POST['reqSize']);
if($reqSize > 880 && $reqSize <= 0) {
	http_response_code(400);
	exit();
}
$mysql = dbConnection();
if($type == "gens") {
	$query = "SELECT id,type,latin,english,gender,declension,notes,conjugation,chapter FROM words WHERE type='noun' AND declension=3 AND chapter >= ? AND chapter <= ? " . (($random) ? "ORDER BY RAND()" : "") . " LIMIT ?";
	$sth = $mysql->prepare($query);
	$sth->bind_param('iii', $chapter_min, $chapter_max, $reqSize);
} else if($type == "nums") {
	$query = "SELECT id,type,latin,english,gender,declension,notes,conjugation,chapter FROM words WHERE (type='adjective, ordinal' OR type='adjective, cardinal') AND chapter >= ? AND chapter <= ? " . (($random) ? "ORDER BY RAND()" : "") . " LIMIT ?";
	$sth = $mysql->prepare($query);
	$sth->bind_param('iii', $chapter_min, $chapter_max, $reqSize);
} else if($type != "all") {
	$query = "SELECT id,type,latin,english,gender,declension,notes,conjugation,chapter FROM words WHERE type=? AND chapter >= ? AND chapter <= ? " . (($random) ? "ORDER BY RAND()" : "") . " LIMIT ?";
	$sth = $mysql->prepare($query);
	$sth->bind_param('siii', $type, $chapter_min, $chapter_max, $reqSize);
} else {
	$query = "SELECT id,type,latin,english,gender,declension,notes,conjugation,chapter FROM words WHERE chapter >= ? AND chapter <= ? " . (($random) ? "ORDER BY RAND()" : "") . " LIMIT ?";
	$sth = $mysql->prepare($query);
	$sth->bind_param('iii', $chapter_min, $chapter_max, $reqSize);
}
$sth->execute();
$sth->bind_result($word_id, $word_type, $word_latin, $word_english, $word_gender, $word_declension, $word_notes, $word_conjugation, $word_chapter);
$results = [
	"size" => 0,
	"result" => []
];
while($sth->fetch()) {
	$thisResult = [
		"id" => $word_id,
		"type" => $word_type,
		"latin" => $word_latin,
		"english" => $word_english,
		"gender" => $word_gender,
		"declension" => $word_declension,
		"notes" => $word_notes,
		"conjugation" => $word_conjugation,
		"chapter" => $word_chapter
	];
	$results['result'][] = $thisResult;
}
$results['size'] = count($results['result']);
http_response_code(200);
header('Content-Type: application/json');
exit(json_encode($results));
?>
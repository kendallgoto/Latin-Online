<?php
	
require_once('../globals.php');
if(!isset($_SESSION['uid'])) {
	header("Location: /latin/");
	exit();
}
$lim = $_GET['limit'];
$start = min($_GET['start'], $_GET['end']);
$end = max($_GET['start'], $_GET['end']);
$start = max($start, 1);
$end = min($end, 40);

if(isset($_GET['method']))
	$method = $_GET['method'];
else
	$method = "chapter";
//Display methods:
//Chapter, Alphabetical, Grammar, Frequently Missed
$sortStyles = [
	"chapter" => "Chapter",
	"lexie" => "Alphabetical (Eng)",
	"lexil" => "Alphabetical (Lat)",
	"grammar" => "Grammar",
	"missed" => "Accuracy"
];
//figure out our current sorting style
$activeStyle = $sortStyles[$method];
$baseURL = "/latin/fp/wordlist/$start/$end/";
if(!isset($sortStyles[$method])) {
	//this doesn't exist...
	header("Location: {$baseURL}chapter");
	exit();
}
$build = '<p class="lead">Sorting by <a href="'.$baseURL.'chapter" class="badge badge-primary">'.$activeStyle.'</a> -';
foreach($sortStyles as $styleName=>$style) {
	if($styleName == $method)
		continue;
	$build .= " <a href=\"{$baseURL}$styleName\" class=\"badge badge-secondary\">$style</a>";
}
$build .= "</p>";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
		<title>kgo.to / latin | Word List</title>
	</head>
	<body>
<?php latin_web_getNav(true); ?>
		<main role="main">
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Vocab List
					</h1>
					<p class="lead">Displaying from Chapter <strong><?php echo $start; ?></strong> to Chapter <strong><?php echo $end; ?></strong>.</p>
					<?php echo $build; ?>
				</div>
			</section>
			<div class="py-5 bg-light">
				<div class="container">
					<div class="row">
						<div class="col-12">
							<table class="table table-striped table-bordered table-hover">
							  <thead class="thead-light">
							    <tr>
							      <th scope="col">Latin</th>
							      <th scope="col">English</th>
							      <th scope="col">Part of Speech</th>
							      <th scope="col">Chapter</th>
							      <th scope="col">Accuracy</th>
							    </tr>
							  </thead>
							  <tbody>
								<?php
								
								//build our rows
								$mysql = dbConnection();
								if($method == "grammar")
									$sorting = "`type`, `chapter`";
								else if($method == "chapter")
									$sorting = "chapter";
								else if($method == "lexie")
									$sorting = "english";
								else if($method == "lexil")
									$sorting = "latin";
								else if($method == "missed") {
									$sorting = "accr DESC, occurrences DESC";
								}
								//how we fetch this data depends on our sort method
								//if($sorting == 'missed') {
									$query = "SELECT words.id,words.type,words.latin,words.english,words.gender,words.declension,words.notes,words.conjugation,words.chapter, count(wordSess) as occurrences, COALESCE(SUM(accr),0) as timesRight, COALESCE(SUM(accr),0)/count(wordSess) as accr FROM words LEFT JOIN (SELECT session_events.id as wordSess, word, `right` as accr FROM session_events JOIN sessions ON session_events.sessionIdent = sessions.id WHERE uid = ? GROUP BY session_events.id) AS t ON words.id = t.word WHERE chapter >= ? AND chapter <= ? GROUP BY words.id ORDER BY ".$sorting;
									$sth_chp = $mysql->prepare($query);
									$sth_chp->bind_param('iii', $_SESSION['uid'], $start, $end);
									$sth_chp->execute();
									$sth_chp->bind_result($word_id, $word_type, $word_latin, $word_english, $word_gender, $word_declen, $word_notes, $word_conjug, $word_chp, $word_occr, $word_right, $accr);
									/*
								} else {
									$query = "SELECT id,type,latin,english,gender,declension,notes,conjugation,chapter FROM words WHERE chapter >= ? AND chapter <= ? ORDER BY ".$sorting;
									$sth_chp = $mysql->prepare($query);
									echo $mysql->error;
									$sth_chp->bind_param('ii', $start, $end);
									$sth_chp->execute();
									$sth_chp->bind_result($word_id, $word_type, $word_latin, $word_english, $word_gender, $word_declen, $word_notes, $word_conjug, $word_chp);
								}
								$sth_chp->bind_result($word_id, $word_type, $word_latin, $word_english, $word_gender, $word_declen, $word_notes, $word_conjug, $word_chp);
									*/
								while($sth_chp->fetch()) {
									$word_POS = ucwords($word_type);
									if($word_POS == "Verb" && $word_conjug != 0)
										$word_POS = "Verb ($word_conjug)";
									if($word_POS == "Noun" && $word_declen != 0)
										$word_POS = "Noun ($word_declen)";
									if($word_notes)
										$word_english .= " ($word_notes)";
									if($accr == "")
										$accr = "Unknown";
									else
										$accr = round($accr * 100, 2) . "%";
									echo <<<HTML
								    <tr>
								      <th scope="row">$word_latin</th>
									  <td>$word_english</td>
									  <td>$word_POS</td>
									  <td>$word_chp</td>
									  <td>$accr</td>
								    </tr>
HTML;
								}
								
								?>
							  </tbody>
							</table>
							
						</div>
					</div>
				</div>
			</div>
		</main>
<?php
	latin_web_getFoot();
?>
	</body>
</html>
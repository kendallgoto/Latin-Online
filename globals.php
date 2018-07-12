<?php
session_start();

function dbConnection()
{
    $username = "";
    $password = "";
    $database = "";
    $connhost = "";

    $mysql = new mysqli($connhost, $username, $password, $database);
    return $mysql;
}
function latin_web_generateSelectRange($start, $end, $prefix, $selected=1) {
	for($i = $start; $i <= $end; $i++) {
		$areSel = ($selected == $i) ? " selected" : "";
		echo <<<HTML
												<option value="$i"$areSel>$prefix$i</option>
HTML;
		echo PHP_EOL;
	}
}
function latin_web_getHeader() {
	echo <<<HTML
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
		<meta content="Latin study tools for students learning vocabulary and grammar, developed for Los Altos High School and for Wheelock's Latin." name="description">
		<meta content="Kendall Goto" name="author">
	    <meta name="google-signin-scope" content="profile email">
	    <meta name="google-signin-client_id" content="962755034364-b2kppi91a9qjf4dbcfol4nfttob3hrmv.apps.googleusercontent.com">
		<link href="/latin/assets/fav.ico" rel="icon">
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha256-j+P6EZJVrbXgwSR5Mx+eCS6FvP9Wq27MBRC/ogVriY0=" crossorigin="anonymous">
		<link href="/latin/css/root.css" rel="stylesheet">
		<style>
			body {
				min-width: 700px;
			}
			.historyElement {
				margin-bottom: 15px;
			}
			.historyElement .historyValue p {
				font-size: 3.5rem;
				line-height: 100%;
			}
			.words-right {
				color: #007bff;
			}
			.freeplayBtn {
				height: calc(2.25rem + 2px);
			}
			.flex-spacer {
				width: 5px;
			}
			.navbar-collapse {
				flex-grow: unset;
			}
			@media (min-width: 1200px) {
				.dictfit p.card-text {
					margin-bottom: 34px;
				}
			}
			.abcRioButton {
				width: 100% !important; 
			}
			.ani-s2 {
				animation-delay: 1s;
				-webkit-animation-delay: 1s;
			}
			.ani-s3 {
				animation-delay: 1.1s;
				-webkit-animation-delay: 1.1s;
			}
			.ani-s4 {
				animation-delay: 1.2s;
				-webkit-animation-delay: 1.2s;
			}
			.ani-s5 {
				animation-delay: 1.5s;
				-webkit-animation-delay: 1.5s;
			}
			.card-header {
			    font-size: 15px;
			    padding-top: 5px;
			    padding-bottom: 5px;
			    text-transform: uppercase;
			    font-weight: bold;
			    letter-spacing: 1.3px;
			    color: #6f6f6f;
			}
			.trackedLabel {
				font-size: 12px;
			    font-style: italic;
			    color: #636363;
			    letter-spacing: 1.2px;
			    text-transform: uppercase;
			    position: absolute;
			}
			.trackedLabel.questionLabel {
				left: 10px;
				top: 10px;
			}
			.trackedLabel.centerLabel {
				left: 0;
				right:0;
				text-align: center;
			}
			.trackedLabel.questionTiming {
				right: 10px;
				top: 10px;
			}
			.question-box {
				overflow: hidden;
			}
			.question-box .question {
			    display: flex;
			    justify-content: space-around;
			    font-size: 1.5em;
			}
			.question-box .question .question-part {
				text-shadow: 0px 0px 7px #bdbdbd;
			}
			.question-box .question .question-part.desired {
				color: #666;
			}
			.animate-slow {
				animation-duration: 2s;
				-webkit-animation-duration: 2s;
			}
			.animate-quick {
				animation-duration: 0.5s;
				-webkit-animation-duration: 0.5s;
			}
			.incorrectAnswer {
				color: #ff0500;
			}
			.question-box .question .question-part.desired {
				transition: background-color 0.6s;
				-webkit-transiiton: background-color 0.6s;
			}
			.question-box .question .question-part.desired.correctAnswer {
				color: #007bff;
			}
			.question-box input {
				transition: background-color 0.6s, color 0.6s;
				-webkit-transiiton: background-color 0.6s, color 0.6s;
			}
			.question-box input.correctAnswer {
				background-color: #007bff;
				color: white;
			}
			.question-box input.incorrectAnswer {
				background-color: #ff0500;
				color: white;
			}
			.question-box .question .question-part.desired.incorrectAnswer {
				color: #ff0500;
			}
			#history .card {
				margin-bottom: 12px;
			}
			.hidden {
				display: none !important;
			}
			.network {
				color: #aaa;
				bottom: 1em;
				right: 1em;
				position: fixed;
				display: none;
			}
			.qs {
				box-shadow: 0 0 5px 0 #ccc;
			}
			#wht .input-group {
				width: 100%;
			}
			.inputViewer {
				margin-bottom: 15px;
			}
			#wht .list-group-item {
				text-align: left;
			}
			#wht .list-group-item .badge {
				background: none;
				color: black;
				cursor: pointer;
			}
			#wht .template { display: none;}
			.defList .definition {
				margin: 10px;
			}
			.defList .definition {
				cursor: pointer;
			}
			.uglyfloat {
				font-size: 1.5em;
			    position: fixed;
			    right: 1em;
			    bottom: 1em;
			    background-color: #343a40;
			    border-radius: 50%;
			    width: 1.5em;
			    height: 1.5em;
			    text-align: center;
			    color: rgba(255,255,255,.5);
			    box-shadow: 0px 0px 6px 0px rgba(0,0,0,0.66);
				cursor: pointer;
			}
		</style>
HTML;
	echo PHP_EOL;
}
function latin_web_getFoot() {
	echo <<<HTML
		<footer class="text-muted">
			<div class="container">
				<p>
					&copy; 2018 <a href="https://kgo.to">Kendall Goto</a> | <a href="/latin/privacy">Privacy Policy</a> | <a href="https://github.com/kendallgoto">Source</a> | Made for <a href="http://mvla.net/lahs">Los Altos High School</a>
				</p>
			</div>
		</footer>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script>window.jQuery || document.write('<script src="js/jquery-3.3.1.min.js"><\/script>')</script> 
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js" integrity="sha384-u/bQvRA/1bobcXlcEYpsEdFVK/vJs3+T+nXLsBYJthmdBuavHvAW6UsmqO2Gd/F9" crossorigin="anonymous"></script>
HTML;
	echo PHP_EOL;
}
function latin_web_getNav($returnHome = false) {
	if($returnHome) {
		$return = <<<HTML
        <li class="nav-item">
          <a class="nav-link" href="/latin/">Return to Home</a>
        </li>
HTML;
	}
	if(isset($_SESSION['uid'])) {
		$navStuff = <<<HTML
	    <div class="collapse navbar-collapse">
		    <ul class="navbar-nav">
				$return
		      <!-- <li class="nav-item">
		        <a class="nav-link" href="/latin/settings">Settings</a>
		      </li> -->
		      <li class="nav-item">
		        <a class="nav-link" href="/latin/logout">Logout</a>
		      </li>
		  	</ul>
		</div>
HTML;
	}
	echo <<<HTML
		<header>
			<div class="navbar navbar-dark navbar-expand-lg bg-dark box-shadow">
				<div class="container d-flex justify-content-between">
					<a class="navbar-brand d-flex align-items-center" href="/latin/"><img src="/latin/assets/icon.svg" width="20" height="20" class="mr-2" /> kgo.to/<strong>Latin</strong></a>
					$navStuff
				</div>
			</div>
		</header>
HTML;
	echo PHP_EOL;
}
?>
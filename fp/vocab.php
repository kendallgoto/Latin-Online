<?php
	
require_once('../globals.php');
if(!isset($_SESSION['uid'])) {
	header("Location: /latin/");
	exit();
}
$start = min($_GET['start'], $_GET['end']);
$end = max($_GET['start'], $_GET['end']);
$start = max($start, 1);
$end = min($end, 40);

if(isset($_GET['dir']))
	$type = $_GET['dir'];
else
	$type = "l2e";
//Display methods:
//Chapter, Alphabetical, Grammar, Frequently Missed
$sortStyles = [
	"l2e" => "Latin to English",
	"e2l" => "English to Latin"
];
//figure out our current sorting style
$activeStyle = $sortStyles[$type];
$styleIndex = array_search($type,array_keys($sortStyles));
$baseURL = "/latin/fp/vocab/$start/$end/";
if(!isset($sortStyles[$type])) {
	//this doesn't exist...
	header("Location: {$baseURL}l2e");
	exit();
}
$build = '<p class="lead">Testing <a href="'.$baseURL.$type.'" class="badge badge-primary">'.$activeStyle.'</a> -';
foreach($sortStyles as $styleName=>$style) {
	if($styleName == $type)
		continue;
	$build .= " <a href=\"{$baseURL}$styleName\" class=\"badge badge-secondary\">$style</a>";
}
$build .= "</p>";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.min.css" integrity="sha256-BJ/G+e+y7bQdrYkS2RBTyNfBHpA9IuGaPmf9htub5MQ=" crossorigin="anonymous" />
		<title>kgo.to / latin | FP: Vocabulary</title>
	</head>
	<body>
		<span class="network trackedLabel" id="commError" data-toggle="tooltip" data-placement="top" title="Statistics will not post to servers until network is regained. 1 items remain to be synced."><span class="oi oi-wifi"></span> Connection Lost (<span id="queuedSize">0</span>)</span>
<?php latin_web_getNav(true); ?>
		<div class="modal fade text-center" id="completedTaskModal" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Practice Ended</h5>
		        <button type="button" class="close" data-dismiss="modal">
		          <span>&times;</span>
		        </button>
			  </div>
		      <div class="modal-body">
				<div class="row">
					<div class="col-6">
						<div class="historyElement">
							<div class="historyTitle"><p class="text-muted">Word Accuracy</p></div>
							<div class="historyValue"><p class="lead"><span class="words-right">0</span> / <span class="words-attempted">0</span></p></div>
							<div class="historyUnits"><p class="text-muted">words</p></div>
						</div>
					</div>
					<div class="col-6">
						<div class="historyElement">
							<div class="historyTitle"><p class="text-muted">Time Logged</p></div>
							<div class="historyValue"><p class="lead minutesLogged">0</p></div>
							<div class="historyUnits"><p class="text-muted">minutes</p></div>
						</div>
					</div>
				</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" onclick="window.location.href='/latin/'">Back to Home</button>
		        <button type="button" class="btn btn-primary" onclick="window.location.reload()">Restart</button>
		      </div>
		    </div>
		  </div>
		</div>
		<main role="main">
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Vocabulary Test
					</h1>
					<p class="lead">Displaying from Chapter <strong><?php echo $start; ?></strong> to Chapter <strong><?php echo $end; ?></strong>.</p>
					<?php echo $build; ?>
				</div>
				<div class="container mt-5">
					<div class="card question-box" id="qbox">
						<div class="card-body">
							<span class="trackedLabel questionLabel">Q<span id="questionNumber">1</span> of <span id="totalQuestions">—</span></span>
							<span class="trackedLabel questionTiming"><span class="oi oi-clock"></span> <span id="thisTime">0:00</span> / <span id="totalTime">0:00</span></span>
							<div class="question animated animate-quick slideOutRight my-4">
								<div class="question-part"></div>
							</div>
							<div class="answerDetails">
								<p class="lead text-muted font-italic mb-2 answerDef">&nbsp;</p>
								<p class="initialGuess hidden">You guessed <span class="incorrectAnswer"></span>
							</div>
							<div class="input-group input-group-lg">
								<input type="text" class="form-control" placeholder="Answer" id="guessbox">
									<div class="input-group-append">
									<button class="btn btn-outline-primary" type="button">Next &raquo;</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<div class="py-5 bg-light" style="min-height: 300px;">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-7" id="history">
							<h3 class="text-center text-muted lead">Words will appear here after being completed for review.</h3>
						</div>
					</div>
				</div>
			</div>
		</main>
<?php
	latin_web_getFoot();
?>
	<script>
		var wordbank;
		var wordCount = 0;
		var wordTimer, totalTimer = 0;
		var updatingQuestion = false;
		var wrongOnce = false;
		var activeWord;
		var correctWords = 0, incorrectWords = 0;
		var wordGuesses = []; // each: ["guess": "some correct / incorrect guess", "time": time since start of question (ms)]
		var timerLoop;
		
		var networkQueued = 0;
		
		//https://github.com/daneden/animate.css
		var animationEnd = (function(el) {
		  var animations = {
		    animation: 'animationend',
		    OAnimation: 'oAnimationEnd',
		    MozAnimation: 'mozAnimationEnd',
		    WebkitAnimation: 'webkitAnimationEnd',
		  };

		  for (var t in animations) {
		    if (el.style[t] !== undefined) {
		      return animations[t];
		    }
		  }
		})(document.createElement('div'));
		//end cit.

		function fetchVerbBank(chp_min, chp_max) {
			return $.post('/latin/ajax/fetch', { start: chp_min, end: chp_max, randomized: true, reqSize: 800, type: 'all'});
		}
		//from warmenhoven
	    function checkEnglish(correct, guess) {
	      var answers = correct.toLowerCase().replace(/ ?\([^\)]*\) ?/g, "").replace(/;/g, ",").split(",");
	      var minguess = guess.toLowerCase().replace(/^(a|an|the|to) /, "").replace(/ ?\([^\)]*\) ?/g, "").replace(/!/g, "");
	      for (i = 0; i < answers.length; i++) {
	        var a = answers[i].replace(/!/g, "").replace(/^ */, "").replace(/ *$/, "").replace(/^(a|an|the|to) /, "");
	        if (a.trim() == minguess.trim()) {
				if(a.trim() != "")
	          	  return true;
	        }
	      }		  
	      return false;
	    }

	    function checkLatin(correct, guess) {
	      var answers = correct.replace(/ ?\([^\)]*\) ?/g, "").replace(/;/g, ",").split(",");
			if(correct.toLowerCase() == guess.toLowerCase())
			{
				return true;
			}
			else if(correct.toLowerCase().replace(/,/g, "") == guess.toLowerCase())
			{
				return true;
			}
			
	      if (answers[0] == "-") {
	        return (guess.toLowerCase() == answers[1].replace(/^ +/, "").toLowerCase());
	      } else {
  	        return (guess.toLowerCase() == answers[0].replace(/^ +/, "").toLowerCase());
	      }
		  return false;
	    }
		//end cit.
		
		function answerWord() {
			//check guess
			//if right: 
			//			save data for history 
			//			save data for telemetry 
			//			notify user (correct!) 
			
			var guess = $("#guessbox").val().trim();
			var answer = $("#qbox .question .desired").attr('data-answer').trim();
			var accuracy;
			if("<?php echo $type;?>" == "l2e") {
				accuracy = checkEnglish(answer, guess);
			} else {
				accuracy = checkLatin(answer, guess);
			}
			//add to guess array
			wordGuesses.push({
				"guess": guess,
				"time": (new Date() - wordTimer),
				"right": accuracy
			});
			if(accuracy) { //perform any needed checks here
				//animations:
				//first, clear out our input
				$('#guessbox').addClass('correctAnswer');
				$('#qbox .answerDetails').fadeTo(200, 1);
				$("#qbox .question .desired").addClass("correctAnswer");
				setTimeout(function() {
					//move this word down to the answered set
					if(wrongOnce)
						incorrectWords++;
					else
						correctWords++;
					wrongOnce = false;
					collectTelemetry(activeWord);
					wordGuesses = [];
					dragDown();
				}, 800)
			} else {
				$('#guessbox').addClass('incorrectAnswer');
				$('#qbox .answerDetails').fadeTo(500, 1);
				if(!wrongOnce) {
					if(guess.length == 0) guess = "n/a";
					$('#qbox .answerDetails .initialGuess .incorrectAnswer').text(guess);
				}
				wrongOnce = true;
				setTimeout(function() {
					$('#guessbox').removeClass('incorrectAnswer');
				}, 300);
			}
		}
		function collectTelemetry(word) {
			//note down this word and our metrics with it . . .
			//todo: i could harden this to make it less easy to abuse / cheat stats, but whats the point? don't cheat your own education you're playing yourself.
			//wordid is in word.id
			//list of all of our guesses is in wordGuesses
			var thisWord = word.id;
			var guesses = wordGuesses;
			var endpoint = window.location.pathname;
			var postDate = new Date().getTime() / 1000;
			networkQueued++;
			var cancelLoop = function(word, guess, ep, date) {
					$.post('/latin/ajax/r', {wordId: word, guesses: guess, endpoint: ep, postDate: date, sessionid: "<?php echo uniqid($_SESSION['uid']."_", 1) ?>"} ).fail(function() {
						//theres a network error ...
						$('#commError').fadeIn().attr('data-original-title', "Statistics will not post xto servers until network is regained. "+networkQueued+" item(s) remain to be synced.").tooltip();
						$('#commError #queuedSize').text(networkQueued);
						setTimeout(function() {
							cancelLoop(word, guess, ep, date);
						}, 3000);
					}).done(function(resp) {
						console.log(resp);
						networkQueued--;
						if(networkQueued <= 0) {
							networkQueued = 0;
							$('#commError').fadeOut();
						}
						$('#commError').attr('data-original-title', "Statistics will not post to servers until network is regained. "+networkQueued+" item(s) remain to be synced.").tooltip();
						$('#commError #queuedSize').text(networkQueued);
					});
			}
			cancelLoop(thisWord, guesses, endpoint, postDate);
		}
		function dragDown() {
			var cloned = $('#qbox').clone();
			$('#guessbox').val("").removeClass('correctAnswer');
			$("#qbox .question .desired").removeClass("correctAnswer incorrectAnswer");
			$('#qbox .answerDetails .initialGuess .incorrectAnswer').text("");
			$('#qbox .answerDetails').fadeTo(200, 0);
			$(cloned).removeAttr('id');
			$('.input-group', cloned).remove();
			$('.question', cloned).removeClass("animated animate-quick slideInLeft");
			$('.question-part.desired', cloned).removeClass("animated infinite pulse animate-slow");
			$('.answerDetails', cloned).fadeTo(0, 1);
			$('.answerDetails .initialGuess', cloned).removeClass('hidden');
			if($('.answerDetails .initialGuess .incorrectAnswer', cloned).text().length == 0) {
				$('.answerDetails .initialGuess', cloned).remove();
			}
			$(cloned).addClass('animated fadeInDown');
			$(cloned).prependTo('#history');
			$('#history > h3').fadeOut('slow');
			promptNewWord();
		}
		function promptNewWord() {
			if(updatingQuestion)
				return;
			var word = wordbank.shift();
			if(word == undefined) {
				//we're out of words!
				promptEnd(false);
				return;
			}
			activeWord = word;
			updatingQuestion = true;
			//generate this word prompt
			//prepare our stage	
			if(isNaN(totalTimer)) {
				totalTimer = new Date() - wordTimer;
			}
			else
				totalTimer = totalTimer + (new Date() - wordTimer);	
			if(!$('#qbox .question').hasClass('slideOutRight')) {
				//first word doesn't need to worry about this
				$('#qbox .question').addClass("slideOutRight");
				$('#qbox .question').one(animationEnd, function() {
					//proceed with setup
					populateBox(word);
				});
			}
			else
				populateBox(word);
		}
		function populateBox(word) {
			//get latin + split
			var testWord, testAnswer;
			if("<?php echo $type;?>" == "l2e") {
				testWord = word.latin;
				testAnswer = word.english
			} else {
				testWord = word.english;
				testAnswer = word.latin;
			}		
		    if(word.gender != null && word.gender != "")
				testWord = testWord + " ("+word.gender+".)";
			else if(word.type != "verb")
				testWord = testWord + " ("+word.type+")";
			console.log(word);
			$("#qbox .question .question-part").attr('data-answer', testAnswer).text(testWord).addClass('desired animated infinite pulse animate-slow');
			$('#qbox .answerDetails').fadeTo(500, 0, function(){
				$('.answerDef', this).text(testAnswer + " (chp "+word.chapter+")");
			});
			//animate back onscreen
			$('#qbox .question').removeClass('slideOutRight').addClass("slideInLeft");
			wordTimer = new Date();
			wordCount++;
			updatingQuestion = false;
			updateDisplays(false);
		}
		function toFixed(num, fixed) { //truncate tofixed
		    var re = new RegExp('^-?\\d+(?:\.\\d{0,' + (fixed || -1) + '})?');
		    return num.toString().match(re)[0];
		}
		function updateDisplays(timeUpdate) {
			if(!timeUpdate) {
				$("#questionNumber").text(wordCount);
				$('#totalQuestions').text(wordCount + wordbank.length);
			}
			var wordTime = (new Date()) - wordTimer; // in ms
			if(wordTime / 1000 / 60 > 59) {
				//we've exceeded 1 hr on this - let's end the session (the user is idle)
				promptEnd(true);
			}
			//timestamp:
			$("#thisTime").text(toFixed(wordTime / 1000 / 60, 0) + ":" + toFixed(wordTime / 1000 % 60, 0).padStart(2, "0"));
			if(!isNaN(totalTimer))
				$("#totalTime").text(toFixed(totalTimer / 1000 / 60, 0) + ":" + toFixed(totalTimer / 1000 % 60, 0).padStart(2, "0"));
		}
		function promptEnd(idleEnd) {
			var modal = $('#completedTaskModal');
			$('.words-right', modal).text(correctWords);
			$('.words-attempted', modal).text(wordCount);
			var time = (totalTimer / 1000 / 60).toFixed(0);
			if(time < 1)
				$('.minutesLogged', modal).text("<1");
			else
				$('.minutesLogged', modal).text((totalTimer / 1000 / 60).toFixed(0));
			$('#qbox .question').fadeTo(500, 0);
			$('#qbox .input-group input, #qbox .input-group btn').prop('disabled', true);
			modal.modal('show');
			clearInterval(timerLoop);
		}
		$(function() {
			$('#qbox .input-group btn').click(answerWord);
			$('#qbox .input-group input').keypress(function(e){
				if(e.keyCode==13) answerWord();
			});
			fetchVerbBank(<?php echo $start; ?>, <?php echo $end; ?>).done(function(msg) {
				wordbank = msg.result;
				promptNewWord();
			});
			timerLoop = setInterval(function() {
				updateDisplays(true);
			}, 500);
			
			//make changing badges not mess with history
			$('.badge').each(function() {
				var link = $(this).attr('href');
				$(this).attr('href', '').click(function() {
				    setTimeout(function(){
				        window.location.replace(link);
				    },0)
				})
			})
		})
	</script>
	</body>
</html>
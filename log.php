<?php
	
require_once('./globals.php');
if(!isset($_SESSION['uid'])) {
	header("Location: /latin/");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.min.css" integrity="sha256-BJ/G+e+y7bQdrYkS2RBTyNfBHpA9IuGaPmf9htub5MQ=" crossorigin="anonymous" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mhayes-twentytwenty/1.0.0/css/twentytwenty.min.css" integrity="sha256-2Wze5epdlzF8qLSkEqbpZ54nJACNd5Vk87ymLv9vcqw=" crossorigin="anonymous" />
		<title>kgo.to / latin | Dev Notes</title>
	</head>
	<body>
		<?php latin_web_getNav(true); ?>
		<main role="main">
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Dev Notes
					</h1>
					<p class="lead">A brief chronology of some of the changes happening at kgo.to/<strong>latin</strong>.</p>
				</div>
				<div class="container mt-5">
					<div class="card question-box">
						<div class="card-body text-left">
							<span class="trackedLabel centerLabel">7/12/18</span></span>
							<h3 class="mt-4 text-center">kgo.to/<strong>latin</strong> "Modern" update launches</h3>
							<p class="lead">I've wanted to redesign this site for a very, very long time.</p>
							<p>For awhile, the entire layout had been a basic reskin of the older Warmenhoven site. Finally, however, the redesign is here and live. But many things have changed and I wanted to cover a few of those changes.<p>
								<p>First of all, every single core feature from the old site has been added to the bottom of the user's landing page in the form of freeplay exercises. This selection functions very closely to the old site, but has a flow that is more accessible and less isolated on the page.</p>
								<div class="compareContrast twentytwenty-container qs my-3">
									<img src="old-fp.png">
									<img src="new-fp.png">
								</div>	 
								<p>Next, on actual "tests," I've moved towards favouring a more linear flashcard-esque one-by-one approach. Although I've personally liked being able to have a list of questions and skipping back and forth through them, this approach allows for a lot of advantages. On each word, accuracy and timing can be better studied, and in general the UX becomes a lot more streamlined. Hopefully users can adapt to this without too much trouble.
								<div class="compareContrast twentytwenty-container qs my-3">
									<img src="old-questions.png">
									<img src="new-questions.png">
								</div>
								<p>Finally, sessions. One of the core principles within the new kgo.to/<strong>latin</strong> is the idea of session-based practice. The intent is to make this an impactful, studyable 10-15 minute automatically laid out practice session that reviews old words and studies new words for the week in a few different approaches, simultaneously. A good comparison to make with how this operates is with Membean, except with features exclusively designed and centric to the Latin language and curriculum. I'll write some more about what this is going to actually look like soon.
								<p>And some wrap-up: login was made mandatory. This was basically a necessity with the changes towards sessions, however the login is pretty noninvasive and quick with Google. Additionally, at the end of last year I was given a ton of feedback regarding how to improve the site moving forward - many of these changes are being made right now so thank you very much for all of that! If you have lasting concerns, requests, etc. for the site, please write me an email! I'll take them at <a href="mailto:latin@kgo.to">latin@kgo.to</a> - thanks!
						</div>
					</div>
				</div>
			</section>
		</main>
<?php
	latin_web_getFoot();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mhayes-twentytwenty/1.0.0/js/jquery.event.move.min.js" integrity="sha256-fqXzZX48vO+Q2Uz6Wkh3veStRw4i24PfRgW7twugCxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mhayes-twentytwenty/1.0.0/js/jquery.twentytwenty.min.js" integrity="sha256-D2l9+EHv9C1Jk7FXmUNFnbtmFHowHewFTkbZTnzqdsg=" crossorigin="anonymous"></script>
	<script>
		$(function() {
			$('.compareContrast').twentytwenty();
		})
	</script>
	</body>
</html>
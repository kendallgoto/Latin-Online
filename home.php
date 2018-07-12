<?php
	
require_once('globals.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
		<title>kgo.to / latin - Dashboard</title>
	</head>
	<body>
<?php latin_web_getNav(); ?>
		<main role="main">
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Welcome back, <strong><?php echo $_SESSION['name_given']; ?></strong>.
					</h1>
					<?php
					$mysql = dbConnection();
					$selectQuery = "SELECT id FROM sessions WHERE uid=? AND freeplay=0 LIMIT 1";
					$sth = $mysql->prepare($selectQuery);
					$sth->bind_param('i', $_SESSION['uid']);
					$sth->execute();
					$sth->bind_result($session_id);
					$sth->store_result();
					$sth->fetch();
					if($sth->num_rows()):
					?>
					<p class="lead text-muted">Your last session:</p>
					<div class="row">
						<div class="col-4">
							<div class="historyElement">
								<div class="historyTitle"><p class="text-muted">Last Session</p></div>
								<div class="historyValue"><p class="lead">6</p></div>
								<div class="historyUnits"><p class="text-muted">days ago</p></div>
							</div>
						</div>
						<div class="col-4">
							<div class="historyElement">
								<div class="historyTitle"><p class="text-muted">Word Accuracy</p></div>
								<div class="historyValue"><p class="lead"><span class="words-right">15</span> / <span class="words-attempted">35</span></p></div>
								<div class="historyUnits"><p class="text-muted">words</p></div>
							</div>
						</div>
						<div class="col-4">
							<div class="historyElement">
								<div class="historyTitle"><p class="text-muted">Time Logged</p></div>
								<div class="historyValue"><p class="lead">23</p></div>
								<div class="historyUnits"><p class="text-muted">minutes</p></div>
							</div>
						</div>
					</div>
					<p>
						<a class="btn btn-secondary my-2" href="/latin/history">&lsaquo; Past Sessions</a> <a class="btn btn-primary my-2 disabled" href="#"id="practiceOn"><!-- Practice More &rsaquo; -->Coming Soon</a>
					</p>
				<?php else: ?>
					<p class="lead text-muted">Get started with kgo.to/<strong>Latin</strong> with your first practice session.</p>
					<p>
						<a class="btn btn-primary my-2 disabled" href="#" id="practiceOn">Coming Soon</a>
						<p class="lead">Follow progress <a href="/latin/log/">here</a></p>
					</p>
				<?php endif; ?>
				</div>
			</section>
			<div class="py-5 bg-light">
				<div class="container">
					<h3 class="text-center">Free Practice</h3>
					<p class="lead text-center">Free Practice activities let you quickly practice and use the resources provided to you by kgo.to/<strong>latin</strong>, and do not count for time on your recorded sessions.</p>
					<div class="row">
						<div class="col-12 col-lg-6 col-xl-4"><!-- display card -->
							<div class="card mb-4 box-shadow">
							    <div class="card-header">
							      &#9733; Popular
							    </div>
								<div class="card-body">
									<h5 class="card-title">Build a Vocab List</h5>
									<p class="card-text">Create a basic list of words available here, given a specific chapter range and content description.</p>
									<div class="d-flex">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="display_from">From</label>
											</div>
											<select class="custom-select" id="display_from">
<?php latin_web_generateSelectRange(1, 40, "Chp ");?>
											</select>
										</div>
										<div class="flex-spacer"></div>
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="display_to">To</label>
											</div>
											<select class="custom-select" id="display_to">
<?php latin_web_generateSelectRange(1, 40, "Chp ", 40);?>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="display_words">Ordered By</label>
											</div>
											<!--
											<select class="custom-select" id="display_words">
												<option value="0" selected>All Words</option>
												<option disabled="disabled">-----------</option>
												<option value="noun">All Nouns</option>
												<option value="noun 1">First Declension Nouns</option>
												<option value="noun 2">Second Declension Nouns</option>
												<option value="noun 3">Third Declension Nouns</option>
												<option value="noun 3i">Third Declension I-Stem Nouns</option>
												<option value="noun 4">Fourth Declension Nouns</option>
												<option value="noun 5">Fifth Declension Nouns</option>
												<option value="noun 0">Irregular Nouns</option>
												<option disabled="disabled">-----------</option>
												<option value="verb">All Verbs</option>
												<option value="verb 1">First Conjugation Verbs</option>
												<option value="verb 2">Second Conjugation Verbs</option>
												<option value="verb 3">Third Conjugation Verbs</option>
												<option value="verb 3i">Third Conjugation -io Verbs</option>
												<option value="verb 4">Fourth Conjugation Verbs</option>
												<option value="verb 0">Irregular Verbs</option>
												<option disabled="disabled">-----------</option>
												<option value="adjective">Adjectives</option>
												<option value="adverb">Adverbs</option>
												<option value="cardinal">Cardinals</option>
												<option value="conjunction">Conjunctions</option>
												<option value="enclitic">Enclitics</option>
												<option value="interjection">Interjections</option>
												<option value="ordinal">Ordinals</option>
												<option value="preposition">Prepositions</option>
												<option value="pronoun">Pronouns</option>
											</select>
												-->
											<select class="custom-select" id="display_words">
												<option value="chapter" selected>Chapter</option>
												<option value="lexil">Alphabetical (Latin)</option>
												<option value="lexie">Alphabetical (English)</option>
												<option value="grammar">Grammar Type</option>
												<option value="missed">Frequently Missed</option>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" id="display_go" type="button">Start</button>
									</div>
								</div>
							</div>
						</div> <!-- end of display card .. -->
						<div class="col-12 col-lg-6 col-xl-4">  <!-- Principal Parts-->
							<div class="card mb-4 box-shadow">
							    <div class="card-header">
							      &#9733; Popular
							    </div>
								<div class="card-body">
									<h5 class="card-title">Test Principal Parts</h5>
									<p class="card-text">Builds a test designed to ensure memory of principal parts of verbs in a select chapter range.</p>
									<div class="d-flex">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="pp_from">From</label>
											</div>
											<select class="custom-select" id="pp_from">
<?php latin_web_generateSelectRange(1, 40, "Chp ");?>
											</select>
										</div>
										<div class="flex-spacer"></div>
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="pp_to">To</label>
											</div>
											<select class="custom-select" id="pp_to">
<?php latin_web_generateSelectRange(1, 40, "Chp ", 40);?>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="pp_type">Principal Part</label>
											</div>
											<select class="custom-select" id="pp_type">
												<option value="all" selected>All</option>
												<option value="first">First</option>
												<option value="second">Second</option>
												<option value="third">Third</option>
												<option value="fourth">Fourth</option>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" id="pp_go" type="button">Start</button>
									</div>
								</div>
							</div>
						</div><!-- end of a card .. -->
						<div class="col-12 col-lg-6 col-xl-4">  <!-- Regular Test-->
							<div class="card mb-4 box-shadow">
							    <div class="card-header">
							      &#9733; Popular
							    </div>
								<div class="card-body">
									<h5 class="card-title">Test Word Definitions</h5>
									<p class="card-text">Builds a test designed to ensure memory of word definitions within a selected chapter range.</p>
									<div class="d-flex">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="vocab_from">From</label>
											</div>
											<select class="custom-select" id="vocab_from">
<?php latin_web_generateSelectRange(1, 40, "Chp ");?>
											</select>
										</div>
										<div class="flex-spacer"></div>
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="vocab_to">To</label>
											</div>
											<select class="custom-select" id="vocab_to">
<?php latin_web_generateSelectRange(1, 40, "Chp ", 40);?>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="vocab_dir">Direction</label>
											</div>
											<select class="custom-select" id="vocab_dir">
												<option value="l2e" selected>Latin to English</option>
												<option value="e2l">English to Latin</option>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" id="vocab_go" type="button">Start</button>
									</div>
								</div>
							</div>
						</div><!-- end of a card .. -->
						<div class="col-12 col-lg-6 col-xl-4">  <!-- Number Test-->
							<div class="card mb-4 box-shadow">
								<div class="card-body">
									<h5 class="card-title">Test Number Words</h5>
									<p class="card-text">Create a test of a list of numbers either in Latin or in English.</p>
									<div>
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="num_dir">Direction</label>
											</div>
											<select class="custom-select" id="num_dir">
												<option value="l2e" selected>Latin to English</option>
												<option value="e2l">English to Latin</option>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" id="num_go" type="button">Start</button>
									</div>
								</div>
							</div>
						</div><!-- end of a card .. -->
						<div class="col-12 col-lg-6 col-xl-4">  <!-- Number Test-->
							<div class="card mb-4 box-shadow">
								<div class="card-body">
									<h5 class="card-title">Test Genitive Singulars</h5>
									<p class="card-text">Test your ability to match third declension nouns with their genitive singulars.</p>
									<div class="d-flex">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="gensg_from">From</label>
											</div>
											<select class="custom-select" id="gensg_from">
<?php latin_web_generateSelectRange(7, 40, "Chp ");?>
											</select>
										</div>
										<div class="flex-spacer"></div>
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<label class="input-group-text" for="gensg_to">To</label>
											</div>
											<select class="custom-select" id="gensg_to">
<?php latin_web_generateSelectRange(7, 40, "Chp ", 40);?>
											</select>
										</div>
									</div>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" id="gensg_go" type="button">Start</button>
									</div>
								</div>
							</div>
						</div><!-- end of a card .. -->
						<div class="col-12 col-lg-6 col-xl-4 dictfit">  <!-- Dictionary-->
							<div class="card mb-4 box-shadow">
								<div class="card-body">
									<h5 class="card-title">Comprehensive Dictionary</h5>
									<p class="card-text">Via the work of William Whitaker, this site provides an interface for a comprehensive latin dictionary.</p>
									<div class="mt-1">
										<button class="btn btn-sm btn-outline-primary freeplayBtn w-100" type="button" href="/latin/dictionary">Go Now</button>
									</div>
								</div>
							</div>
						</div><!-- end of a card .. -->
					</div>
				</div>
			</div>
		</main>
<?php
	latin_web_getFoot();
?>
	<script>
		$(function() {
			
			$('#display_go').click(function() {
				window.location.href = "/latin/fp/wordlist/"+$('#display_from').val()+"/"+$('#display_to').val()+"/"+$('#display_words').val();
			});
			$('#pp_go').click(function() {
				window.location.href = "/latin/fp/pp/"+$('#pp_from').val()+"/"+$('#pp_to').val()+"/"+$('#pp_type').val();
			});
			$('#vocab_go').click(function() {
				window.location.href = "/latin/fp/vocab/"+$('#vocab_from').val()+"/"+$('#vocab_to').val()+"/"+$('#vocab_dir').val();
			});		
				
			$('#num_go').click(function() {
				window.location.href = "/latin/fp/num/"+$('#num_dir').val();
			});			
			$('#gensg_go').click(function() {
				window.location.href = "/latin/fp/gensg/"+$('#gensg_from').val()+"/"+$('#gensg_to').val();
			});			
		})
		
	</script>
	</body>
</html>
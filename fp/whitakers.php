<?php
	
require_once('../globals.php');
if(!isset($_SESSION['uid'])) {
	header("Location: /latin/");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
		<title>kgo.to / latin | Whitaker's Words</title>
	</head>
	<body>
<?php latin_web_getNav(true); ?>
		<main role="main" id="wht">
			<div class="uglyfloat" data-toggle="tooltip" data-html="true" title="<strong>This dictionary is hard to read!</strong><br>Yes, it is. The original program to compute Latin entries was created in 1993. Efforts are being made to make this easier to read, so bear with me here!">
				?
			</div>
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Comprehensive Dictionary
					</h1>
					<p class="lead">Non-Wheelock words are provided definitions based on a much more sophisticated dictionary.</p>
				</div>
			</section>
			<div class="py-5 bg-light">
				<div class="container">
					<div class="row">
						<div class="col-12">
				  		  <div class="inputViewer">
				  			<div class="input-group input-group-lg">
				  			  <input type="text" class="form-control" placeholder="Enter a word to search for, then press enter.">
				  			</div>
				  		  </div>
						  <div class="defList" id="defAdd" style="font-family: monospaced, mono; text-align: left; font-size: 2rem">
							  <div class="template definition" id="templateDef">
								<div class="card question-box">
									<div class="card-body">
										<div class="">
											<div class="question-part" id="searched"></div>
										</div>
										<div class="answerDetails">
											<p class="lead font-italic mb-0 answerDef" id="resp"></p>
										</div>
									</div>
								</div>
							  </div>
						  </div>
						</div>
					</div>
				</div>
			</div>
		</main>
<?php
	latin_web_getFoot();
?>
	<script>
		var inProg = 0;
		$(function() {
			$('.uglyfloat').tooltip();
			$('.uglyfloat').click(function() {
				$('.uglyfloat').tooltip('show');
			})
			$('input').keydown(function(e) {
				if (e.which == 13) {
					var value = $('input').val().toLowerCase();
					$('input').select();
					value = value.replace(/[^a-z]/g,'').trim();
					if(value.length == 0) return;
					if(inProg) return;
					inProg = 1;
					//make query!
					$.get("https://aws.kgo.to/reg/process.php?word="+value).done(function(res) {
						inProg = 0;
						res = res.replace(/<(?!br\s*\/?)[^>]+>/g, '');
						var tempDef = $('#templateDef').clone();
						$(tempDef).removeAttr('id').removeClass('template');
						$('#resp', tempDef).html(res).removeAttr('id');
						$('#searched', tempDef).text(value).removeAttr('id');
						$(tempDef).prependTo('#defAdd');
					});
				}
			});
		});
	</script>
	</body>
</html>
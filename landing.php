<?php
	
require_once('globals.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php latin_web_getHeader(); ?>
		<title>kgo.to / latin</title>
	</head>
	<body>
<?php latin_web_getNav(); ?>
		<main role="main">
			<section class="jumbotron text-center">
				<div class="container">
					<h1 class="jumbotron-heading">
						Latin, <strong class="d-inline-block animated fadeInUp">Online.</strong>
					</h1>
					<p class="lead">kgo.to/<strong>latin</strong> provides unique tools to users, designed with Latin and education at its core.</p>
					<div class="row">
						<div class="col-md-6 animated fadeInLeft ani-s4">
							<img src="assets/pp1.png" width="100%" class="qs">
						</div>
						<div class="col-md-6 animated fadeInLeft ani-s2">
							<img src="assets/pp2.png" width="100%" class="qs">
						</div>
					</div>
					<p class="lead mt-2 animated fadeInUp ani-s2">Log in and get started with a session-based approach to constant practice of Latin vocabulary and grammar.</p>
					<p>
   					 	<div id="latin-signin" class="animated fadeIn ani-s5"></div>
					</p>
				</div>
			</section>
		</main>
<?php
	latin_web_getFoot();
?>
		<script>
		    function onSuccess(googleUser) {
			  var id_token = googleUser.getAuthResponse().id_token;
			  $.post('/latin/ajax/login', {'idtok': id_token}, function(res) {
				  window.location.reload(); 
			  });
		    }
		    function onFailure(error) {
		      console.log(error);
		    }
			function renderButton() {
		        gapi.signin2.render('latin-signin', {
		          'scope': 'email profile openid',
		          'width': 1200,
		          'height': 50,
		          'longtitle': true,
		          'theme': 'dark',
		          'onsuccess': onSuccess,
		          'onfailure': onFailure
		        });
				
			}
		</script>
	    <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
	</body>
</html>
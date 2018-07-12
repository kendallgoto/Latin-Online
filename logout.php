<?php
require_once('globals.php');

//log us out
session_destroy();
session_unset();

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
						Logging out...</strong>
					</h1>
				 	<div id="latin-signin" class="d-none"></div>
				</div>
			</section>
		</main>
<?php latin_web_getFoot(); ?>
		<script>
		    function onSuccess() {
		      var auth2 = gapi.auth2.getAuthInstance();
		      auth2.signOut().then(function () {
				  window.location.href = "/latin/";
		      });
		    }
			
			function renderButton() {
		        gapi.signin2.render('latin-signin', {
		          'scope': 'email profile openid',
		          'width': 0,
		          'height': 0,
		          'onsuccess': onSuccess
		        });
				
			}
		</script>
	    <script src="https://apis.google.com/js/platform.js?onload=renderButton"></script>
	</body>
</html>
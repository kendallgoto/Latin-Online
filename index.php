<?php

//crossroads handler
require 'globals.php';

if(isset($_SESSION['uid']))
	require 'home.php';
else
	require 'landing.php';
	
?>
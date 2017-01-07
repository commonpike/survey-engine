<?php

	// this trik allows you to use
	// the same survey for several clients:
	// set up a category called 'client'
	// in survey.json and enter the details here
	
	// read config 
	require('config.php');
	
	$config->filtercat='client';
	$config->filterval=1;
	include('survey.php');
	
?>
	
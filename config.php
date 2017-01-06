<?php
	class Config { }
	$config = new Config();
	
	// as long as testing is on, all results
	// are entered as bogus, and bogus will be
	// included in the output
	$config->testing 		= true;
	$config->mysqldb 		= 'surveyengine';
	$config->mysqluser	= 'xxxx';
	$config->mysqlpass	= 'xxxx';
	
	// limit ajax submissions 
	$config->cors		= "*"; // Access-Control-Allow-Origin

	// pass is required on some functions
	$config->uipass		= 'xxxx';
	
	date_default_timezone_set('GMT') 
	
?>
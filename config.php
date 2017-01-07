<?php
	class Config { }
	$config = new Config();
	
	// as long as testing is on, all results
	// are entered as bogus, and bogus will be
	// included in the output
	$config->testing 		= true;
	$config->mysqldb 		= 'mk2015';
	$config->mysqluser	= 'prodemos';
	$config->mysqlpass	= '&vaderland!';
	
	$config->filtercat	= 'client';
	$config->filterval	= '1';
	
	// limit ajax submissions 
	$config->cors		= "*"; // Access-Control-Allow-Origin

	// pass is required on some functions
	$config->uipass		= 'R0CK0';
	
	date_default_timezone_set('GMT') 
	
?>
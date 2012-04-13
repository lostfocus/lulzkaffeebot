<?php
	require_once('config.php');
	require_once('functions.php');
	
	var_dump(date("r"));
	$now = $beforelunch = mktime(12,01);
	var_dump(date("r",$now));
	$nct = calculateNextCoffeeTweet($now);
	var_dump(date("r",$nct));
<?php
	require_once('config.php');
	require_once('Twitter.php');
	require_once('functions.php');



	$nct_file = 'nextCoffeeTweet.txt';
	$nct = trim(file_get_contents(dirname(__FILE__) . '/'.$nct_file));
	$now = time();
	if($nct == ''){
		file_put_contents(dirname(__FILE__) . '/'.$nct_file, calculateNextCoffeeTweet($now));
		die();
	}
	if((int)$nct < $now){
		$twitter = new Twitter();
		$twitter->setCredentials($config['username'], $config['password']);
		file_put_contents(dirname(__FILE__) . '/'.$nct_file, calculateNextCoffeeTweet($now));
		$tweet = sprintf('O HAI! Um %s Uhr ist es Zeit für die nächste Kaffeepause!',date('H:i',time()+300));
		$twitter->update($tweet, 'xml', array('lat' => $config['lat'],'long' => $config['long']));
	}
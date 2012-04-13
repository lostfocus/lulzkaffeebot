<?php
function calculateNextCoffeeTweet($now = false){
	if(!$now) $now = time();
	$timearea = false;
	$nextcoffeetweet = false;
	$beforework = mktime(9,30);
	$beforelunch = mktime(11,15);
	$afterlunch = mktime(14,00);
	$evening = mktime(17,30);
	if($now < $beforework){
		$timearea = 'beforework';
	} elseif($now < $beforelunch){
		$timearea = 'am';
	} elseif($now < $afterlunch){
		$timearea = 'lunch';
	} elseif($now < $evening){
		$timearea = 'pm';
	} else {
		$timearea = 'evening';
	}
	switch($timearea){
		case 'beforework':
			$nextcoffeetweet = mktime(9,30);
			break;
		case 'am':
			$nextcoffeetweet = $now + ((60 + (rand(0,20)-10)) * 60);
			if($nextcoffeetweet > $beforelunch){
				$nextcoffeetweet = $afterlunch + rand(0,20) * 60;
			}
			break;
		case 'lunch':
			$nextcoffeetweet = $now + ((60 + (rand(0,20)-10)) * 60);
			if($nextcoffeetweet < $afterlunch){
				$nextcoffeetweet = $afterlunch + rand(0,20) * 60;
			}
			break;
		case 'pm';
			$nextcoffeetweet = $now + ((90 + (rand(0,40)-20)) * 60);
			if($nextcoffeetweet > $evening){
				$nextcoffeetweet = (mktime(9,30,00,date("n",time()+86400),date("j",time()+86400),date("Y",time()+86400)))+ (rand(0,20)-10) * 60;
			}
			break;
		default:
			$nextcoffeetweet = (mktime(9,30,00,date("n",time()+86400),date("j",time()+86400),date("Y",time()+86400)))+ (rand(0,20)-10) * 60;
			break;
	}
	if(date("w",$nextcoffeetweet) == "0") $nextcoffeetweet = $nextcoffeetweet + 86400;
	if(date("w",$nextcoffeetweet) == "6") $nextcoffeetweet = $nextcoffeetweet + 172800;
	return $nextcoffeetweet;
}
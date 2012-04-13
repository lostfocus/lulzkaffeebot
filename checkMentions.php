<?php

require_once('config.php');
require_once('functions.php');
require_once('Twitter.php');

$twitter = new Twitter();
$twitter->setCredentials($config['username'], $config['password']);

$next_tweet = file_get_contents(dirname(__FILE__) . '/nextCoffeeTweet.txt');
$since_id   = file_get_contents(dirname(__FILE__) . '/lastMention.txt');
$mentions   = $twitter->getMentions($since_id);
if (!is_array($mentions)) {
	die();
}
foreach ($mentions as $mention) {
	if ((string) $mention->id > $since_id) {
		$since_id = (string) $mention->id;
	}
	if (strpos(strtolower($mention->text), 'wann') !== false || strpos(strtolower($mention->text), 'when') !== false) {
		$time = $next_tweet - time();
		if ($time <= 0) {
			$next_tweet = calculateNextCoffeeTweet();
			$time = $next_tweet - time();
		}
		if (date('w', $next_tweet) == 0 || date('w', $next_tweet) == 6) {
			$message = 'erst wieder am Montag';
		} else if (date('d', $next_tweet) != date('d')) {
			if (date('w', $next_tweet) == 1 && date('w') == 5) {
				$message = 'am Montag';
			} else if ($next_tweet - time() < 24 * 3600) {
				$message = 'morgen';
			} else {
				$message = 'in ' . ceil(($next_tweet - time()) / 24 * 3600) . ' Tagen';
			}
		} else if ($time <= 15 * 60) {
			$message = 'bald';
		} else if ($time <= 45 * 60) {
			$message = 'in ungefähr einer halben Stunde';
		} else if ($time <= 90 * 60) {
			$message = 'in ungefähr einer Stunde';
		} else if ($time <= 120 * 60) {
			$message = 'in 1½ Stunden';
		} else {
			$message = 'in ' . ceil($time / 3600) . ' Stunden';
		}
	} else {
		$message = 'lulz lulz lulz!';
	}
	$tweet = '@' . $mention->user->screen_name . ' ' . $message;
	$twitter->update($tweet, 'xml', array('in_reply_to_status_id' => $mention->id, 'lat' => $config['lat'], 'long' => $config['long']));
}
file_put_contents(dirname(__FILE__) . '/lastMention.txt', $since_id);

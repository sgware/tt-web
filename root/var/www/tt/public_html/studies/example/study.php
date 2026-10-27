<?php
include($_SERVER['tt_library']);

// Load the 'example' study and user data.
$study = Study::load('example');
$user = $study->user();

// Create 5 step objects corresponding to the 5 steps in the study.
$steps = array();
for($i = 0; $i < 5; $i++)
	$steps[$i] = array('url' => '/play/?study=example&user=' . $user->id, 'status' => 'step');

// Step 1: Play the tutorial as the player.
$steps[0]['url'] .= '&world=tutorial&role=PLAYER&partner=test';
if($user->getSession(world: 'tutorial', role: 'PLAYER', complete: true))
	$steps[0]['status'] = 'past step';

// Step 2: Play the tutorial as the game master.
$steps[1]['url'] .= '&world=tutorial&role=GAME_MASTER&partner=test';
if($user->getSession(world: 'tutorial', role: 'GAME_MASTER', complete: true)) {
	$steps[1]['status'] = 'past step';
	// If the user has completed the tutorial, assign them to the control or
	// experimental group.
	if(!isset($user->group)) {
		$study->distribute($user, 'control', 'experimental');
		$study->save();
		$user->save();
	}
	// If this user is in the control group, play The Crown as the player first.
	if($user->group == 'control') {
		$steps[2]['url'] .= '&world=crown&role=PLAYER&partner=test';
		$steps[3]['url'] .= '&world=crown&role=GAME_MASTER&partner=test';
	}
	// If this user is in the experimental group, play as the game master first.
	else {
		$steps[2]['url'] .= '&world=crown&role=GAME_MASTER&partner=test';
		$steps[3]['url'] .= '&world=crown&role=PLAYER&partner=test';
	}
}

// Get the last session this user completed.
$last = $user->getSession(world: 'crown', complete: true);

// If the last thing this user did was play The Crown and they have submitted
// their enjoyment, record their rating.
if($last and !isset($last['enjoyment']) and isset($_GET['enjoyment'])) {
	$user->sessions[$last['id']]['enjoyment'] = $_GET['enjoyment'];
	$user->save();
}

// If the last thing this user did was play The Crown and they have submitted
// their preference, record their answer.
if($last and !isset($user->preference) and isset($_GET['preference'])) {
	$user->preference = $_GET['preference'];
	$user->save();
}

// Step 3: Play The Crown the first time and rate enjoyment.
if($last and isset($last['enjoyment']))
	$steps[2]['status'] = 'past step';

// Step 4: Play The Crown a second time, rate enjoyment, and choose a
// preference.
if($last and isset($user->preference))
	$steps[3]['status'] = 'past step';

// Mark current and future steps.
$step = -1;
for($i = 0; $i < count($steps); $i++) {
	if($steps[$i]['status'] == 'step') {
		if($step == -1) {
			$step = $i;
			$steps[$i]['status'] = 'current step';
		}
		else
			$steps[$i]['status'] = 'future step';
	}
}

?>
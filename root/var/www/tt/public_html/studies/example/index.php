<?php
include('study.php');

// Redirect the user to the correct page based on their progress.
try {
	// If this user has played The Crown but has not rated their last session,
	// send them to the survey.
	$last = $user->getSession(world: 'crown', complete: true);
	if($last and !isset($last['enjoyment']))
		header("Location: /studies/example/survey.php?user=" . $user->id);
	// Otherwise, send them to the instructions page.
	else
		header("Location: /studies/example/instructions.php?user=" . $user->id);
}
// If an error occurs, redirect to the error page.
catch(Exception $exception) {
	header("Location: /error.php?message=" . $exception->getMessage());
}

?>
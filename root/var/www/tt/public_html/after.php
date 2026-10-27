<?php
include($_SERVER['tt_library']);
// If the user came here from a study...
if(isset($_GET['study'])) {
	try {
		// Load the study and user data.
		$study = Study::load($_GET['study']);
		$user = $study->user();
		// If a session ID is provided, save the session to the user's log.
		if(isset($_GET['session'])) {
			$session = read_session_metadata($_GET['session'], $_GET['role'] ?? null);
			$user->sessions[$session['id']] = $session;
			$user->save();
		}
		// Redirect the user to the study's page.
		header('Location: /studies/' . $study->id . '?user='. $user->id);
		exit();
	}
	// If an error occurs, redirect to the error page.
	catch(Exception $exception) {
		header("Location: /error.php?message=" . $exception->getMessage());
	}
}
?><!DOCTYPE html>
<html>
	<head>
		<title>Thank You for Playing</title>
		<?php include($_SERVER['tt_head']); ?>
	</head>
	<body>
		<?php include($_SERVER['tt_header']); ?>
		<main>
			<h1>Thank you for playing!</h1>
			<p>The choices you made and questions your answered have been recorded, but we do not log identifying information like your name, or email address. Please read our <a href="/consent.php">consent page</a> for all the details. Thank you for contributing valuable data to the science of interactive storytelling.</p>
			<p>Would you like to <a href="/play">play again</a>?</p>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>
<!DOCTYPE html>
<html>
	<head>
		<title>Error</title>
		<?php include($_SERVER['tt_head']); ?>
	</head>
	<body>
		<?php include($_SERVER['tt_header']); ?>
		<main>
			<h1>Error</h1>
			<p>We're sorry, but an error has occurred<?php
$message = $_GET['message'] ?? null;
if($message != null)
	echo(": <span style=\"color:red\">" . html_entity_decode($message) . '</span>');
else
	echo('.');
?></p>
			<p>Please contact the administrator of this website for further support.</p>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>
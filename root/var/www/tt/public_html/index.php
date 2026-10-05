<!DOCTYPE html>
<html>
	<head>
		<title>Tandem Tales</title>
		<?php include($_SERVER['DOCUMENT_ROOT'].'/../template/head.php'); ?>
	</head>
	<body>
		<?php include($_SERVER['DOCUMENT_ROOT'].'/../template/nav.php'); ?>
		<main>
<?php

// Get environment variables.
$world = getenv('play_world') ?? '';
$role = getenv('play_role') ?? '';
if($role == '' && getenv('agent_role') !== false) {
	if(getenv('agent_role') == 'PLAYER')
		$role = 'GAME_MASTER';
	else if(getenv('agent_role') == 'GAME_MASTER')
		$role = 'PLAYER';
}
$partner = getenv('play_partner') ?? '';

// Generate quick play link.
if($world != '' || $role != '' || $partner != '') {
	$url = "https://localhost/play/?world=$world&role=$role&partner=$partner";
	$text = 'Click here to play ';
	$text .= $world == '' ? 'in any world ' : "in world \"$world\" ";
	$text .= $role == '' ? 'as either role ' : "as \"$role\" ";
	$text .= $partner == '' ? 'with any partner' : "with partner \"$partner\"";
	$text .= '.';
	echo("\t\t\t<p><a href=\"$url\">$text</a></p>\n");
}

?>
			<p><a href="https://localhost/play">Click here to choose your own game settings.</a></p>
		</main>
		<?php include($_SERVER['DOCUMENT_ROOT'].'/../template/footer.php'); ?>
	</body>
</html>
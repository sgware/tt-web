<!DOCTYPE html>
<html>
	<head>
		<title>Tandem Tales</title>
		<?php include($_SERVER['tt_head']); ?>
	</head>
	<body>
		<?php include($_SERVER['tt_header']); ?>
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
	$text = 'Click here to play ';
	$url = "https://localhost/play/?";
	$text .= $world == '' ? 'in any world ' : "in world \"$world\" ";
	if($world != '')
		$url .= 'world=' . $world . '&';
	$text .= $role == '' ? 'as either role ' : "as \"$role\" ";
	if($role != '')
		$url .= 'role=' . $role . '&';
	$text .= $partner == '' ? 'with any partner' : "with partner \"$partner\"";
	if($partner != '')
		$url .= 'partner=' . $partner . '&';
	$text .= '.';
	echo("\t\t\t<p><a href=\"$url\">$text</a></p>\n");
}

?>
			<p><a href="https://localhost/play">Click here to choose your own game settings.</a></p>
			<p><a href="https://localhost/studies/example">Click here to take part in an example study.</a></p>
			<p><a href="https://localhost/admin">Click here to see the administator tools.</a> (username: <tt>admin</tt>, password: <tt>admin</tt>)</p>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>
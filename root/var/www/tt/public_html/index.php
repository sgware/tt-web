<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Tandem Tales Web Server</title>
	</head>
	<body>
		<h1>Tandem Tales Web Server</h1>
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
$url = "https://localhost/play/?world=$world&role=$role&partner=$partner";
$text = 'Check here to play ';
$text .= $world == '' ? 'in any world ' : "in world \"$world\" ";
$text .= $role == '' ? 'as either role ' : "as \"$role\" ";
$text .= $partner == '' ? 'with any partner' : "with partner \"$partner\"";
$text .= '.';
echo("\t\t<p><a href=\"$url\">$text</a></p>\n");
?>
		<p><a href="https://localhost/play">Click here to choose your own game settings.</a></p>
		<p>System Information: <?php echo(php_uname()); ?><p>
	</body>
</html>
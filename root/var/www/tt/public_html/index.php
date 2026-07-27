<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Tandem Tales Web Server</title>
	</head>
	<body>
		<h1>Tandem Tales Web Server</h1>
		<p><a href="https://localhost/play">Click here to play.</a></p>
<?php
$world = getenv('world');
$role = getenv('role');
$partner = getenv('partner');
if($world != null || $role != null || $partner != null) {
	$url = 'https://localhost/play/?password=false';
	$text = 'Check here to play';
	if($world != null) {
		$url .= "&world=$world";
		$text .= " in world \"$world\"";
	}
	if($role != null) {
		$url .= "&role=$role";
		$text .= " as \"$role\"";
	}
	if($partner != null) {
		$url .= "&partner=$partner";
		$text .= " with partner \"$partner\"";
	}
	$text .= '.';
	echo("\t\t<p><a href=\"$url\">$text</a></p>\n");
}
?>
		<p>System Information: <?php echo(php_uname()); ?><p>
	</body>
</html>
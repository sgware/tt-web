<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Tandem Tales Web Server</title>
	</head>
	<body>
		<h1>Tandem Tales Web Server</h1>
<?php
$world = getenv('world') ?? '';
$role = getenv('role') ?? '';
$partner = getenv('partner') ?? '';
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
<?php include('admin.php'); ?><!DOCTYPE html>
<html>
	<head>
		<title>Tandem Tales Administrator Tools</title>
		<?php include($_SERVER['tt_head']); ?>
	</head>
	<body>
		<?php include($_SERVER['tt_header'] ?? 'unknown'); ?>
		<main>
			<h1>Server Status</h1>
<?php if($status === null) echo('<p>The server is not running or the status file could not be read.</p>'); else { ?>
			<p>Server Version: <?php echo($status['version']); ?></p>
			<h2>Running Sessions</h2>
			<?php echo($playing_html); ?>
			<h2>Waiting Agents</h2>
			<?php echo($waiting_html); ?>
<?php } ?>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>
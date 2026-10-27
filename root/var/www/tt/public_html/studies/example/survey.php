<?php include('study.php'); ?><!DOCTYPE html>
<html>
	<head>
		<title>Survey</title>
		<?php include($_SERVER['tt_head']); ?>
	</head>
	<body>
		<main>
			<h1>Survey</h1>
			<p>Please answer the following questions.</p>
			<form action="/studies/example/index.php" method="GET">
				<input type="hidden" name="user" value="<?php echo($user->id); ?>">
				<fieldset>
					<legend>I enjoyed the game of The Crown that I jusy played.</legend>
					<label for="enjoyment-sd">
						<input type="radio" id="enjoyment-sd" name="enjoyment" value="strongly-disagree" />
						Strongly disagree
					</label>
					<label for="enjoyment-d">
						<input type="radio" id="enjoyment-d" name="enjoyment" value="disagree" />
						Disagree
					</label>
					<label for="enjoyment-n">
						<input type="radio" id="enjoyment-n" name="enjoyment" value="neutral" />
						Neither agree nor disagree
					</label>
					<label for="enjoyment-a">
						<input type="radio" id="enjoyment-a" name="enjoyment" value="agree" />
						Agree
					</label>
					<label for="enjoyment-sa">
						<input type="radio" id="enjoyment-sa" name="enjoyment" value="strongly-agree" />
						Strongly agree
					</label>
				</fieldset>
<?php if(count($user->getSessions(world: 'crown', complete: true)) > 1) { ?>
				<fieldset>
					<legend>Which version of The Crown did you prefer?</legend>
					<label for="preference-first">
						<input type="radio" id="preference-first" name="preference" value="<?php echo($user->group == 'control' ? 'player' : 'gm'); ?>" />
						I had more fun the first time I played The Crown.
					</label>
					<label for="preference-second">
						<input type="radio" id="preference-second" name="preference" value="<?php echo($user->group == 'control' ? 'gm' : 'player'); ?>" />
						I had more fun the second time I played The Crown.
					</label>
				</fieldset>
<?php } ?>
				<input type="submit" value="Submit"/>
			</form>
		</main>
		<?php include($_SERVER['tt_footer']); ?>
	</body>
</html>